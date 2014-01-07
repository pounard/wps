<?php

namespace Smvc\Error;

/**
 * Configuration error
 */
class ConfigError extends \RuntimeException implements Error
{
    public function __construct($message = null, $code = null, $previous = null)
    {
        if (null === $code) {
            $code = 500;
        }

        parent::__construct($message, $code, $previous);
    }
}
