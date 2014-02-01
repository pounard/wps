<?php

namespace Smvc\Core;

/**
 * Base application implementation
 */
class DefaultApplication implements ApplicationInterface
{
    /**
     * @var \Pimple
     */
    private $services;

    /**
     * @var array
     */
    private $parameters;

    /**
     * Default constructor
     */
    public function __construct(array $parameters = array())
    {
        $this->services = new \Pimple();
        $this->parameters = $parameters;
    }

    /**
     * Get raw service from the internal services registry by name
     *
     * @param string $name
     */
    public function get($name)
    {
        return $this->services[$name];
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
     * Get internal service registry
     *
     * @return \Pimple
     */
    public function getServiceRegistry()
    {
        return $this->services;
    }

    /**
     * Get configuration
     *
     * @return \ArrayAccess
     */
    public function getConfig()
    {
        return $this->services['config'];
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
        return $this->services['session'];
    }

    /**
     * Get mail reader
     *
     * @return \Smvc\Core\Messager
     */
    public function getMessager()
    {
        return $this->services['messager'];
    }

    /**
     * Get database connection
     *
     * @return \PDO
     */
    public function getDatabase($target = 'default')
    {
        return $this->services['db.' . $target];
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
        return $this->services['dao.' . $name];
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
       return $this->services['factory.' . $name];
    }

    /**
     * Get mail reader
     *
     * @return \Smvc\View\Helper\TemplateFactory
     */
    public function getTemplateFactory()
    {
       return $this->services['factory.template'];
    }

    /**
     * Get model object factory
     *
     * @return \Smvc\Model\Factory\DefaultFactory
     */
    public function getModelFactory()
    {
        return $this->services['factory.model'];
    }

    /**
     * Get account provider
     *
     * @return \Smvc\Security\AccountProviderInterface
     */
    public function getAccountProvider()
    {
        return $this->services['accountprovider'];
    }

    /**
     * Get filter factory
     *
     * @return \Smvc\View\Helper\FilterFactory
     */
    public function getFilterFactory()
    {
        return $this->services['factory.filter'];
    }
}
