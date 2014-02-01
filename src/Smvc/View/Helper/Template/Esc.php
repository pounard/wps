<?php

namespace Smvc\View\Helper\Template;

use Smvc\Core\AbstractApplicationAware;
use Smvc\Core\ApplicationInterface;
use Smvc\View\Helper\FilterInterface;

class Esc extends AbstractApplicationAware
{
    /**
     * @var FilterInterface
     */
    private $plainFilter;

    /**
     * @var FilterInterface
     */
    private $xssFilter;

    public function setApplication(ApplicationInterface $app)
    {
        parent::setApplication($app);

        $this->plainFilter = $app->getFilterFactory()->getFilter("secure");
        $this->xssFilter = $app->getFilterFactory()->getFilter("html");
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
