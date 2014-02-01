<?php

namespace Wps;

use Smvc\Core\ApplicationInterface;
use Smvc\View\Helper\TemplateFactory;

class Application
{
    public function bootstrap(array $config, ApplicationInterface $application)
    {
        // @todo Find a better way
        TemplateFactory::register('\Wps\View\Helper\Template\Media', 'media');
    }
}
