<?php

namespace Account\Controller\Account;

use Smvc\Controller\AbstractController;
use Smvc\Core\Message;
use Smvc\Dispatch\Http\RedirectResponse;
use Smvc\Dispatch\RequestInterface;
use Smvc\Error\LogicError;
use Smvc\Security\Account;
use Smvc\View\View;

class LoginController extends AbstractController
{
    public function isAuthorized(RequestInterface $request, array $args)
    {
        return !$this
            ->getApplication()
            ->getSession()
            ->isAuthenticated();
    }

    public function getAction(RequestInterface $request, array $args)
    {
        return new View(array(), 'account/login');
    }

    public function postAction(RequestInterface $request, array $args)
    {
        $content = $request->getContent();
        $app = $this->getApplication();
        $accountProvider = $app->getAccountProvider();

        if ($accountProvider->authenticate($content['username'], $content['password'])) {
            // Yeah! Success.
            if (!$app->getSession()->regenerate($content['username'])) {
                $app->getMessager()->addMessage("Could not create your session", Message::TYPE_ERROR);
                throw new LogicError("Could not create session");
            }
            $app->getMessager()->addMessage("Welcome back!", Message::TYPE_SUCCESS);

            return new RedirectResponse();

        } else {
            // Bouh! Wrong credentials.
            $app->getMessager()->addMessage("Unable to login, please check your account name and password", Message::TYPE_ERROR);

            // Redirect to the very same page but using GET
            return new RedirectResponse($request->getResource());
        }
    }
}
