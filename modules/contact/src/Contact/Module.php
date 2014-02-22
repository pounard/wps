<?php

namespace Contact;

use Smvc\Core\ApplicationInterface;
use Smvc\Core\Module as BaseModule;

class Module extends BaseModule
{
    public function bootstrap(array $config, ApplicationInterface $application)
    {
        $application
            ->getFactory('template')
            ->register(
                'contact',
                '\Contact\View\Helper\Template\Contact'
            );
    }
}
