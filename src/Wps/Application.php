<?php

namespace Wps;

use Wps\Session\DatabaseSessionHandler;

use Smvc\Core\ApplicationInterface;
use Smvc\View\Helper\TemplateFactory;

class Application
{
    public function bootstrap(array $config, ApplicationInterface $application)
    {
        session_set_save_handler(
            new DatabaseSessionHandler(
                $application->getDatabase()
            ),
            true
        );

        // @todo Find a better way
        TemplateFactory::register('\Wps\View\Helper\Template\Media', 'media');
    }
}
