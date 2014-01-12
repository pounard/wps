<?php

namespace Wps\Media\Type;

use Smvc\Core\AbstractContainerAware;
use Smvc\Core\ContainerAwareInterface;

class TypeFactory extends AbstractContainerAware
{
    static private $registered = array(
        'null' => '\Wps\Media\Type\UnknownType',
    );

    /**
     * Allow external code to register classes
     *
     * @param string $name
     * @param string $class
     */
    static public function register($class, $name = null)
    {
        if (!class_exists($class)) {
            trigger_error(sprintf("Class '%s' does not exist", $class));
        } else {
            if (null === $name) {
                $name = md5($class); // Predictible and fast
            }
            self::$registered[$name] = $class;
        }
    }

    /**
     * @var TypeInterface[]
     */
    private $instances;

    /**
     * Get instance
     *
     * @return TypeInterface
     */
    public function getInstance($name)
    {
        if (!isset($this->instances[$name])) { // Flyweight pattern
            if (!isset(self::$registered[$name])) { // Fallback
                $instance = new UnknownType();
            } else {
                $instance = new self::$registered[$name]();
            }
            if ($instance instanceof ContainerAwareInterface) {
                $instance->setContainer($this->getContainer());
            }
            $this->instances[$name] = $instance;
        }

        return $this->instances[$name];
    }
}
