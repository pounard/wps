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
     * @param string $privateKey
     * @param string $siteKey
     *
     * @return string
     */
    public function getSecurePath($path, $privateKey = null, $siteKey = null)
    {
        
    }
}
