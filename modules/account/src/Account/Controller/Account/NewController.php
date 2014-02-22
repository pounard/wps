<?php

namespace Account\Controller\Account;

use Account\Security\Crypt;

use Smvc\Controller\AbstractController;
use Smvc\Core\Message;
use Smvc\Dispatch\Http\RedirectResponse;
use Smvc\Dispatch\RequestInterface;
use Smvc\Error\LogicError;
use Smvc\Error\NotFoundError;
use Smvc\Security\Account;
use Smvc\View\View;

class NewController extends AbstractController
{
    public function isAuthorized(RequestInterface $request, array $args)
    {
        $app = $this->getApplication();
        $config = $app->getConfig();

        return !$app->getSession()->isAuthenticated() && $config['account/user_register'];
    }

    public function getAction(RequestInterface $request, array $args)
    {
        return new View(array(), 'account/new');
    }

    public function postAction(RequestInterface $request, array $args)
    {
        $content = $request->getContent();
        $app = $this->getApplication();
        $accountProvider = $app->getAccountProvider();
        $messager = $app->getMessager();
        $hasErrors = false;

        // Check for mail being unique
        if (empty($content['mail'])) {
            $messager->addMessage("Mail address cannot be empty", Message::TYPE_ERROR);
            $hasErrors = true;
        } else {
            try {
                if ($account = $accountProvider->getAccount($content['mail'])) {
                    $messager->addMessage("Mail address is already registered", Message::TYPE_ERROR);
                    $hasErrors = true;
                }
            } catch (NotFoundError $e) {
                // That's ok user does not exists.
            }
        }

        // Check for password and confirmation being identical
        if (empty($content['passwordNew']) || empty($content['passwordConfirm'])) {
            $messager->addMessage("Password and confirmation cannot be empty", Message::TYPE_ERROR);
            $hasErrors = true;
        } else if ($content['passwordNew'] !== $content['passwordConfirm']) {
            $messager->addMessage("Password and confirmation must be identical", Message::TYPE_ERROR);
            $hasErrors = true;
        }

        if ($hasErrors) {
            return new View(array(), 'account/new');
        }

        $account = $accountProvider->createAccount(
            $content['mail'],
            $content['displayName'],
            true, /* Set to false once mails are OK */
            null /* Set validate token */
        );
        $accountProvider->setAccountPassword(
            $account->getId(),
            $content['passwordNew'],
            Crypt::createSalt()
        );

        $messager->addMessage("A validation email has been sent and will arrive shortly, please do follow instructions provided", Message::TYPE_SUCCESS);

        return new RedirectResponse('account/login');
    }
}
