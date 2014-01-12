<?php

namespace Wps\Media\Import;

use Wps\Media\Media;
use Wps\Media\Album;
use Wps\Security\MediaSecurity;
use Wps\Util\FileSystem;

use Smvc\Core\AbstractContainerAware;
use Smvc\Core\Container;
use Smvc\Model\Persistence\DaoInterface;
use Smvc\Security\Account;

/**
 * Default importer implementat that must be used by any other
 */
class DefaultImporter extends AbstractContainerAware
{
    /**
     * @var DaoInterface
     */
    private $mediaDao;

    /**
     * @var DaoInterface
     */
    private $albumDao;

    /**
     * Root working directory
     *
     * @var string
     */
    private $workingDirectory;

    /**
     * Destination directory
     *
     * @var string
     */
    private $destination;

    /**
     * @var Account
     */
    private $owner;

    /**
     * Default constructor
     *
     * @param Importer $importer
     *   Importer instance
     */
    public function __construct(Account $owner)
    {
        $this->owner = $owner;
    }

    public function setContainer(Container $container)
    {
        parent::setContainer($container);

        $config = $container->getConfig();

        // Ensure the destination directory
        $path = $config['directory/public'];
        FileSystem::ensureDirectory($path, true, true);
        $this->destination = $path;

        // Ensure the working directory
        $path = FileSystem::pathJoin($config['directory/upload'], $this->getOwner()->getId());
        FileSystem::ensureDirectory($path, true, true);
        $this->workingDirectory = $path;

        // Get the DAO's
        $this->albumDao = $container->getDao("album");
        $this->mediaDao = $container->getDao("media");
    }

    /**
     * Get destination directory
     *
     * @return string
     *   Root working directory
     */
    final public function getDestinationDirectory()
    {
        return $this->destination;
    }

    /**
     * Get working directory
     *
     * @return string
     *   Root working directory
     */
    final public function getWorkingDirectory()
    {
        return $this->workingDirectory;
    }

    /**
     * Get owner account
     *
     * @return Account
     */
    final public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Create media path relative to public files directory
     *
     * @param string $path
     *
     * @return string
     */
    final protected function createRealPath($path)
    {
        // Keep the file name ext
        if ($pos = strrpos($path, '.')) {
            $ext = substr($path, $pos);
        } else {
            $ext = '';
        }

        // Use SHA512 because we wont URL to be long enough
        $privateKey = $this->owner->getPrivateKey();
        $siteKey = '';
        $path = base64_encode(hash_hmac('sha512', $path, $privateKey . $siteKey . "wps", true));

        return trim(preg_replace('/[^a-zA-Z0-9]{1,}/', '/', $path), "/") . $ext;
    }

    /**
     * Find or create album for the given media
     *
     * @param Media $media
     *
     * @return Album
     */
    protected function findAlbum(Media $media)
    {
        // We should definitely create the album if possible
        $album = $this->albumDao->loadFirst(array(
            'path' => $media->getPath(),
        ));

        if (!$album) {
            $album = new Album();
            $album->fromArray(array(
                'accountId'   => $this->getOwner()->getId(),
                'path'        => $media->getPath(),
            ));
        }

        // Update or insert, when updating this will change the
        // update date
        $this->albumDao->save($album);

        return $album;
    }

    /**
     * Import single media
     *
     * @param Media $new
     * @param Album $album
     */
    final public function import(Media $new, Album $album = null)
    {
        if (null === $album) {
            $album = $this->findAlbum($new);
        }

        $owner = $this->getOwner();

        $new->fromArray(array(
            'albumId'   => $album->getId(),
            'accountId' => $owner->getId(),
        ));

        // Attempt loading by filename and path for graceful merge
        $existing = $this->mediaDao->loadFirst(array(
            'path' => $new->getPath(),
            'name' => $new->getName(),
        ));

        $toUpdate = null;

        if (empty($existing)) {
            // Insert
        } else {
            // If we got something ensure the hash
            if ($new->getMd5Hash() === $existing->getMd5Hash()) {
                // @todo Log this?
                return;
            } else {
                // Update
                $toUpdate = $existing;
            }
        }

        // If nothing attempt with filename only then check for MD5
        if ($toUpdate) {
            // @todo Should we update a photo in another album?
            // @todo Or just warn the user there is potential duplicates?
            // Sounds dumb, right?

            // @todo
            // Copy the file over the the existing one and update
            // existing instance internals

            $this->mediaDao->save($existing);

        } else {

            // Copy the new file
            $filepath = $new->getPathName();
            $realPath = $this->createRealPath($filepath);
            $new->fromArray(array('realPath' => $realPath));

            // Get physical target (needs the data dir)
            $source = FileSystem::pathJoin($this->getWorkingDirectory(), $filepath);
            $target = FileSystem::pathJoin($this->getDestinationDirectory(), $realPath);
            // Everything is relative find the real file path and create it
            // if necessary
            FileSystem::ensureDirectory(dirname($target), true, true);

            // Then copy everything
            if (!copy($source, $target)) {
                throw new \RuntimeException("Could not copy file");
            }
            // Ok we're good to go update the instance
            $new->fromArray(array('realPath' => $realPath));

            $this->mediaDao->save($new);
        }
    }
}