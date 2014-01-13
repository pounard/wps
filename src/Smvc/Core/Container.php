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
     * Get raw service from the internal container by name
     *
     * @param string $name
     */
    public function get($name)
    {
        return $this->container[$name];
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
     * Get database connection
     *
     * @return \PDO
     */
    public function getDatabase($target = 'default')
    {
        return $this->container['db.' . $target];
    }

    /**
     * Get DAO
     *
     * @param string $key
     *
     * @return \Smvc\Model\Persistence\DaoInterface
     */
    public function getDao($name)
    {
        return $this->container['dao.' . $name];
    }

    /**
     * Get factory
     *
     * @param string $name
     *
     * @return \Smvc\Plugin\FactoryInterface
     */
    public function getFactory($name)
    {
       return $this->container['factory.' . $name];
    }

    /**
     * Get mail reader
     *
     * @return \Smvc\View\Helper\TemplateFactory
     */
    public function getTemplateFactory()
    {
       return $this->container['factory.template'];
    }

    /**
     * Get model object factory
     *
     * @return \Smvc\Model\Factory\DefaultFactory
     */
    public function getModelFactory()
    {
        return $this->container['factory.model'];
    }

    /**
     * Get account provider
     *
     * @return \Smvc\Security\AccountProviderInterface
     */
    public function getAccountProvider()
    {
        return $this->container['accountprovider'];
    }

    /**
     * Get filter factory
     *
     * @return \Smvc\View\Helper\FilterFactory
     */
    public function getFilterFactory()
    {
        return $this->container['factory.filter'];
    }
}
