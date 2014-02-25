<?php

namespace Wps\Controller\Share;

use Wps\Util\Date;

use Smvc\Controller\AbstractController;
use Smvc\Dispatch\RequestInterface;
use Smvc\Dispatch\Http\RedirectResponse;
use Smvc\Error\NotFoundError;
use Smvc\View\View;

class AlbumController extends AbstractController
{
    public function isAuthorized(RequestInterface $request, array $args)
    {
        if (count($args) < 1) {
            return true;
        }

        $token = $args[0];

        $app = $this->getApplication();
        $session = $app->getSession();

        $account = $session->getAccount();
        $db = $app->getDatabase();

        $st = $db->prepare("
            SELECT
                a.id,
                a.share_password,
                s.id_session
            FROM album a
            LEFT JOIN album_acl aa
                ON aa.id_album = a.id
            LEFT JOIN session_share s
                ON s.id_session = ?
                AND s.id_album = a.id
            WHERE
                a.share_token = ?
                AND (
                    a.share_enabled = 1
                    OR (
                        a.id_account = ?
                        OR (
                            aa.id_account = ?
                            AND aa.can_read = 1
                        )
                    )
                )
        ");
        $st->execute(array(
            $session->getId(),
            $token,
            $account->getId(),
            $account->getId(),
        ));

        foreach ($st as $row) {
            if (!empty($row['id_session'])) {
                return true;
            } else if (!empty($row['share_password'])) {
                return new RedirectResponse('share/album/auth/' . $token);
            } else {
                $st = $db->prepare("INSERT INTO session_share (id_session, id_album) VALUES (?, ?)");
                $st->execute(array($session->getId(), $row['id']));
                return true;
            }
        }

        return false;
    }

    public function getMediaAction(RequestInterface $request, array $args)
    {
        $app = $this->getApplication();
        $albumDao = $app->getDao('album');
        $mediaDao = $app->getDao('media');

        $query = $this->getQueryFromRequest($request);
        $media = $mediaDao->load($args[1]);
        $album = $albumDao->load($media->getAlbumId());

        // Find previous and next entry by album
        $prev = null;
        $next = null;
        $db = $app->getDatabase();
        $queryArgs = array(
            $media->getAlbumId(),
            $media->getUserDate()->format(Date::MYSQL_DATETIME),
            $media->getId(),
        );
        $st = $db->prepare("
            SELECT id FROM media
            WHERE id_album = ? AND ts_user_date <= ? AND id NOT IN (?)
            ORDER BY ts_user_date DESC, id DESC
            LIMIT 1
        ");
        $st->setFetchMode(\PDO::FETCH_COLUMN, 0);
        if ($st->execute($queryArgs)) {
            foreach ($st as $value) {
                $prev = $mediaDao->load($value);
            }
        }
        $st = $db->prepare("
            SELECT id FROM media
            WHERE id_album = ? AND ts_user_date >= ? AND id NOT IN (?)
            ORDER BY ts_user_date ASC, id ASC
            LIMIT 1
        ");
        $st->setFetchMode(\PDO::FETCH_COLUMN, 0);
        if ($st->execute($queryArgs)) {
            foreach ($st as $value) {
                $next = $mediaDao->load($value);
            }
        }

        return new View(array(
            'album' => $album,
            'media' => $media,
            'prev'  => $prev,
            'next'  => $next,
            'size'  => isset($args[2]) ? $args[2] : '600',
            'owner' => $app->getAccountProvider()->getAccountById($media->getAccountId()),
            'pathbase' => 'share/album/' . $args[0],
        ), 'share/album/media');
    }

    public function getAlbumAction(RequestInterface $request, array $args)
    {
        $app      = $this->getApplication();
        $albumDao = $app->getDao('album');
        $mediaDao = $app->getDao('media');

        $pager = $this->getPagerQueryFromRequest($request, 'page', 150);
        $album = $albumDao->loadFirst(array(
            'shareEnabled' => 1,
            'shareToken' => $args[0],
        ));
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
        ), 'share/album/view');
    }

    public function getAlbumIndex(RequestInterface $request, array $args)
    {
        $app = $this->getApplication();
        $albumDao = $app->getDao('album');
        $mediaDao = $app->getDao('media');
        $session = $app->getSession();
        $config = $app->getConfig();

        $query = $this->getQueryFromRequest($request);
        $db = $app->getDatabase();
        $st = $db->prepare("
            SELECT
                a.id
            FROM album a
            JOIN session_share s
                ON s.id_session = ?
                AND s.id_album = a.id
            WHERE
                a.share_token IS NOT NULL
                AND a.share_enabled = 1
            ORDER BY a.ts_user_date_end DESC
            LIMIT " . ((int)$query->getLimit()) . " OFFSET " . ((int)$query->getOffset())
        );
        $st->setFetchMode(\PDO::FETCH_COLUMN, 0);
        $st->execute(array($session->getId()));
        $idList = array();
        foreach ($st as $value) {
            $idList[] = $value;
        }
        if (empty($idList)) {
            $albums = array();
        } else {
            $albums = $albumDao->loadAllFor(array('id' => $idList), 0, 0);
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
            'previews' => $previewMediaMap,
            'canRegister' => $config['account/share_register'],
        ), 'share/albums');
    }

    public function getAction(RequestInterface $request, array $args)
    {
        switch (count($args)) {

            case 0:
                return $this->getAlbumIndex($request, $args);

            case 1:
                return $this->getAlbumAction($request, $args);

            default:
                return $this->getMediaAction($request, $args);
        }
    }
}
