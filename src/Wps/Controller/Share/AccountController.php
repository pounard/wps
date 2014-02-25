<?php

namespace Wps\Controller\Share;

use Wps\Util\Date;

use Account\Security\Crypt;

use Smvc\Controller\AbstractController;
use Smvc\Core\Message;
use Smvc\Dispatch\Http\RedirectResponse;
use Smvc\Dispatch\RequestInterface;
use Smvc\Error\NotFoundError;
use Smvc\View\View;

class AccountController extends AbstractController
{
    public function isAuthorized(RequestInterface $request, array $args)
    {
        $app = $this->getApplication();
        $session = $app->getSession();

        if ($session->isAuthenticated()) {
            return false;
        }

        $config = $app->getConfig();
        if (!$config['account/share_register']) {
            return false;
        }

        $db = $app->getDatabase();

        // Ensure the user has something shared
        $st = $db->prepare("
            SELECT
                1
            FROM album a
            JOIN session_share s
                ON s.id_session = ?
                AND s.id_album = a.id
            WHERE
                a.share_enabled = 1
        ");
        $st->execute(array($session->getId()));

        foreach ($st as $value) {
            return true;
        }

        return false;
    }

    public function getAction(RequestInterface $request, array $args)
    {
        return new View(array(), 'share/account');
    }

    public function postAction(RequestInterface $request, array $args)
    {
        // @todo Duplicate code from the new account controller
        // Find a better way to achieve this
        $content = $request->getContent();
        $app = $this->getApplication();
        $accountProvider = $app->getAccountProvider();
        $messager = $app->getMessager();
        $hasErrors = false;

        // Check for mail being unique
        if (empty($content['mail'])) {
            $messager->addMessage("Mail address cannot be empty", Message::TYPE_ERROR);
            $hasErrors = true;
        } else {
            try {
                if ($account = $accountProvider->getAccount($content['mail'])) {
                    $messager->addMessage("Mail address is already registered", Message::TYPE_ERROR);
                    $hasErrors = true;
                }
            } catch (NotFoundError $e) {
                // That's ok user does not exists.
            }
        }

        // Check for password and confirmation being identical
        if (empty($content['passwordNew']) || empty($content['passwordConfirm'])) {
            $messager->addMessage("Password and confirmation cannot be empty", Message::TYPE_ERROR);
            $hasErrors = true;
        } else if ($content['passwordNew'] !== $content['passwordConfirm']) {
            $messager->addMessage("Password and confirmation must be identical", Message::TYPE_ERROR);
            $hasErrors = true;
        }

        if ($hasErrors) {
            return new View(array(), 'account/new');
        }

        $account = $accountProvider->createAccount(
            $content['mail'],
            $content['displayName'],
            true, /* Set to false once mails are OK */
            null /* Set validate token */
        );
        $accountProvider->setAccountPassword(
            $account->getId(),
            $content['passwordNew'],
            Crypt::createSalt()
        );

        $messager->addMessage("A validation email has been sent and will arrive shortly, please do follow instructions provided", Message::TYPE_SUCCESS);

        // From this point, everything is ok, just give the new account the
        // right album ACL
        $db = $app->getDatabase();
        $st = $db->prepare("
            INSERT INTO album_acl (id_album, id_account, can_read, can_write)
            SELECT
                a.id  AS id_album,
                ?     AS id_account,
                1     AS can_read,
                0     AS can_write
            FROM album a
            JOIN session_share s
                ON s.id_session = ?
                AND s.id_album = a.id
            WHERE
                a.share_enabled = 1
        ");
        $st->execute(array(
            $account->getId(),
            $app->getSession()->getId(),
        ));

        return new RedirectResponse('share/album');
    }
}
