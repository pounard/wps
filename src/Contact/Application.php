<?php

namespace Contact;

use Smvc\Core\ApplicationInterface;
use Smvc\View\Helper\TemplateFactory;

class Application
{
    public function bootstrap(array $config, ApplicationInterface $application)
    {
        // @todo Find a better way
        TemplateFactory::register('\Contact\View\Helper\Template\Contact', 'contact');
    }
}
