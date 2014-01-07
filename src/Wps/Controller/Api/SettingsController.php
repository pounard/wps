<?php

namespace Wps\Controller\Api;

use Smvc\Controller\AbstractController;
use Smvc\Dispatch\RequestInterface;
use Smvc\View\View;

class SettingsController extends AbstractController
{
    public function getAction(RequestInterface $request, array $args)
    {
        $container = $this->getContainer();

        return new View($container->getConfig()->toArray());
    }
}
