<?php

namespace Wps\Security;

/**
 * Media security helper functions
 */
final class MediaSecurity
{
    /**
     * Secure a path by using the user account private key and site global
     * key as seeds
     *
     * @param string $path
     *   Original full path to filename to hash
     * @param string $suffix
     *   Arbitrary filename suffix
     * @param string $privateKey
     *   Private key to use for hashing
     * @param string $siteKey
     *   Site global private key for additional security
     *
     * @return string
     */
    static public function getSecurePath($path, $suffix = '', $privateKey = '', $siteKey = '')
    {
        // Keep the file name ext
        if ($pos = strrpos($path, '.')) {
            $ext = $suffix . substr($path, $pos);
        } else {
            $ext = $suffix;
        }

        // Use SHA512 because we wont URL to be long enough
        $path = base64_encode(hash_hmac('sha512', $path, $privateKey . $siteKey . "wps", true));

        return trim(preg_replace('/[^a-zA-Z0-9]{1,}/', '/', $path), "/") . $ext;
    }
}
