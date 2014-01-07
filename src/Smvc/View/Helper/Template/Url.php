<?php

namespace Smvc\View\Helper\Template;

use Smvc\Core\AbstractContainerAware;

class Url extends AbstractContainerAware
{
    public function __invoke($path = null, array $args = null)
    {
        // FIXME: Base path here
        return '/' . $path;
    }
}
