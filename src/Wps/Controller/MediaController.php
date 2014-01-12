<?php

namespace Wps\Controller;

use Wps\Media\Toolkit\ExternalImagickImageToolkit;
use Wps\Util\FileSystem;

use Smvc\Controller\AbstractController;
use Smvc\Dispatch\RequestInterface;
use Smvc\Error\NotFoundError;
use Smvc\Dispatch\Http\FileStreamResponse;

class MediaController extends AbstractController
{
    public function getAction(RequestInterface $request, array $args)
    {
        if (count($args) < 2) {
           throw new NotFoundError();
        }

        // Ensure we have a valid size
        $size = array_shift($args);
        if (!in_array($size, array("100", "150", "300", "500", "900", "full"))) {
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
        $outFile = FileSystem::pathJoin($config['directory/public'], $size, $realPath);

        $toolkit = new ExternalImagickImageToolkit();
        $toolkit->scaleAndCrop($inFile, $outFile, $size, $size);

        return new FileStreamResponse($outFile, array('Content-Type' => $media->getMimetype()));
    }
}
