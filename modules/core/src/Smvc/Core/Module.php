<?php

namespace Smvc\Core;

class Module
{
    private $name;

    private $namespace;

    private $path;

    /**
     * Default constructor
     *
     * @param string $name
     * @param string $path
     * @param string $namespace
     */
    public function __construct($name, $path, $namespace = null)
    {
        $this->name = $name;
        $this->path = $path;
        $this->namespace = $namespace;
    }

    /**
     * Get module configuration to be merged with global one
     *
     * You can override this method and return anything
     *
     * @return array
     *   Or NULL if no configuration
     */
    public function getConfig()
    {
        if ($config = @include $this->getPath() . '/etc/config.php') {
            return $config;
        }
    }

    /**
     * Get module internal name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get module internal name
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Get module path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    public function bootstrap(array $config, ApplicationInterface $application)
    {
    }
}
