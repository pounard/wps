<?php

namespace Contact\Controller\Contact;

use Smvc\Controller\AbstractController;
use Smvc\Dispatch\RequestInterface;
use Smvc\Error\NotFoundError;
use Smvc\View\View;
use Smvc\Dispatch\Http\RedirectResponse;

class RequestController extends AbstractController
{
    public function getAction(RequestInterface $request, array $args)
    {
        if (1 !== count($args)) {
            throw new NotFoundError();
        }

        $app     = $this->getApplication();
        $db      = $app->getDatabase();
        $account = $app->getSession()->getAccount();
        $dao     = $app->getDao('contact');
        $contact = $dao->load($args[0]);

        $st = $db->prepare("
            SELECT c.*
            FROM contact c
            WHERE
                c.id_account = ?
                AND c.id_contact = ?
            LIMIT 1 OFFSET 0
        ");
        $st->execute(array($account->getId(), $args[0]));

        foreach ($st as $values) {
            // Can have only one result.
            return new RedirectResponse('contact/list');
        }

        return new View(array('contact' => $contact), 'contact/request');
    }

    public function postAction(RequestInterface $request, array $args)
    {
        if (1 !== count($args)) {
            throw new NotFoundError();
        }

        $app     = $this->getApplication();
        $db      = $app->getDatabase();
        $account = $app->getSession()->getAccount();
        $dao     = $app->getDao('contact');
        $contact = $dao->load($args[0]);

        $st = $db->prepare("
            SELECT c.*
            FROM contact c
            WHERE
                c.id_account = ?
                AND c.id_contact = ?
            LIMIT 1 OFFSET 0
        ");
        $st->execute(array($account->getId(), $args[0]));

        foreach ($st as $values) {
            // Can have only one result.
          return new RedirectResponse('contact/list');
        }

        $st = $db->prepare("
            INSERT INTO contact (id_account, id_contact, is_source, is_paired) VALUES (?, ?, 1, 0), (?, ?, 0, 1)
        ");
        $st->execute(array(
            $account->getId(),
            $contact->getId(),
            $contact->getId(),
            $account->getId(),
        ));

        $app
            ->getMessager()
            ->addMessage(
                  sprintf(
                      "Request to %s has been sent, you wont see the contact until approval",
                      $contact->getDisplayName()
                  )
            );

        return new RedirectResponse('contact/list'); 
    }
}
