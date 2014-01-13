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
            $account = new Account();
            $account->fromArray(array(
                'id'          => $object->id,
                'username'    => $object->mail,
                'displayName' => $object->user_name,
                'salt'        => $object->salt,
                'publicKey'   => $object->key_public,
                'privateKey'  => $object->key_private,
                'keyType'     => $object->key_type
            ));

            return $account;
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
            $account = new Account();
            $account->fromArray(array(
                'id'          => $object->id,
                'username'    => $object->mail,
                'displayName' => $object->user_name,
                'salt'        => $object->salt,
                'publicKey'   => $object->key_public,
                'privateKey'  => $object->key_private,
                'keyType'     => $object->key_type
            ));

            return $account;
        }

        throw new NotFoundError(sprintf("Account with id '%s' does not exist", $id));
    }

    public function getAnonymousAccount()
    {
            $account = new Account();
            $account->fromArray(array(
                'id'       => 0,
                'username' => "Anonymous",
            ));

            return $account;
    }

    public function authenticate($username, $password)
    {
        $db = $this->getContainer()->getDatabase();

        $account = $this->getAccount($username);
        if (!$account) {
            return false;
        }

        $st = $db->prepare("SELECT 1 FROM account WHERE mail = ? AND password_hash = ? AND is_active = 1");
        $st->setFetchMode(\PDO::FETCH_COLUMN, 0);
        $st->execute(array($username, Crypt::getPasswordHash($password, $account->getSalt())));

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

    public function setAccountPassword($id, $password, $salt = null)
    {
        $db = $this->getContainer()->getDatabase();

        if (null === $salt) {
            $account = $this->getAccountById($id);
            $st = $db->prepare("UPDATE account SET password_hash = ? WHERE id = ?");
            $st->execute(array(Crypt::getPasswordHash($password, $account->getSalt()), $id));
        } else {
            $st = $db->prepare("UPDATE account SET password_hash = ?, salt = ? WHERE id = ?");
            $st->execute(array(Crypt::getPasswordHash($password, $salt), $salt, $id));
        }
    }
}
