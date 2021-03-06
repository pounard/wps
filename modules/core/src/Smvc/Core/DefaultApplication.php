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
     * @var Module[]
     */
    private $modules;

    /**
     * Default constructor
     */
    public function __construct(array $parameters = array())
    {
        $this->services = new \Pimple();
        $this->parameters = $parameters;
    }

    public function setModules($modules)
    {
        $this->modules = $modules;
    }

    public function getModules()
    {
        return $this->modules;
    }

    public function getModule($name)
    {
        if (isset($this->modules[$name])) {
            return $this->modules[$name];
        }
    }

    public function get($name)
    {
        return $this->services[$name];
    }

    public function getParameter($name, $default)
    {
        // FIXME: Rework this
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

    public function getConfig()
    {
        return $this->services['config'];
    }

    public function getDefaultCharset()
    {
        $config = $this->getConfig();

        return $config['charset'];
    }

    public function getSession()
    {
        return $this->services['session'];
    }

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

    public function getFactory($name)
    {
       return $this->services['factory.' . $name];
    }

    /**
     * Get factory item
     *
     * @param string $name
     * @param string $key
     */
    public function getFactoryItem($name, $key)
    {
        return $this->getFactory($name)->getInstance($key);
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
