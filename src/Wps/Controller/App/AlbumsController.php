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
        $mediaDao = $container->getDao('media');
        $account = $container->getSession()->getAccount();

        $query = $this->getQueryFromRequest($request);
        $albums = $albumDao->loadAllFor(
            array(
                'accountId' => $account->getId(),
            ),
            $query->getLimit(),
            $query->getOffset()
        );

        // Existing user set preview identifiers
        $previewIdList = array();
        $previewMediaMap = array();
        foreach ($albums as $album) {
            if ($id = $album->getPreviewMediaId()) {
                $previewIdList[] = $id;
            } else {
                // Find first media of this album
                // This is worst case scenario and I hop this won't happen
                $albumId = $album->getId();
                if ($media = $mediaDao->loadFirst(array('albumId' => $albumId))) {
                    $previewMediaMap[$albumId] = $media;
                }
            }
        }
        if (!empty($previewIdList)) {
            foreach ($mediaDao->loadAll($previewIdList) as $media) {
                // Preview can only be a media of the selected album
                $previewMediaMap[$media->getAlbumId()] = $media;
            }
        }

        return new View(array(
            'albums'   => $albums,
            'owner'    => $account,
            'previews' => $previewMediaMap,
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
