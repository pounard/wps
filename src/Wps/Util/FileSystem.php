<?php

namespace Wps\Util;

/**
 * File system high level abstraction
 */
class FileSystem
{
    /**
     * Create directory
     *
     * @todo I am stupid and I could have used recursive mkdir directly
     *
     * @param string $path
     */
    static public function createDirectory($path)
    {
        $current = null;
        foreach (explode('/', $path) as $segment) {
            if (null === $current) {
                if (!mkdir($segment)) {
                    throw new \RuntimeException(sprintf("Could not create directory '%s'", $segment));
                }
                $current = $segment;
            } else {
                $current .= '/' . $segment;
                if (!mkdir($current)) {
                    throw new \RuntimeException(sprintf("Could not create directory '%s'", $current));
                }
            }
        }
    }

    /**
     * Ensure directory exists
     *
     * @param string $path
     *   Directory path
     * @param boolean $writable
     *   Should it be writable
     * @param boolean $create
     *   Should we create it if it does not exist
     */
    static public function ensureDirectory($path, $writable = false, $create = false)
    {
        if (!is_dir($path)) {
            if ($create) {
                self::createDirectory($path);
            } else {
                throw new \RuntimeException(sprintf("Directory does not exists '%s'", $path));
            }
        }
        if ($writable) {
            if (!is_writable($path)) {
                throw new \RuntimeException(sprintf("Directory is not writable '%s'", $path));
            }
        } else if (!is_readable($path)) {
            throw new \RuntimeException(sprintf("Directory is not readable '%s'", $path));
        }
    }
}
