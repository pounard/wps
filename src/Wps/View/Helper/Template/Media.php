<?php

namespace Wps\View\Helper\Template;

use Wps\Media\Media as BaseMedia;
use Wps\Util\FileSystem;

use Smvc\Core\AbstractContainerAware;
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

        $config = $this->getContainer()->getConfig();
        $src    = $this->url(FileSystem::pathJoin($config['directory/web'], $size, $realPath));

        $href   = null;
        if ($withLink) {
            if ('full' === $toSize) {
                $href = $this->url(FileSystem::pathJoin($config['directory/web'], $toSize, $realPath));
            } else {
                $href = $this->url(FileSystem::pathJoin('app/media', $media->getId(), $toSize));
            }
        }

        $imgTag = '<img src="' . $src . '" alt="' . $media->getDisplayName() . '"/>';

        if ($href) {
            return '<a href="' . $href . '">' . $imgTag . '</a>';
        } else {
            return $imgTag;
        }
    }
}
