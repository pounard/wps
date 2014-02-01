<?php

namespace Account\Controller\Account;

use Smvc\Controller\AbstractController;
use Smvc\Core\Message;
use Smvc\Dispatch\Http\RedirectResponse;
use Smvc\Dispatch\RequestInterface;
use Smvc\Form\Form;
use Smvc\View\View;

class SettingsController extends AbstractController
{
    private function getForm()
    {
        $container = $this->getContainer();
        $config = $container->getConfig();
        $account = $container->getSession()->getAccount();

        $form = new Form();

        $form->addElement(array(
            'name'        => 'displayName',
            'default'     => $account->getDisplayName(),
            'placeholder' => $account->getUsername(),
        ));

        return $form;
    }

    public function getAction(RequestInterface $request, array $args)
    {
        $form = $this->getForm();

        return new View(array(
            'defaults' => $form->getDefaultValues(),
            'placeholders' => $form->getPlaceHolders(),
        ), 'account/settings');
    }

    public function postAction(RequestInterface $request, array $args)
    {
        $form = $this->getForm();
        $form->setValues($request->getContent());

        if ($form->validate($form->getValues())) {

            /*
            $container = $this->getContainer();
            $config = $container->getConfig();
            $account = $container->getSession()->getAccount();
             */

            $this
                ->getContainer()
                ->getMessager()
                ->addMessage("Your account informations have been saved", Message::TYPE_SUCCESS);

            return new RedirectResponse($request->getResource());

        } else {
            $messager = $this->getContainer()->getMessager();
            if ($messages = $form->getValidationMessages()) {
                foreach ($messages as $message) {
                    $messager->addMessage($message, Message::TYPE_ERROR);
                }
            } else {
                $messager->addMessage("Validation errors", Message::TYPE_ERROR);
            }

            return new View(array(
                'defaults' => $form->getValues(),
                'placeholders' => $form->getPlaceHolders(),
            ), 'account/settings');
        }
    }
}
