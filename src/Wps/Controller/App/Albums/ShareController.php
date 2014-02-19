<?php

namespace Wps\Controller\App\Albums;

use Wps\Security\Access;

use Smvc\Controller\AbstractController;
use Smvc\Core\Message;
use Smvc\Dispatch\RequestInterface;
use Smvc\Dispatch\Http\RedirectResponse;
use Smvc\Error\NotFoundError;
use Smvc\View\View;

class ShareController extends AbstractController
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
            'accessLevel' => array(
                Access::LEVEL_PRIVATE => "Private",
                Access::LEVEL_FRIEND => "All my contacts",
                Access::LEVEL_PUBLIC => "Public",
            ),
            'currentAccess' => $album->getAccessLevel(),
        ), 'app/album/share');
    }

    public function postAction(RequestInterface $request, array $args)
    {
        if (1 !== count($args)) {
            throw new NotFoundError();
        }

        $values = $request->getContent();
        $accesLevel = (int)$content['accessLevel'];
        foreach ($values['contacts'] as $id => $value) {
            
        }

        $app->getMessager()->addMessage("Album details have been updated", Message::TYPE_SUCCESS);

        return new RedirectResponse('app/albums/' . $album->getId());
    }
}
