<?php

namespace Wps\View\Helper\Template;

use Wps\Media\Media as BaseMedia;
use Wps\Util\FileSystem;

use Smvc\View\Helper\Template\AbstractHelper;

class MediaSizeLinks extends AbstractHelper
{
    public function __invoke($media, $current = null, $basepath = 'app/media')
    {
        if (!$media instanceof BaseMedia) {
            return '';
        }
        if (!$realPath = $media->getRealPath()) {
            return ''; // Never crash on display
        }

        $config = $this->getApplication()->getConfig();

        // @todo Unhardcode this and fetch from configuration
        // @todo Include full?
        $allowedSizes = array(
            "preview" => 300,
            "medium" => 600,
            "wide" => 900,
            "huge" => 1200,
        );
        $mediaWidth = $media->getWidth();

        $items = array();
        foreach ($allowedSizes as $label => $width) {

            if ($mediaWidth < $width) {
                break;
            }

            $href = $this->url($basepath . '/' . $media->getId() . '/' . $width);

            if ($current == $width) {
                $items[] = '<li class="current">' . $label . '</li>';
            } else {
                $items[] = '<li><a href="' . $href . '">' . $label . '</a></li>';
            }
        }

        if (!empty($items)) {
            return '<ul class="sizes">' . implode('', $items) . '</ul>';
        }
    }
}
