<?php

namespace Smvc\Plugin;

/**
 * Factory interface
 */
interface FactoryInterface
{
    /**
     * Is an instance registered with this name
     *
     * @param string $name
     */
    public function isSupported($name);

    /**
     * Get instance
     *
     * @param string $name
     *
     * @return TypeInterface
     */
    public function getInstance($name);
}
