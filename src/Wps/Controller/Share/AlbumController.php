<?php

namespace Wps\Controller\Share;

use Smvc\Controller\AbstractController;
use Smvc\Core\Message;
use Smvc\Dispatch\RequestInterface;
use Smvc\Dispatch\Http\RedirectResponse;
use Smvc\Error\NotFoundError;
use Smvc\View\View;

class AlbumController extends AbstractController
{
    public function isAuthorized(RequestInterface $request, array $args)
    {
        if (1 !== count($args)) {
            throw new \NotFoundError();
        }

        $app = $this->getApplication();
        $account = $app->getSession()->getAccount();
        $db = $app->getDatabase();

        $st = $db->prepare("
            SELECT 1
            FROM album a
            LEFT JOIN album_acl aa
                ON aa.id_album = a.id
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
        $st->setFetchMode(\PDO::FETCH_COLUMN, 0);
        $st->execute(array(
            $args[0],
            $account->getId(),
            $account->getId(),
        ));

        foreach ($st as $value) {
            return true;
        }

        return false;
    }

    public function getAction(RequestInterface $request, array $args)
    {
        if (1 !== count($args)) {
            throw new NotFoundError();
        }

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
}
