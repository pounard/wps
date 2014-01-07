<?php

namespace Smvc\Security\Auth;

/**
 * Always true auth provider
 */
class TrueAuthProvider implements AuthProviderInterface
{
    /**
     * Authenticate account
     *
     * @param string $username
     * @param string $password
     *
     * @return boolean
     */
    public function authenticate($username, $password)
    {
        return true;
    }
}
