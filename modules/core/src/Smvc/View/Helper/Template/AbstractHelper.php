<?php

namespace Smvc\View\Helper\Template;

use Smvc\Core\AbstractApplicationAware;
use Smvc\Core\ApplicationInterface;
use Smvc\Dispatch\RequestInterface;
use Smvc\View\Helper\TemplateFactory;

/**
 * Base implementation for view helpers that makes them able to use other
 * template helpers
 */
class AbstractHelper extends AbstractApplicationAware
{
    /**
     * @var TemplateFactory
     */
    protected $factory;

    public function setApplication(ApplicationInterface $app)
    {
        parent::setApplication($app);

        $this->factory = $app->getTemplateFactory();
    }

    /**
     * Call another helper
     */
    public function __call($name , array $arguments)
    {
        return call_user_func_array($this->factory->getInstance($name), $arguments);
    }
}
