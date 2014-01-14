<?php

namespace Wps;

use Smvc\Core\Container;
use Smvc\View\Helper\TemplateFactory;

class Application
{
    public function bootstrap(array $config, Container $container)
    {
        // @todo Find a better way
        TemplateFactory::register('\Wps\View\Helper\Template\Media', 'media');
    }
}
