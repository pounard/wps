<?php

namespace Wps;

use Wps\Media\Persistence\MediaDao;
use Wps\Media\Persistence\AlbumDao;

use Smvc\Core\Container;
use Smvc\View\Helper\TemplateFactory;

class Application
{
    public function bootstrap(array $config, Container $container)
    {
        $internal = $container->getInternalContainer();

        $internal['dao.media'] = function () use ($container) {
            $instance = new MediaDao();
            $instance->setContainer($container);
            return $instance;
        };
        $internal['dao.album'] = function () use ($container) {
            $instance = new AlbumDao();
            $instance->setContainer($container);
            return $instance;
        };

        // @todo Find a better way
        TemplateFactory::register('\Wps\View\Helper\Template\Media', 'media');
    }
}
