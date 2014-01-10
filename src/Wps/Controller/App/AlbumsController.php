<?php

namespace Wps\Controller\App;

use Smvc\Controller\AbstractController;
use Smvc\Dispatch\RequestInterface;
use Smvc\Error\NotFoundError;
use Smvc\Media\Persistence\DaoInterface;
use Smvc\View\View;

class AlbumsController extends AbstractController
{
    public function getAlbums(RequestInterface $request, array $args)
    {
        $albumDao = $this->getContainer()->getDao('album');

        $query = $this->getQueryFromRequest($request);
        $albums = $albumDao->loadAllFor(array(), $query->getLimit(), $query->getOffset());

        return new View(array(
            'albums' => $albums,
        ), 'app/albums');
    }

    public function getAlbumContents(RequestInterface $request, array $args)
    {
        $albumDao = $this->getContainer()->getDao('album');
        $mediaDao = $this->getContainer()->getDao('media');

        $query  = $this->getQueryFromRequest($request);
        $album  = $albumDao->load($args[0]);
        $medias = $mediaDao->loadAllFor(array(
            'albumId' => $album->getId(),
        ));

        return new View(array(
            'album'  => $album,
            'medias' => $medias,
        ), 'app/album');
    }

    public function getAction(RequestInterface $request, array $args)
    {
        switch (count($args)) {

            case 0:
                return $this->getAlbums();

            case 1:
                return $this->getAlbumContents($request, $args);

            case 2:
                throw new NotFoundError();
        }
    }
}
