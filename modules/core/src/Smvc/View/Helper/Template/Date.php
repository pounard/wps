<?php

namespace Smvc\View\Helper\Template;

use Smvc\Core\AbstractApplicationAware;
use Smvc\Core\ApplicationInterface;
use Smvc\View\Helper\FilterInterface;

class Date extends AbstractApplicationAware
{
    public function __invoke($date, $format = null)
    {
        if (!$date instanceof \DateTime) {
            return '';
        }
        if (empty($format)) {
            return ''; // @todo Default format depending on language
        }
        return $date->format($format);
    }
}
