<?php

namespace Contact\Controller\Contact;

use Smvc\Controller\AbstractController;
use Smvc\Dispatch\RequestInterface;
use Smvc\View\View;

class ListController extends AbstractController
{
    public function getAction(RequestInterface $request, array $args)
    {
        $app     = $this->getApplication();
        $db      = $app->getDatabase();
        $dao     = $app->getDao('contact');
        $account = $app->getSession()->getAccount();

        $st = $db->prepare("
            SELECT c.id_contact
            FROM contact c
            WHERE c.id_account = ?
        ");
        $st->execute(array($account->getId()));

        $idList = array();
        foreach ($st as $values) {
            $idList[] = $values['id_contact'];
        }

        return new View(array(
            'contacts' => $dao->loadAll($idList),
            'account'  => $account,
        ), 'contact/list');
    }
}
