<?php

namespace Smvc\Security;

interface AccountProviderInterface
{
    /**
     * Get user account
     *
     * @param string $username
     *
     * @return AccountInterface
     */
    public function getAccount($username);

    /**
     * Get anonymous account
     *
     * @return AccountInterface
     *   Account must have a 0 identifier
     */
    public function getAnonymousAccount();
}
