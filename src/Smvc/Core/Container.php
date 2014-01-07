<?php

namespace Smvc\Core;

/**
 * Main service container
 */
class Container
{
    /**
     * @var \Pimple
     */
    private $container;

    /**
     * @var array
     */
    private $parameters;

    /**
     * Default constructor
     */
    public function __construct(array $parameters = array())
    {
        $this->container = new \Pimple();
        $this->parameters = $parameters;
    }

    /**
     * Get parameter value
     *
     * FIXME: Rework this
     *
     * @param string $name
     * @param mixed $default
     */
    public function getParameter($name, $default)
    {
        if (array_key_exists($name, $this->parameters)) {
            return $this->parameters[$name];
        }
        return $default;
    }

    /**
     * Get internal container
     *
     * @return \Pimple
     */
    public function getInternalContainer()
    {
        return $this->container;
    }

    /**
     * Get configuration
     *
     * @return \ArrayAccess
     */
    public function getConfig()
    {
        return $this->container['config'];
    }

    /**
     * Get default charset
     */
    public function getDefaultCharset()
    {
        $config = $this->getConfig();

        return $config['charset'];
    }

    /**
     * Get session
     *
     * @return \Smvc\Core\Session
     */
    public function getSession()
    {
        return $this->container['session'];
    }

    /**
     * Get mail reader
     *
     * @return \Smvc\Core\Messager
     */
    public function getMessager()
    {
        return $this->container['messager'];
    }

    /**
     * Get mail reader
     *
     * @return \Smvc\View\Helper\TemplateFactory
     */
    public function getTemplateFactory()
    {
       return $this->container['templatefactory'];
    }

    /**
     * Get model object factory
     *
     * @return \Smvc\Model\Factory\DefaultFactory
     */
    public function getModelFactory()
    {
        return $this->container['modelfactory'];
    }

    /**
     * Get filter factory
     *
     * @return \Smvc\View\Helper\FilterFactory
     */
    public function getFilterFactory()
    {
        return $this->container['filterfactory'];
    }
}
