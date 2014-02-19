<?php

namespace Wps\View\Helper\Template;

use Wps\Media\Media as BaseMedia;
use Wps\Util\FileSystem;

use Smvc\View\Helper\Template\AbstractHelper;

class Media extends AbstractHelper
{
    public function __invoke($media, $size = 100, $withLink = true, $toSize = 'w600')
    {
        if (!$media instanceof BaseMedia) {
            return '';
        }
        if (!$realPath = $media->getRealPath()) {
            return ''; // Never crash on display
        }

        $config = $this->getApplication()->getConfig();
        $src    = $this->url(FileSystem::pathJoin($config['directory/web'], $size, $realPath));

        $href   = null;
        if ($withLink) {
            if ('full' === $toSize) {
                $href = $this->url(FileSystem::pathJoin($config['directory/web'], $toSize, $realPath));
            } else {
                $href = $this->url(FileSystem::pathJoin('app/media', $media->getId(), $toSize));
            }
        }

        if ('h' === $size[0]) {
            $height = (int)substr($size, 1);
            $width = floor(($height / $media->getHeight()) * $media->getWidth());
        } else if ('w' === $size[0]) {
            $width = (int)substr($size, 1);
            $height = floor(($width / $media->getWidth()) * $media->getHeight());
        } else if ('m' === $size[0]) {
            // FIXME Should be max of depending on width or height
            $width = $height = (int)substr($size, 1);
        } else {
            // Square
            $width = $height = (int)$size;
        }

        $imgTag = '<img class="lazy-load" data-src="' . $src . '" alt="' . $media->getDisplayName() . '" width="' . $width . '" height="' . $height . '"/>';

        if ($href) {
            return '<a href="' . $href . '" title="View larger">' . $imgTag . '</a>';
        } else {
            return $imgTag;
        }
    }
}
