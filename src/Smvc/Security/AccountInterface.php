<?php

namespace Smvc\Security;

/**
 * User account
 */
interface AccountInterface
{
    /**
     * Get identifier
     *
     * @return int
     */
    public function getId();

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername();

    /**
     * Get account public key
     *
     * @return string
     */
    public function getPublicKey();

    /**
     * Get account private key
     *
     * @return string
     */
    public function getPrivateKey();

    /**
     * Get account key type
     *
     * @return string
     *   'rsa', 'dsa' or other value
     */
    public function getKeyType();
}
