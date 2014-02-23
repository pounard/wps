<?php

namespace Wps\Controller\App\Albums;

use Wps\Security\Access;

use Smvc\Core\Message;
use Smvc\Dispatch\RequestInterface;
use Smvc\Dispatch\Http\RedirectResponse;
use Smvc\Error\NotFoundError;
use Smvc\View\View;
use Account\Security\Crypt;

class ShareController extends AbstractEditController
{
    public function getCurrentContacts($albumId)
    {
        $db = $this->getApplication()->getDatabase();

        $st = $db->prepare("
            SELECT a.id_account
            FROM album_acl a
            WHERE a.id_album = ?
        ");
        $st->setFetchMode(\PDO::FETCH_COLUMN, 0);
        $st->execute(array($albumId));

        return $st->fetchAll();
    }

    public function getCandidateContacts()
    {
        $app = $this->getApplication();
        $db = $app->getDatabase();

        $st = $db->prepare("
            SELECT
                c.id_contact,
                COALESCE(a.user_name, a.mail)
            FROM contact c
            JOIN account a ON a.id = c.id_contact
            WHERE c.id_account = ?
        ");
        $st->setFetchMode(\PDO::FETCH_KEY_PAIR);
        $st->execute(array(
            $app->getSession()->getAccount()->getId(),
        ));

        return $st->fetchAll();
    }

    public function getAction(RequestInterface $request, array $args)
    {
        $app = $this->getApplication();
        $albumDao = $app->getDao('album');
        $contactDao = $app->getDao('contact');
        $album = $albumDao->load($args[0]);

        return new View(array(
            'album'  => $album,
            'contacts' => $this->getCandidateContacts(),
            'currentContacts' => $this->getCurrentContacts($album->getId()),
            'isShared' => $album->isShared(),
            'currentToken' => $album->getShareToken(),
            'currentPassword' => $album->getSharePassword(),
            'accessLevel' => array(
                Access::LEVEL_PRIVATE => "Private",
                Access::LEVEL_FRIEND  => "All my contacts",
                Access::LEVEL_PUBLIC_HIDDEN => "Public but not listed (anonymous users need direct URL)",
                Access::LEVEL_PUBLIC_VISIBLE => "Public and visible for anonymous",
            ),
            'currentAccess' => $album->getAccessLevel(),
        ), 'app/album/share');
    }

    public function postAction(RequestInterface $request, array $args)
    {
        if (1 !== count($args)) {
            throw new NotFoundError();
        }

        $app = $this->getApplication();
        $albumDao = $app->getDao('album');
        $album = $albumDao->load($args[0]);
        $messager = $app->getMessager();

        $dropSessions = false;
        $updates = array();

        $content = $request->getContent();
        $updates['shareEnabled'] = (bool)$content['isShared'];
        if ($updates['shareEnabled'] && !$album->getShareToken()) {
            // Generate a new token if necessary
            $updates['shareToken'] = preg_replace('/[^a-zA-Z0-9]/', '', Crypt::createRandomToken());
        }

        if (empty($content['sharePassword'])) {
            $updates['sharePassword'] = null;
        } else {
            $updates['sharePassword'] = $content['sharePassword'];
        }

        if ($album->getSharePassword() !== $updates['sharePassword']) {
            // Invalidate sessions if password if changed
            $dropSessions = true;
        }
        if (!$updates['shareEnabled'] && $album->isShared()) {
            // Invalidate sessions in case of disable
            $dropSessions = true;
        }

        $album->fromArray($updates);
        $albumDao->save($album);

        // Drop sessions if necessary
        // @todo
        if ($dropSessions) {
            $messager->addMessage("Due to changes user session that may access the album have been dropped, they will need to relog", Message::TYPE_INFO);
        }

        $messager->addMessage("Album details have been updated", Message::TYPE_SUCCESS);

        return new RedirectResponse('app/albums/' . $album->getId());
    }
}
