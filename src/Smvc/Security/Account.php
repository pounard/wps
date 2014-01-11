<?php

namespace Smvc\Security;

/**
 * User account
 */
class Account implements AccountInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $displayName;

    /**
     * @var string
     */
    private $privateKey;

    /**
     * @var string
     */
    private $publicKey;

    /**
     * @var string
     */
    private $keyType;

    /**
     * Default constructor
     *
     * @param int $id
     * @param string $username
     * @param string $displayName
     * @param string $password
     * @param string $publicKey
     * @param string $privateKey
     * @param string $keyType
     */
    public function __construct(
        $id,
        $username    = null,
        $displayName = null,
        $password    = null,
        $publicKey   = null,
        $privateKey  = null,
        $keyType     = null)
    {
        $this->id = $id;
        $this->username = $username;
        $this->displayName = $displayName;
        $this->password = $password;
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
        $this->keyType = $keyType;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getDisplayName()
    {
        if (null === $this->displayName) {
            return $this->username;
        }
        return $this->displayName;
    }

    public function getPublicKey()
    {
        return $this->publicKey;
    }

    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    public function getKeyType()
    {
        return $this->keyType;
    }
}
