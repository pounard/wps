<?php

namespace Smvc\Security;

use Smvc\Security\Auth\AuthProviderInterface;

interface AccountProviderInterface extends AuthProviderInterface
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
     * Create new account
     *
     * @param string $username
     * @param string $displayName
     *
     * @return Account
     */
    public function createAccount($username, $displayName = null, $active = false, $validateToken = null);

    /**
     * Set account keys
     *
     * @param scalar $id
     * @param string $privateKey
     * @param string $publicKey
     * @param string $type
     */
    public function setAccountKeys($id, $privateKey, $publicKey, $type);

    /**
     * Set account password
     *
     * @param scalar $id
     * @param string $password
     * @param string $salt
     */
    public function setAccountPassword($id, $password, $salt = null);
}
