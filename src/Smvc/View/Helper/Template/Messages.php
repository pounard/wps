<?php

namespace Smvc\View\Helper\Template;

use Smvc\Core\AbstractContainerAware;

class Messages extends AbstractContainerAware
{
    public function __invoke()
    {
        return $this
            ->getContainer()
            ->getMessager()
            ->getMessages();
    }
}
