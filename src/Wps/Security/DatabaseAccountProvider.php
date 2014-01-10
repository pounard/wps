<?php

namespace Wps\Security;

use Smvc\Core\AbstractContainerAware;
use Smvc\Error\NotFoundError;
use Smvc\Security\Account;
use Smvc\Security\AccountProviderInterface;
use Smvc\Security\Auth\AuthProviderInterface;
use Smvc\Security\Crypt\Crypt;

class DatabaseAccountProvider extends AbstractContainerAware implements
    AccountProviderInterface
{
    public function getAccount($username)
    {
        $db = $this->getContainer()->getDatabase();

        $st = $db->prepare("SELECT * FROM account WHERE mail = :mail");
        $st->setFetchMode(\PDO::FETCH_OBJ);
        $st->execute(array(':mail' => $username));

        foreach ($st as $object) {
            return new Account(
                $object->id,
                $object->mail,
                $object->user_name,
                null,
                $object->key_public,
                $object->key_private,
                $object->key_type
            );
        }

        throw new NotFoundError(sprintf("Account with name '%s' does not exist", $username));
    }

    public function getAccountById($id)
    {
        $db = $this->getContainer()->getDatabase();

        $st = $db->prepare("SELECT * FROM account WHERE id = :id");
        $st->setFetchMode(\PDO::FETCH_OBJ);
        $st->execute(array(':id' => $id));

        foreach ($st as $object) {
            return new Account(
                $object->id,
                $object->mail,
                $object->user_name,
                null,
                $object->key_public,
                $object->key_private,
                $object->key_type
            );
        }

        throw new NotFoundError(sprintf("Account with id '%s' does not exist", $id));
    }

    public function getAnonymousAccount()
    {
        return new Account(0, "Anonymous");
    }

    public function authenticate($username, $password)
    {
        $db = $this->getContainer()->getDatabase();

        $account = $this->getAccount($username);
        if (!$account) {
            return false;
        }

        // FIXME: Missing password
        $st = $db->prepare("SELECT 1 FROM account WHERE mail = ? AND password_hash = ? AND is_active = 1");
        $st->setFetchMode(\PDO::FETCH_COLUMN, 0);
        $st->execute(array($username, Crypt::getPasswordHash($password, $account->getPrivateKey())));

        foreach ($st as $exists) {
            return true;
        }

        return false;
    }

    public function setAccountKeys($id, $privateKey, $publicKey, $type)
    {
        $db = $this->getContainer()->getDatabase();

        $st = $db->prepare("UPDATE account SET key_public = ?, key_private = ?, key_type = ? WHERE id = ?");
        $st->execute(array(
            $publicKey,
            $privateKey,
            $type,
            $id
        ));
    }

    public function setAccountPassword($id, $password)
    {
      $db = $this->getContainer()->getDatabase();

      $account = $this->getAccountById($id);

      $st = $db->prepare("UPDATE account SET password_hash = ? WHERE id = ?");
      $st->execute(array(Crypt::getPasswordHash($password, $account->getPrivateKey()), $id));
    }
}
