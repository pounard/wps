<?php

namespace Contact\Controller\Contact;

use Smvc\Controller\AbstractController;
use Smvc\Dispatch\RequestInterface;
use Smvc\Error\NotFoundError;
use Smvc\View\View;

class ListController extends AbstractController
{
    public function getAction(RequestInterface $request, array $args)
    {
        if (1 !== count($args)) {
            throw new NotFoundError();
        }

        $app = $this->getApplication();
        $db = $app->getDatabase();
        $account = $app->getSession()->getAccount();

        $st = $db->prepare("
            SELECT a.id, c.account_id, a.user_name
            FROM account a
            LEFT JOIN contact c
                ON c.id_contact = a.id
                AND c.id_account = ?
            WHERE a.id = ?
            LIMIT 1 OFFSET 0
        ");
        $st->execute(array($args[1], $account->getId()));

        $exists = false;
        $target = null;
        foreach ($st as $values) {
            if (empty($values->account_id)) {
                // Ok for adding this users.
                return new View(array(
                    'list' => $list,
                    'contact' => $account,
                ), 'contact/list');
            } else {
                // Already in your friends.
                return new View(array(
                    'list' => $list,
                    'contact' => $account,
                ), 'contact/list');
            }
        }
    }
}
