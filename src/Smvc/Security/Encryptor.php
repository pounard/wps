<?php

namespace Smvc\Security;

class Encryptor
{
    /**
     * Create a non predictable but reproductible hash from the given string
     *
     * @return string
     */
    public function hash($string)
    {
        // FIXME SERIOUSLY
        // THIS IS NOT SECURE AND SHOULD USE A PRIVATE KEY!!!
        // PER USER WOULD BE EVEN BETTER.
        return md5($string);
    }

    /**
     * Get a hashed path for a file
     *
     * @return string
     */
    public function getPath($path)
    {
        
    }
}
