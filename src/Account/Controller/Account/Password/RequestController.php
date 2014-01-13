<?php

namespace Account\Controller\Account\Password;

use Smvc\Controller\AbstractController;
use Smvc\Core\Message;
use Smvc\Dispatch\Http\RedirectResponse;
use Smvc\Dispatch\RequestInterface;
use Smvc\Security\Account;
use Smvc\Security\Crypt\Crypt;
use Smvc\View\View;

class RequestController extends AbstractController
{
    public function isAuthorized(RequestInterface $request, array $args)
    {
        return !$this
            ->getContainer()
            ->getSession()
            ->isAuthenticated();
    }

    public function getAction(RequestInterface $request, array $args)
    {
        return new View(array(), 'account/password/request');
    }

    public function postAction(RequestInterface $request, array $args)
    {
        $container = $this->getContainer();
        $messager = $container->getMessager();
        $values = $request->getContent();

        if (empty($values) || empty($values['mail'])) {
            $messager->addMessage("You must type in your mail address", Message::TYPE_ERROR);

            return new RedirectResponse($request->getResource());
        }
        if ($values['mail'] !== $values['mail_confirm']) {
            $messager->addMessage("Both mail addresses do not match", Message::TYPE_ERROR);

            return new RedirectResponse($request->getResource());
        }

        try {
            $accountProvider = $container->getAccountProvider();
            $account = $accountProvider->getAccount($values['mail']);
            $password = Crypt::createPassword();

            // @todo Send password via mail
            $accountProvider
                ->setAccountPassword(
                    $account->getId(),
                    $password,
                    Crypt::createSalt()
                );

            $messager->addMessage("You new password is: " . $password, Message::TYPE_INFO);

        } catch (\Exception $e) {
            // Never leave indications to hackers so just ignore errors
            // silently and let the success message appear
        }

        $messager->addMessage("A mail has been sent to your mail address", Message::TYPE_SUCCESS);

        return new RedirectResponse('account/login');
    }
}
