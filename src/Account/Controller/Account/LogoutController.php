<?php

namespace Account\Controller\Account;

use Smvc\Controller\AbstractController;
use Smvc\Dispatch\RequestInterface;
use Smvc\View\View;
use Smvc\Dispatch\Http\RedirectResponse;

class LogoutController extends AbstractController
{
    public function getAction(RequestInterface $request, array $args)
    {
        $app = $this->getApplication();
        $app->getSession()->destroy();
        $app->getMessager()->addMessage("See you later!");

        return new RedirectResponse('account/login');
    }
}
