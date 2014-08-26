<?php

namespace Smvc\Security;

use Smvc\Model\DefaultExchange;

/**
 * User account
 */
class Account extends DefaultExchange implements AccountInterface
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
    private $password;

    /**
     * @var string
     */
    private $salt;

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

    public function getSalt()
    {
        return $this->salt;
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

    protected function getPrivateProperties()
    {
        return array('username', 'password', 'salt', 'privateKey');
    }

    public function toArray()
    {
        return array(
            'id'          => $this->id,
            'username'    => $this->username,
            'displayName' => $this->displayName,
            'password'    => $this->password,
            'salt'        => $this->salt,
            'publicKey'   => $this->publicKey,
            'privateKey'  => $this->privateKey,
            'keyType'     => $this->keyType,
        );
    }

    public function fromArray(array $array)
    {
        $array += $this->toArray();

        $this->id          = $array['id'];
        $this->username    = $array['username'];
        $this->displayName = $array['displayName'];
        $this->password    = $array['password'];
        $this->salt        = $array['salt'];
        $this->publicKey   = $array['publicKey'];
        $this->privateKey  = $array['privateKey'];
        $this->keyType     = $array['keyType'];
    }
}
