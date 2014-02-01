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
        return true;
    }

    public function getAction(RequestInterface $request, array $args)
    {
        return new View(array(), 'account/password/request');
    }

    public function postAction(RequestInterface $request, array $args)
    {
        $app = $this->getApplication();
        $messager = $app->getMessager();
        $session = $app->getSession();
        $values = $request->getContent();

        if (empty($values) || empty($values['mail'])) {
            $messager->addMessage("You must type in your mail address", Message::TYPE_ERROR);

            return new RedirectResponse($request->getResource());
        }
        if ($values['mail'] !== $values['mail_confirm']) {
            $messager->addMessage("Both mail addresses do not match", Message::TYPE_ERROR);

            return new RedirectResponse($request->getResource());
        }

        if ($session->isAuthenticated()) {
            // Validate current logged in user has inputed the right mail
            // adress and reject request if the mail is not the same
            if ($session->getAccount()->getUsername() !== $values['mail']) {
                $messager->addMessage("I am sorry sir but you did not write your own mail address!", Message::TYPE_ERROR);

                return new RedirectResponse($request->getResource());
            }
        }

        try {
            $accountProvider = $app->getAccountProvider();
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

        if ($session->isAuthenticated()) {
            return new RedirectResponse('account');
        } else {
            return new RedirectResponse('account/login');
        }
    }
}
