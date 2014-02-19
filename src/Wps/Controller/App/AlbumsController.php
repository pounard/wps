<?php

namespace Wps\Controller\App;

use Smvc\Controller\AbstractController;
use Smvc\Core\Message;
use Smvc\Dispatch\RequestInterface;
use Smvc\Dispatch\Http\RedirectResponse;
use Smvc\Error\NotFoundError;
use Smvc\Media\Persistence\DaoInterface;
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
            ORDER BY a.ts_user_date_end DESC
            LIMIT " . ((int)$query->getLimit()) . " OFFSET " . ((int)$query->getOffset())
        );
        $st->setFetchMode(\PDO::FETCH_COLUMN, 0);
        $st->execute(array($account->getId(), $account->getId()));
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

    public function getAlbumForm(RequestInterface $request, array $args)
    {
        $app = $this->getApplication();
        $albumDao = $app->getDao('album');
        $album = $albumDao->load($args[0]);

        return new View(array(
            'album'  => $album,
        ), 'app/album/edit');
    }

    public function getShareForm(RequestInterface $request, array $args)
    {
        $app = $this->getApplication();
        $albumDao = $app->getDao('album');
        $contactDao = $app->getDao('contact');
        $album = $albumDao->load($args[0]);

        // Already shared too

        // Share potentials

        return new View(array(
            'album'  => $album,
        ), 'app/album/share');
    }

    public function getAction(RequestInterface $request, array $args)
    {
        switch (count($args)) {

            case 0:
                return $this->getAlbums($request, $args);

            case 1:
                return $this->getAlbumContents($request, $args);

            case 2:
                switch ($args[1]) {

                    case 'edit':
                        return $this->getAlbumForm($request, $args);
                        break;

                    case 'share':
                        return $this->getShareForm($request, $args);
                        break;

                    default:
                        throw new NotFoundError();
                }
                break;

            default:
                throw new NotFoundError();
        }
    }

    public function postAction(RequestInterface $request, array $args)
    {
        switch (count($args)) {

            case 2:
                switch ($args[1]) {

                    case 'edit':
                        // Ok continue.
                        break;

                    default:
                        throw new NotFoundError();
            }
            break;

            default:
                throw new NotFoundError();
        }

        $app = $this->getApplication();
        $albumDao = $app->getDao('album');
        $album = $albumDao->load($args[0]);
        $values = $request->getContent();

        $data = array();

        // @todo Filtering and other stuff
        if (empty($values['userName'])) {
            $data['userName'] = null;
        } else {
            $data['userName'] = $values['userName'];
        }

        $album->fromArray($data);
        $albumDao->save($album);

        $app->getMessager()->addMessage("Album details have been updated", Message::TYPE_SUCCESS);

        return new RedirectResponse('app/albums/' . $album->getId());
    }
}
