<?php

namespace Smvc\View\Helper\Template;

use Smvc\Core\AbstractContainerAware;
use Smvc\Core\Container;
use Smvc\Dispatch\RequestInterface;
use Smvc\View\Helper\TemplateFactory;

/**
 * Base implementation for view helpers that makes them able to use other
 * template helpers
 */
class AbstractHelper extends AbstractContainerAware
{
    /**
     * @var TemplateFactory
     */
    protected $factory;

    public function setContainer(Container $container)
    {
        parent::setContainer($container);

        $this->factory = $container->getTemplateFactory();
    }

    /**
     * Call another helper
     */
    public function __call($name , array $arguments)
    {
        return call_user_func_array($this->factory->getInstance($name), $arguments);
    }
}
