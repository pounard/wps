<?php

namespace Wps\Controller\App;

use Smvc\Controller\AbstractController;
use Smvc\Core\Message;
use Smvc\Dispatch\RequestInterface;
use Smvc\Dispatch\Http\RedirectResponse;
use Smvc\Error\NotFoundError;
use Smvc\View\View;

class AlbumsController extends AbstractController
{
    public function getAlbums(RequestInterface $request, array $args)
    {
        $app = $this->getApplication();
        $albumDao = $app->getDao('album');
        $mediaDao = $app->getDao('media');
        $account = $app->getSession()->getAccount();

        $query = $this->getQueryFromRequest($request);
        $db = $app->getDatabase();
        $st = $db->prepare("
            SELECT DISTINCT(a.id)
            FROM album a
            LEFT JOIN album_acl aa
                ON aa.id_album = a.id
            WHERE (
                a.id_account = ?
                OR aa.id_account = ?
            )
            ORDER BY a.ts_user_date_begin DESC
            LIMIT " . ((int)$query->getLimit()) . " OFFSET " . ((int)$query->getOffset())
        );
        $st->setFetchMode(\PDO::FETCH_COLUMN, 0);
        $st->execute(array($account->getId(), $account->getId()));
        $idList = array();
        foreach ($st as $value) {
            $idList[] = $value;
        }
        if (empty($idList)) {
            $objects = array();
        } else {
            $objects = $albumDao->loadAllFor(array('id' => $idList), 0, 0);
        }

        // Keep $idList order
        $albums = array();
        foreach ($idList as $id) {
            if (isset($objects[$id])) {
                $albums[$id] = $objects[$id];
            }
        }

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
        $app      = $this->getApplication();
        $albumDao = $app->getDao('album');
        $mediaDao = $app->getDao('media');

        $pager = $this->getPagerQueryFromRequest($request, 'page', 150);
        $album = $albumDao->load($args[0]);
        $conditions = array('albumId' => $album->getId());
        // Proceed to the real query only if we have a total count
        if ($total = $mediaDao->countFor($conditions)) {
            $pager->setTotal($total);
            $medias = $mediaDao->loadAllFor($conditions, $pager->getLimit(), $pager->getOffset());
        }

        return new View(array(
            'album'  => $album,
            'medias' => $medias,
            'pager'  => $pager,
            'owner'  => $app->getAccountProvider()->getAccountById($album->getAccountId()),
        ), 'app/album/view');
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
