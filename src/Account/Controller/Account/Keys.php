<?php

namespace Account\Controller\Account;

use Smvc\Controller\AbstractController;
use Smvc\Core\Message;
use Smvc\Dispatch\Http\RedirectResponse;
use Smvc\Dispatch\RequestInterface;
use Smvc\Error\LogicError;
use Smvc\Security\Account;
use Smvc\View\View;

class KeysController extends AbstractController
{
    public function getAction(RequestInterface $request, array $args)
    {
        return new View(array(), 'account/keys');
    }

    public function postAction(RequestInterface $request, array $args)
    {
        $content = $request->getContent();
        $container = $this->getContainer();
        $session = $container->getSession();
        $account = $session->getAccount();

        $current = $content['password_current'];
        $new = $content['password_new'];
        $confirm = $content['password_confirm'];

        if ($new !== $confirm) {
            $container->getMessager()->addMessage("Confirmation does not matches the new password", Message::TYPE_ERROR);

            return new RedirectResponse($request->getResource());
        }

        if ($container->getAccountProvider()->authenticate($account->getUsername(), $current)) {
            $session->getAccountProvider()->setAccountPassword($account->getId(), $new);

            // @todo

            $container->getMessager()->addMessage("Your password has been changed", Message::TYPE_SUCCESS);

            return new RedirectResponse();

        } else {
            // Bouh! Wrong credentials.
            $container->getMessager()->addMessage("Unable to authenticate, please check your password", Message::TYPE_ERROR);

            // Redirect to the very same page but using GET
            return new RedirectResponse($request->getResource());
        }
    }
}
