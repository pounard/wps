<?php

namespace Wps;

use Wps\Media\Persistence\MediaDao;
use Wps\Media\Persistence\AlbumDao;

use Smvc\Core\Container;
use Smvc\View\Helper\TemplateFactory;
use Wps\Media\Type\TypeFactory;

class Application
{
    public function bootstrap(array $config, Container $container)
    {
        // @todo Find a better way
        TemplateFactory::register('\Wps\View\Helper\Template\Media', 'media');
    }
}
