<?php

namespace Wps\Controller\App;

use Smvc\Controller\AbstractController;
use Smvc\Dispatch\RequestInterface;
use Smvc\Error\NotFoundError;
use Smvc\View\View;

class MediaController extends AbstractController
{
    public function getAction(RequestInterface $request, array $args)
    {
        if (count($args) < 1) {
            throw new NotFoundError();
        }

        $container = $this->getContainer();
        $albumDao = $container->getDao('album');
        $mediaDao = $container->getDao('media');

        $query = $this->getQueryFromRequest($request);
        $media = $mediaDao->load($args[0]);
        $album = $albumDao->load($media->getAlbumId());

        return new View(array(
            'album' => $album,
            'media' => $media,
            'size'  => isset($args[1]) ? $args[1] : 'm900',
            'owner' => $container->getAccountProvider()->getAccountById($media->getAccountId()),
        ), 'app/media');
    }
}
