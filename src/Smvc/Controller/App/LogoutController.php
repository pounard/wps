<?php

namespace Smvc\Controller\App;

use Smvc\Controller\AbstractController;
use Smvc\Dispatch\RequestInterface;
use Smvc\View\View;
use Smvc\Dispatch\Http\RedirectResponse;

class LogoutController extends AbstractController
{
    public function getAction(RequestInterface $request, array $args)
    {
        $container = $this->getContainer();
        $container->getSession()->destroy();
        $container->getMessager()->addMessage("See you later!");

        return new RedirectResponse('app/login');
    }
}
