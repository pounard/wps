<?php

namespace Smvc\Security;

class DefaultAccountProvider implements AccountProviderInterface
{
    public function getAccount($username)
    {
        return new Account(crc32($username), $username, null);
    }

    public function getAnonymousAccount()
    {
        return new Account(0, "Anonymous", null);
    }
}
