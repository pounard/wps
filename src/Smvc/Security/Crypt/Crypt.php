<?php

namespace Smvc\Security\Crypt;

class Crypt
{
    /**
     * Create a non predictable but reproductible hash from the given string
     *
     * @return string
     */
    static public function hash($string)
    {
        // FIXME SERIOUSLY
        // THIS IS NOT SECURE AND SHOULD USE A PRIVATE KEY!!!
        // PER USER WOULD BE EVEN BETTER.
        return md5($string);
    }

    /**
     * Get password hash
     *
     * @param string $password
     * @param string $salt
     */
    static public function getPasswordHash($password, $salt = null)
    {
        $options = array();
        if (null !== $salt) {
            $options['salt'] = $salt;
        }

        return password_hash($password, PASSWORD_BCRYPT, $options);
    }

    /**
     * Encrypt data
     *
     * @param string $text
     * @param string $key
     *
     * @return string
     */
    static public function encrypt($text, $key = null)
    {
        throw new \NotImplementedError();
    }

    /**
     * Decrypt data
     *
     * @param string $text
     * @param string $key
     *
     * @return string
     */
    static public function decrypt($text, $key = null)
    {
        throw new \NotImplementedError();
    }

    /**
     * Generate a new public/private key pairs
     *
     * @return string[]
     *   First value is private key, second value is public key, third is key type
     */
    static public function generateRsaKeys()
    {
        $config = array(
            "digest_alg" => "sha512",
            "private_key_bits" => 4096,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );

        $privKey = '';
        $pubKey = '';
        $type = 'rsa';

        // Create the private and public key
        $res = openssl_pkey_new($config);

        // Extract the private key
        openssl_pkey_export($res, $privKey);

        // Extract the public key
        $pubKey = openssl_pkey_get_details($res);

        return array($privKey, $pubKey['key'], $type);
    }
}
