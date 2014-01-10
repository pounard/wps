<?php

namespace Wps\Media\Import;

use Smvc\Media\Persistence\DaoInterface;

use Wps\Media\Media;
use Wps\Util\FileSystem;
use Wps\Media\Album;

/**
 * Import medias from the filesystem. This class will work on a root
 * working directory which is supposed unique for each user: file path
 * and album names will be derivated from the file path and must be
 * relative to the working directory in order to avoid importing
 * site's technical information into database.
 */
class FilesystemImporter
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
     * Default constructor
     *
     * @param Importer $importer
     *   Importer instance
     * @param string $workingDirectory
     *   Root working directory
     */
    public function __construct(
        DaoInterface $mediaDao,
        DaoInterface $albumDao,
        $workingDirectory = null,
        $destination = null)
    {
        $this->mediaDao = $mediaDao;
        $this->albumDao = $albumDao;

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
     * Get full path by prepending the working directory
     *
     * @param string $path
     */
    public function getFullPath($path)
    {
        if (null === $this->workingDirectory) {
            return $path;
        }

        return FileSystem::pathJoin($this->workingDirectory, $path);
    }

    /**
     * Import from folder
     *
     * @param string $path
     *   Path must be relative to working directory
     */
    public function importFromFolder($path)
    {
        $files = new \CallbackFilterIterator(
            new \FilesystemIterator(
                $this->getFullPath($path),
                \FilesystemIterator::CURRENT_AS_PATHNAME |
                \FilesystemIterator::SKIP_DOTS
            ),
            function ($current, $key, $iterator) {
                return is_file($current);
            }
        );

        $album = null;
        $accountId = 0;

        foreach ($files as $filename) {

            if (null === $album) {
                // We should definitely create the album if possible
                $album = $this->albumDao->loadFirst(array(
                    'path' => $path,
                ));

                if (!$album) {
                    $album = new Album();
                    $album->fromArray(array(
                        'accountId'   => $accountId,
                        'path'        => $path,
                    ));
                }

                // Update or insert, when updating this will change the
                // update date
                $this->albumDao->save($album);
            }

            $new = Media::createInstanceFromFile($filename);
            $new->fromArray(array(
                'albumId'   => $album->getId(),
                'accountId' => $accountId,
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
                    continue;
                } else {
                    // Update
                    $toUpdate = $existing;
                }
            }

            // If nothing attempt with filename only then check for MD5
            if (!$toUpdate) {
                // @todo Should we update a photo in another album?
                // @todo Or just warn the user there is potential duplicates?
                // Sounds dumb, right?

                // @todo
                // Copy the file over the the existing one and update
                // existing instance internals

                $this->mediaDao->save($new);
            } else {
                // @todo
                // Copy the new file 

                $this->mediaDao->save($existing);
            }
        }
    }
}
