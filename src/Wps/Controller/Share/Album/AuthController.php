<?php

namespace Wps\Controller\Share\Album;

use Smvc\Controller\AbstractController;
use Smvc\Core\Message;
use Smvc\Dispatch\RequestInterface;
use Smvc\Dispatch\Http\RedirectResponse;
use Smvc\Error\NotFoundError;
use Smvc\View\View;

class AuthController extends AbstractController
{
    public function isAuthorized(RequestInterface $request, array $args)
    {
        if (count($args) < 1) {
            throw new NotFoundError();
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
            if (empty($row['id_session']) && !empty($row['share_password'])) {
                return true;
            }
        }

        return false;
    }

    public function getAction(RequestInterface $request, array $args)
    {
        if (count($args) < 1) {
            throw new NotFoundError();
        }

        $app      = $this->getApplication();
        $albumDao = $app->getDao('album');

        $album = $albumDao->loadFirst(array(
            'shareEnabled' => 1,
            'shareToken' => $args[0],
        ));

        return new View(array(
            'album' => $album,
        ), 'share/album/auth');
    }

    public function postAction(RequestInterface $request, array $args)
    {
        if (count($args) < 1) {
            throw new NotFoundError();
        }

        $app      = $this->getApplication();
        $albumDao = $app->getDao('album');
        $db       = $app->getDatabase();
        $session  = $app->getSession();

        $album = $albumDao->loadFirst(array(
            'shareEnabled' => 1,
            'shareToken' => $args[0],
        ));

        $content = $request->getContent();
        if (!empty($content['password']) && $content['password'] === $album->getSharePassword()) {
            $st = $db->prepare("INSERT INTO session_share (id_session, id_album) VALUES (?, ?)");
            $st->execute(array($session->getId(), $album->getId()));

            return new RedirectResponse('share/album/' . $args[0]);
        }

        $app->getMessager()->addMessage("Invalid password", Message::TYPE_ERROR);

        return $this->getAction($request, $args);
    }
}
