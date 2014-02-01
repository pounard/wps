<?php

namespace Wps\Controller\App;

use Smvc\Controller\AbstractController;
use Smvc\Core\Message;
use Smvc\Dispatch\Http\RedirectResponse;
use Smvc\Dispatch\RequestInterface;
use Smvc\Form\Form;
use Smvc\View\View;

use Zend\Validator\EmailAddress;

class SettingsController extends AbstractController
{
    private function getForm()
    {
        $app = $this->getApplication();
        $config = $app->getConfig();
        $account = $app->getSession()->getAccount();

        $defaultAddress = $app->getServiceRegistry()->offsetGet('defaultAddress');

        $form = new Form();

        // Identity
        $form->addElement(array(
            'name'        => 'displayName',
            'default'     => $config['identity/displayName'],
            'placeholder' => $account->getUsername(),
        ));
        $form->addElement(array(
            'name'        => 'organization',
            'default'     => $config['identity/organization'],
            'placeholder' => "No organization",
        ));
        $form->addElement(array(
            'name'        => 'mail',
            'validators'  => new EmailAddress(),
            'default'     => $config['identity/mail'],
            'placeholder' => $defaultAddress,
        ));
        $form->addElement(array(
            'name'        => 'replyTo',
            'validators'  => new EmailAddress(),
            'default'     => $config['identity/replyTo'],
            'placeholder' => "Same as your mail address",
        ));

        // Compose settings
        $form->addElement(array(
            'name'        => 'copyTo',
            'validators'  => new EmailAddress(),
            'default'     => $config['compose/copyTo'],
        ));

        return $form;
    }

    public function getAction(RequestInterface $request, array $args)
    {
        $form = $this->getForm();

        return new View(array(
            'defaults' => $form->getDefaultValues(),
            'placeholders' => $form->getPlaceHolders(),
        ), 'app/settings/index');
    }

    public function postAction(RequestInterface $request, array $args)
    {
        $form = $this->getForm();
        $form->setValues($request->getContent());

        if ($form->validate($form->getValues())) {

            $this
                ->getApplication()
                ->getConfig()
                ->offsetSet('identity', $form->getValues());

            $this
                ->getApplication()
                ->getMessager()
                ->addMessage("Your preferences have been saved", Message::TYPE_SUCCESS);

            return new RedirectResponse($request->getResource());

        } else {
            $messager = $this->getApplication()->getMessager();
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
            ), 'app/settings/index');
        }
    }
}
