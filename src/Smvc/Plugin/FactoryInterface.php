<?php

namespace Smvc\Plugin;

/**
 * Factory interface
 */
interface FactoryInterface
{
    /**
     * Register a single plugin manually
     *
     * @param string $name
     * @param string $class
     */
    public function register($name, $class);

    /**
     * Register a set of plugins
     *
     * @param string[] $map
     *   Class names hashmap keyed by names
     */
    public function registerAll($map);

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
