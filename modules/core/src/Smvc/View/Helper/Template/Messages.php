<?php

namespace Smvc\View\Helper\Template;

use Smvc\Core\AbstractApplicationAware;

class Messages extends AbstractApplicationAware
{
    public function __invoke()
    {
        return $this
            ->getApplication()
            ->getMessager()
            ->getMessages();
    }
}
