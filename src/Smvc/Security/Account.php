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
     * Default constructor
     *
     * @param int $id
     * @param string $username
     * @param string $password
     */
    public function __construct($id, $username, $password)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }
}
