<?php

namespace Wps\Media\Import;

use Wps\Media\Media;
use Wps\Media\Album;
use Wps\Security\MediaSecurity;
use Wps\Util\FileSystem;

use Smvc\Model\Persistence\DaoInterface;
use Smvc\Security\Account;

/**
 * Default importer implementat that must be used by any other
 */
class DefaultImporter
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
     * @param string $workingDirectory
     *   Root working directory free of user information
     * @param string $destination
     *   Public files destination (usually public/data)
     */
    public function __construct(
        DaoInterface $mediaDao,
        DaoInterface $albumDao,
        Account $owner,
        $workingDirectory = null,
        $destination      = null)
    {
        $this->mediaDao = $mediaDao;
        $this->albumDao = $albumDao;
        $this->owner = $owner;

        if (null !== $destination) {
            $this->setDestinationDirectory($destination);
        }
        if (null !== $workingDirectory) {
            $this->setWorkingDirectory($workingDirectory);
        }
    }

    /**
     * Set destination directory
     *
     * @param string $workingDirectory
     *   Root working directory
     */
    public function setDestinationDirectory($destination)
    {
        FileSystem::ensureDirectory($destination, true, true);

        $this->destination = $destination;
    }

    /**
     * Get destination directory
     *
     * @return string
     *   Root working directory
     */
    public function getDestinationDirectory()
    {
        return $this->destination;
    }

    /**
     * Set working directory
     *
     * @param string $workingDirectory
     *   Root working directory
     */
    public function setWorkingDirectory($workingDirectory)
    {
        FileSystem::ensureDirectory($workingDirectory);

        $this->workingDirectory = $workingDirectory;
    }

    /**
     * Get working directory
     *
     * @return string
     *   Root working directory
     */
    public function getWorkingDirectory()
    {
        return $this->workingDirectory;
    }

    /**
     * Get owner account
     *
     * @return Account
     */
    protected function getOwner()
    {
        return $this->owner;
    }

    /**
     * Get full path by prepending the working directory
     *
     * @param string $path
     */
    protected function getFullPath($path)
    {
        if (null === $this->workingDirectory) {
            return $path;
        }

        return FileSystem::pathJoin($this->workingDirectory, $path);
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
    public function import(Media $new, Album $album = null)
    {
        if (null === $album) {
            $album = $this->findAlbum($new);
        }

        $owner = $this->getOwner();

        $new->fromArray(array(
            'albumId'   => $album->getId(),
            'accountId' => $owner->getId(),
        ));

        /*
        // Copy file if necessary
        if (null !== $destination) {
            if (!is_dir($destination)) {
                throw new \RuntimeException("Destination does not exist or is a no directory");
            }
            if (!is_writable($filename)) {
                throw new \RuntimeException("Destination is not writable");
            }
            // Trust PHP for using the underlaying OS better than us to
            // copy the file, trust the OS to be very efficient for this

            $filename = $destination . '/' . basename($filename);
        }
         */

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
                continue;
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
            // Create secure by obfuscation filename hash
            $hashName = MediaSecurity::getSecurePath($filepath, '', $owner->getPrivateKey());
            $realPath = FileSystem::pathJoin($owner->getId(), $hashName);
            // Everything is relative find the real file path and create it
            // if necessary
            $target = dirname(FileSystem::pathJoin($this->getDestinationDirectory(), $realPath));
            FileSystem::ensureDirectory($target, true, true);
            // Then copy everything
            if (!copy($filepath, FileSystem::pathJoin($target, basename($realPath)))) {
                throw new \RuntimeException("Could not copy file");
            }
            // Ok we're good to go update the instance
            $new->fromArray(array('realPath' => $realPath));

            $this->mediaDao->save($new);
        }
    }
}
