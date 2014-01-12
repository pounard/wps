<?php

namespace Smvc\View\Helper;

use Smvc\Core\AbstractContainerAware;
use Smvc\Core\ContainerAwareInterface;
use Smvc\View\Helper\Template\NullHelper;

class TemplateFactory extends AbstractContainerAware
{
    static private $registered = array(
        'url'      => '\Smvc\View\Helper\Template\Url',
        'messages' => '\Smvc\View\Helper\Template\Messages',
        'null'     => '\Smvc\View\Helper\Template\NullHelper',
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
     * @var FilterInterface[]
     */
    private $instances;

    /**
     * Get filter instance
     */
    public function getInstance($name)
    {
        if (!isset($this->instances[$name])) { // Flyweight pattern
            if (!isset(self::$registered[$name])) { // Fallback
                $instance = new NullHelper();
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
