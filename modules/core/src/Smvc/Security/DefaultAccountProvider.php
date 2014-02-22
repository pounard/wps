<?php

namespace Smvc\Security;

use Smvc\Security\Auth\TrueAuthProvider;

class DefaultAccountProvider extends TrueAuthProvider implements AccountProviderInterface
{
    public function getAccount($username)
    {
        $account = new Account();
        $account->toArray(array(
            'id' => abs(crc32($username)),
            'username' => $username,
        ));

        return $account;
    }

    public function getAccountById($id)
    {
        $account = new Account();
        $account->toArray(array(
            'id' => $id,
            'username' => $id,
        ));

        return $account;
    }

    public function getAnonymousAccount()
    {
        $account = new Account();
        $account->toArray(array(
            'id' => 0,
            'username' => "Anonymous",
        ));

        return $account;
    }

    public function setAccountKeys($id, $privateKey, $publicKey, $type)
    {
    }
}
