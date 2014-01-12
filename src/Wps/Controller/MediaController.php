<?php

namespace Wps\Controller;

use Wps\Media\Toolkit\ExternalImagickImageToolkit;
use Wps\Util\FileSystem;

use Smvc\Controller\AbstractController;
use Smvc\Dispatch\RequestInterface;
use Smvc\Error\NotFoundError;
use Smvc\Dispatch\Http\FileStreamResponse;

/**
 * Generate on the fly images using the asked file size
 */
class MediaController extends AbstractController
{
    public function getAction(RequestInterface $request, array $args)
    {
        if (count($args) < 2) {
           throw new NotFoundError();
        }

        // Ensure we have a valid size
        $sizeId = array_shift($args);
        if (empty($sizeId)) {
          throw new NotFoundError();
        }
        if ('h' === $sizeId[0]) {
            $mode = 'h';
            $size = substr($sizeId, 1);
        } else if ('w' === $sizeId[0]) {
            $mode = 'w';
            $size = substr($sizeId, 1);
        } else if ('m' === $sizeId[0]) {
            $mode = 'm';
            $size = substr($sizeId, 1);
        } else {
            $mode = 's'; // Square
            $size = $sizeId;
        }

        // Ensure size is valid
        // @todo Configuration would be better here
        if (!in_array($size, array("100", "200", "300", "600", "900", "full"))) {
            throw new NotFoundError();
        }

        // Rebuild the media real path from URL
        $hash = FileSystem::pathJoin($args);

        // Load media
        $container = $this->getContainer();
        $mediaDao = $container->getDao("media");
        $media = $mediaDao->loadFirst(array('realPath' => $hash));
        if (!$media) {
            throw new NotFoundError();
        }

        if (!$realPath = $media->getRealPath()) {
            // File has not been copied!
            throw new NotFoundError();
        }
        $config = $container->getConfig();
        $inFile = FileSystem::pathJoin($config['directory/public'], 'full', $realPath);
        $outFile = FileSystem::pathJoin($config['directory/public'], $sizeId, $realPath);

        $toolkit = new ExternalImagickImageToolkit();
        switch ($mode) {

            case 'm':
                $toolkit->scaleTo($inFile, $outFile, $size, $size, true);
                break;

            case 'h':
                $toolkit->scaleTo($inFile, $outFile, null, $size, true);
                break;

            case 'w':
                $toolkit->scaleTo($inFile, $outFile, $size, null, true);
                break;

            case 's':
                $toolkit->scaleAndCrop($inFile, $outFile, $size, $size);
                break;
        }

        return new FileStreamResponse($outFile, array('Content-Type' => $media->getMimetype()));
    }
}
