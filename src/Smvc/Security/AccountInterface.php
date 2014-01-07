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
}
