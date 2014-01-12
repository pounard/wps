<?php

namespace Wps\View\Helper\Template;

use Wps\Media\Media as BaseMedia;
use Wps\Util\FileSystem;

use Smvc\Core\AbstractContainerAware;
use Smvc\View\Helper\Template\AbstractHelper;

class Media extends AbstractHelper
{
    public function __invoke($media, $size = 100, $withLink = true, $toSize = '600')
    {
        if (!$media instanceof BaseMedia) {
            return '';
        }
        if (!$realPath = $media->getRealPath()) {
            return ''; // Never crash on display
        }

        $config = $this->getContainer()->getConfig();
        $src    = $this->url(FileSystem::pathJoin($config['directory/web'], $size, $realPath));

        $href   = null;
        if ($withLink) {
            $href = $this->url(FileSystem::pathJoin('albums', $media->getAlbumId(), 'media', $media->getId(), $toSize));
        }

        $imgTag = '<img src="' . $src . '" width="' . $size . '" height="' . $size .'" alt="' . $media->getDisplayName() . '"/>';

        if ($href) {
            return '<a href="' . $href . '">' . $imgTag . '</a>';
        } else {
            return $imgTag;
        }
    }
}
