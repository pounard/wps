<?php

namespace Contact\Model;

use Smvc\Model\Persistence\DtoInterface;
use Smvc\Model\DefaultExchange;

/**
 * Reprensent a user of the site but not in the account sense.
 *
 * Most of the content here actually comes from the AccountInterface object.
 */
class Contact extends DefaultExchange implements DtoInterface
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

    public function getPublicKey()
    {
        return $this->publicKey;
    }

    public function getKeyType()
    {
        return $this->keyType;
    }

    protected function getPrivateProperties()
    {
        return array('username');
    }

    public function toArray()
    {
        return array(
            'id'          => $this->id,
            'username'    => $this->username,
            'displayName' => $this->displayName,
            'publicKey'   => $this->publicKey,
            'keyType'     => $this->keyType,
        );
    }

    public function fromArray(array $array)
    {
        $array += $this->toArray();

        $this->id          = $array['id'];
        $this->username    = $array['username'];
        $this->displayName = $array['displayName'];
        $this->publicKey   = $array['publicKey'];
        $this->keyType     = $array['keyType'];
    }
}
