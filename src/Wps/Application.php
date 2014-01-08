<?php

namespace Wps;

use Smvc\Core\Container;
use Wps\Media\Persistence\MediaDao;
use Wps\Media\Persistence\AlbumDao;

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
    }
}
