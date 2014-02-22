<?php

namespace Smvc\View;

use Smvc\Error\LogicError;
use Smvc\Error\TechnicalError;
use Smvc\View\Helper\TemplateFactory;

class Template
{
    /**
     * @var View
     */
    protected $view;

    /**
     * @var TemplateFactory
     */
    protected $helpers;

    /**
     * @var TemplateResolver
     */
    protected $resolver;

    /**
     * Default constructor
     *
     * @param View $view
     * @param TemplateFactory $helpers
     */
    public function __construct(View $view, TemplateFactory $helpers, TemplateResolver $resolver)
    {
        $this->view = $view;
        $this->helpers = $helpers;
        $this->resolver = $resolver;
    }

    /**
     * Allow templates to use helpers
     *
     * @param string $name
     * @param array $arguments
     */
    public function __call($name , array $arguments)
    {
        $ret = call_user_func_array($this->helpers->getInstance($name), $arguments);

        if (is_string($ret)) {
            return $ret;
        } else if ($ret instanceof View) {
            $template = new self($ret, $this->helpers, $this->resolver);
            return $template->render();
        } else { // Prey for it to work
            return $ret;
        }
    }

    /**
     * Render template and fetch output
     *
     * @param mixed $values
     * @param string $template
     */
    public function render()
    {
        if (!$name = $this->view->getTemplate()) {
            $name = 'core/debug';
        }
        if (!$path = $this->resolver->findTemplate($name)) {
            throw new LogicError(sprintf("Could not find template '%s'", $name));
        }

        ob_start();
        extract($this->view->getValues());

        if (!(bool)include $path) {
            ob_flush(); // Never leave an opened resource

            throw new LogicError(sprintf("Could not find template '%s'", $template));
        }

        return ob_get_clean();
    }

    public function __toString()
    {
        return $this->render();
    }
}
