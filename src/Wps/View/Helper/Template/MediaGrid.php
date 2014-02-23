<?php

namespace Wps\View\Helper\Template;

use Wps\Media\Media as BaseMedia;
use Wps\Util\FileSystem;

use Smvc\View\Helper\Template\AbstractHelper;
use Smvc\View\View;

class MediaGrid extends AbstractHelper
{
    public function __invoke($mediaList, $columns = 3, $width = 200, $withLink = true, $toSize = 600)
    {
        if (!is_array($mediaList)) {
            return ''; // Better be safe than sorry.
        }

        $columnsData = array_fill(0, $columns, array());
        $columnsSize = array_fill(0, $columns, 0);

        foreach ($mediaList as $media) {
            if (!$media instanceof BaseMedia) {
                continue; // Better be safe than sorry.
            }
            if (!$output = $this->media($media, $width, $withLink, $toSize)) {
                continue; // Better be safe than sorry.
            }

            $currentColumn = 0;
            $currentSize = null;
            for ($i = 0; $i < $columns; ++$i) {
                if (null === $currentSize || $columnsSize[$i] < $currentSize) {
                    $currentColumn = $i;
                    $currentSize = $columnsSize[$i];
                }
            }
            $height = floor(($width / $media->getWidth()) * $media->getHeight());
            $columnsSize[$currentColumn] += $height;
            $columnsData[$currentColumn][] = $output;
        }

        return new View(array('columns' => $columnsData, 'width'   => $width), 'app/helper/media-grid');
    }
}
