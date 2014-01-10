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
     * Get user account
     *
     * @param scalar $id
     *
     * @return AccountInterface
     */
    public function getAccountById($id);

    /**
     * Get anonymous account
     *
     * @return AccountInterface
     *   Account must have a 0 identifier
     */
    public function getAnonymousAccount();

    /**
     * Set account keys
     *
     * @param scalar $id
     * @param string $privateKey
     * @param string $publicKey
     * @param string $type
     */
    public function setAccountKeys($id, $privateKey, $publicKey, $type);
}
