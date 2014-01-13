<?php

namespace Smvc\View\Helper\Template;

use Smvc\Core\AbstractContainerAware;
use Smvc\Core\Container;
use Smvc\View\Helper\FilterInterface;

class Esc extends AbstractContainerAware
{
    /**
     * @var FilterInterface
     */
    private $plainFilter;

    /**
     * @var FilterInterface
     */
    private $xssFilter;

    public function setContainer(Container $container)
    {
        parent::setContainer($container);

        $this->plainFilter = $container->getFilterFactory()->getFilter("secure");
        $this->xssFilter = $container->getFilterFactory()->getFilter("html");
    }

    public function __invoke($text, $plain = false)
    {
        if (empty($text)) {
            return '';
        }
        if ($plain) {
            return $this->plainFilter->filter($text);
        } else {
            return $this->xssFilter->filter($text);
        }
    }
}
