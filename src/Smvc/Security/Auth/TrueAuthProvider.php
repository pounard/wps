<?php

namespace Smvc\Security\Auth;

/**
 * Always true auth provider
 *
 * Do not use this for anything else than pure testing
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
