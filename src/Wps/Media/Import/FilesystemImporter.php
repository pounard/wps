<?php

namespace Wps\Media\Import;

use Wps\Media\Media;

/**
 * Import medias from the filesystem. This class will work on a root
 * working directory which is supposed unique for each user: file path
 * and album names will be derivated from the file path and must be
 * relative to the working directory in order to avoid importing
 * site's technical information into database.
 */
class FilesystemImporter extends DefaultImporter
{
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

        foreach ($files as $filename) {

            $new = Media::createInstanceFromFile($filename);

            // Pure optimization: avoid loading the album at each iteration
            if (null === $album) {
                $album = $this->findAlbum($new);
            }

            $this->import($new, $album);
        }
    }
}
