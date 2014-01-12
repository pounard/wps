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
        $container = $this->getContainer();
        $albumDao = $container->getDao('album');
        $account = $container->getSession()->getAccount();

        $query = $this->getQueryFromRequest($request);
        $albums = $albumDao->loadAllFor(
            array(
                'accountId' => $account->getId(),
            ),
            $query->getLimit(),
            $query->getOffset()
        );

        return new View(array(
            'albums' => $albums,
            'owner'  => $account,
        ), 'app/albums');
    }

    public function getAlbumContents(RequestInterface $request, array $args)
    {
        $container = $this->getContainer();
        $albumDao = $container->getDao('album');
        $mediaDao = $container->getDao('media');

        $query  = $this->getQueryFromRequest($request);
        $album  = $albumDao->load($args[0]);
        $medias = $mediaDao->loadAllFor(array(
            'albumId' => $album->getId(),
        ), 30);

        return new View(array(
            'album'  => $album,
            'medias' => $medias,
            'owner'  => $container->getAccountProvider()->getAccountById($album->getAccountId()),
        ), 'app/album');
    }

    public function getAction(RequestInterface $request, array $args)
    {
        switch (count($args)) {

            case 0:
                return $this->getAlbums($request, $args);

            case 1:
                return $this->getAlbumContents($request, $args);

            default:
                throw new NotFoundError();
        }
    }
}
