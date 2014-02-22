<?php

namespace Smvc\Plugin;

use Smvc\Core\AbstractApplicationAware;
use Smvc\Core\ApplicationAwareInterface;

class DefaultFactory extends AbstractApplicationAware implements FactoryInterface
{
    private $registered = array();

    private $instances = array();

    private $nullInstance;

    public function createNullInstance()
    {
        return new NullPlugin();
    }

    public function register($name, $class)
    {
        if (!class_exists($class)) {
            trigger_error(sprintf("Class '%s' does not exist", $class));
        } else {
            if (null === $name) {
                $name = md5($class); // Predictible and fast
            }
            $this->registered[$name] = $class;
        }
    }

    public function registerAll($map)
    {
        foreach ($map as $name => $class) {
            $this->register($name, $class);
        }
    }

    public function isSupported($name)
    {
        return isset($this->registered[$name]);
    }

    public function getInstance($name)
    {
        if (!isset($this->instances[$name])) { // Flyweight pattern
            if (!isset($this->registered[$name])) { // Fallback
                if (null === $this->nullInstance) {
                    $this->nullInstance = $this->createNullInstance();
                }
                $instance = clone $this->nullInstance; // Prototype pattern
            } else {
                $instance = new $this->registered[$name]();
            }
            if ($instance instanceof ApplicationAwareInterface) {
                $instance->setApplication($this->getApplication());
            }
            $this->instances[$name] = $instance;
        }

        return $this->instances[$name];
    }
}
