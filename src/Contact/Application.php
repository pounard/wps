<?php

namespace Contact;

use Smvc\Core\ApplicationInterface;

class Application
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
