<?php

namespace Smvc\View\Helper\Filter;

use Smvc\View\Helper\FilterInterface;

class NullFilter implements FilterInterface
{
    public function filter($text, $charset = null)
    {
        return $text;
    }
}
