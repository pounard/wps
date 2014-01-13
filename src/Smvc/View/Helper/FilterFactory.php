<?php

namespace Smvc\View\Helper;

use Smvc\Core\AbstractContainerAware;
use Smvc\Core\ContainerAwareInterface;
use Smvc\Plugin\FactoryInterface;
use Smvc\View\Helper\Filter\NullFilter;

class FilterFactory extends AbstractContainerAware implements FactoryInterface
{
    static private $registered = array(
        'autop'   => '\Smvc\View\Helper\Filter\AutoParagraph',
        'htmlesc' => '\Smvc\View\Helper\Filter\HtmlEncode',
        'lntohr'  => '\Smvc\View\Helper\Filter\StupidLinesToHr',
        'lntovd'  => '\Smvc\View\Helper\Filter\StupidLinesToVoid',
        'null'    => '\Smvc\View\Helper\Filter\NullFilter',
        'strip'   => '\Smvc\View\Helper\Filter\Strip',
        'urltoa'  => '\Smvc\View\Helper\Filter\UrlToLink',
        'urltou'  => '\Smvc\View\Helper\Filter\UrlToUrl',
    );

    /**
     * Allow external code to register filter classes
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
                $name = md5($class); // Predictible
            }
            self::$registered[$name] = $class;
        }
    }

    /**
     * @var FilterInterface[]
     */
    private $instances;

    /**
     * @var FitlerInterface[]
     */
    private $filters = array();

    public function isSupported($name)
    {
        return isset(self::$registered[$name]);
    }

    public function getInstance($name)
    {
        if (!isset($this->instances[$name])) { // Flyweight pattern
            if (!isset(self::$registered[$name])) { // Fallback
                $instance = new NullFilter();
            } else {
                $instance = new self::$registered[$name]();
            }
            if ($instance instanceof ContainerAwareInterface) {
               $instance->setContainer($container);
            }
            $this->instances[$name] = $instance;
        }

        return $this->instances[$name];
    }

    /**
     * Get a filter collection using the given filter types
     *
     * @param array $types
     *   Ordered array of filter types
     *
     * @return FilterCollection
     */
    private function getCollectionFrom(array $types)
    {
        foreach ($types as $index => $type) {
            $types[$index] = $this->getInstance($type);
        }

        return new FilterCollection($types);
    }

    /**
     * Get filter for the given text type
     *
     * @param string $type
     *
     * @return FilterInterface
     */
    public function getFilter($type)
    {
        if (isset($this->filters[$type])) {
            return $this->filters[$type];
        }

        // Fetch type configuration
        $config = $this->getContainer()->getConfig();
        $key = 'filters/' . $type;
        if (isset($config[$key])) {
            $types = $config[$key];
        } else {
            $types = array('strip'); // Default must be secure
        }

        return $this->filters[$type] = $this->getCollectionFrom($types);
    }
}
