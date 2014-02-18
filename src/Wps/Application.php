<?php

namespace Wps;

use Wps\Session\DatabaseSessionHandler;

use Smvc\Core\ApplicationInterface;
use Smvc\View\Helper\TemplateFactory;

class Application
{
    public function bootstrap(array $config, ApplicationInterface $application)
    {
        $handler = new DatabaseSessionHandler($application->getDatabase());

        if (PHP_VERSION < '5.4') {
            session_set_save_handler (
                array($handler, 'open'),
                array($handler, 'close'),
                array($handler, 'read'),
                array($handler, 'write'),
                array($handler, 'destroy'),
                array($handler, 'gc')
            );
            register_shutdown_function('session_write_close');
        } else {
            session_set_save_handler($handler, true);
        }

        // @todo Find a better way
        TemplateFactory::register('\Wps\View\Helper\Template\Media', 'media');
        TemplateFactory::register('\Wps\View\Helper\Template\MediaGrid', 'mediaGrid');
    }
}
