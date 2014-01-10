<?php

namespace Smvc\Security;

use Smvc\Security\Auth\TrueAuthProvider;

class DefaultAccountProvider extends TrueAuthProvider implements AccountProviderInterface
{
    public function getAccount($username)
    {
        return new Account(abs(crc32($username)), $username);
    }

    public function getAccountById($id)
    {
       return new Account($id, $id);
    }

    public function getAnonymousAccount()
    {
        return new Account(0, "Anonymous");
    }

    public function setAccountKeys($id, $privateKey, $publicKey, $type)
    {
    }
}
