<?php

namespace Wps\Controller\App\Albums;

use Smvc\Controller\AbstractController;
use Smvc\Dispatch\RequestInterface;
use Smvc\Error\NotFoundError;

class AbstractEditController extends AbstractController
{
    public function isAuthorized(RequestInterface $request, array $args)
    {
        if (1 !== count($args)) {
            throw new \NotFoundError();
        }

        if (!parent::isAuthorized($request, $args)) {
            return false;
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
                a.id = ?
                AND (
                    a.id_account = ?
                    OR (
                        aa.id_account = ?
                        AND aa.can_write = 1
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
}
