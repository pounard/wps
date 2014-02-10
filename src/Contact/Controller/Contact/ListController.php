<?php

namespace Contact\Controller\Contact;

use Smvc\Controller\AbstractController;
use Smvc\Dispatch\RequestInterface;
use Smvc\View\View;

class ListController extends AbstractController
{
    public function getAction(RequestInterface $request, array $args)
    {
        $app = $this->getApplication();
        $db = $app->getDatabase();
        $account = $app->getSession()->getAccount();

        $st = $db->prepare("
            SELECT a.id, a.user_name
            FROM account a
            JOIN contact c ON c.id_contact = a.id
            WHERE c.id_account = ?
        ");
        $st->execute(array($account->getId()));

        $list = array();
        foreach ($st as $values) {
            $list[$values->id] = $values->name;
        }

        return new View(array(
            'list' => $list,
            'account' => $account,
        ), 'contact/list');
    }
}
