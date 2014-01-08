<?php

namespace Smvc\Security;

class DefaultAccountProvider implements AccountProviderInterface
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
        return new Account(0, "Anonymous", null);
    }
}
