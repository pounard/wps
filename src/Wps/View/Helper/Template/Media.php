<?php

namespace Wps\View\Helper\Template;

use Wps\Media\Media as BaseMedia;
use Wps\Util\FileSystem;

use Smvc\View\Helper\Template\AbstractHelper;

class Media extends AbstractHelper
{
    public function __invoke($media, $size = 100, $withLink = true, $toSize = 600)
    {
        if (!$media instanceof BaseMedia) {
            return '';
        }
        if (!$realPath = $media->getRealPath()) {
            return ''; // Never crash on display
        }

        if (is_string($size) && !is_numeric($size[0])) {
            if ('h' === $size[0]) {
                $height = (int)substr($size, 1);
                $width = floor(($height / $media->getHeight()) * $media->getWidth());
            } else if ('w' === $size[0]) {
                $width = (int)substr($size, 1);
                $height = floor(($width / $media->getWidth()) * $media->getHeight());
            } else if ('s' === $size[0]) {
                $width = $height = (int)substr($size, 1);
                $size = $width;
            } else {
                // Dafuck?
                return '';
            }
        } else {
            $width = (int)$size;
            $height = floor(($width / $media->getWidth()) * $media->getHeight());
            $size = 'w' . $size;
        }

        $config = $this->getApplication()->getConfig();
        $src    = $this->url(FileSystem::pathJoin($config['directory/web'], $size, $realPath));

        $href   = null;
        if ($withLink) {
            if ('full' === $toSize) {
                $href = $this->url(FileSystem::pathJoin($config['directory/web'], $toSize, $realPath));
            } else if (is_string($withLink)) {
                $href = $this->url(FileSystem::pathJoin($withLink, $media->getId(), $toSize));
            } else {
                $href = $this->url(FileSystem::pathJoin('app/media', $media->getId(), $toSize));
            }
        }

        $imgTag = '<img class="lazy-load" data-src="' . $src . '" alt="' . $media->getDisplayName() . '" width="' . $width . '" height="' . $height . '"/>';

        if ($href) {
            return '<a class="media-link" href="' . $href . '" title="View larger">' . $imgTag . '</a>';
        } else {
            return $imgTag;
        }
    }
}
