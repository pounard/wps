<?php

namespace Wps\Media\Import;

use Wps\Media\Media;
use Wps\Media\Persistence\DaoInterface;

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
        $this->setDestination($destination);
        $this->setWorkingDirectory($workingDirectory);
    }

    /**
     * Set destination directory
     *
     * @param string $workingDirectory
     *   Root working directory
     */
    public function setDestinationDirectory($destination)
    {
        if (!is_dir($destination)) {
            throw new \RuntimeException("Destination directory does not exists");
        }
        if (!is_writable($destination)) {
            throw new \RuntimeException("Destination directory is not writable");
        }

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
        if (!is_dir($workingDirectory)) {
            throw new \RuntimeException("Working directory does not exists");
        }
        if (!is_readable($workingDirectory)) {
            throw new \RuntimeException("Working directory is not readable");
        }

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

        return $this->workingDirectory . '/' . $path;
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

        foreach ($files as $filename) {
            $new = Media::createInstanceFromFile($filename, $this->destination);
print_r($new);
            // @todo
            // Attempt loading by filename and path
                // If nothing attempt with filename only then check for MD5
            // If something check of MD5
                // If found something matching MD5
                // Else find album by path
                    // If something use this album
                    // Else create new one
            // Save file
        }
    }
}
