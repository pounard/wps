<?php

namespace Wps\Controller\Testing;

use Smvc\Controller\AbstractController;
use Smvc\Core\Message;
use Smvc\Dispatch\Http\RedirectResponse;
use Smvc\Dispatch\RequestInterface;
use Smvc\View\View;

class UploadController extends AbstractController
{
    public function getAction(RequestInterface $request, array $args)
    {
        $container = $this->getContainer();
        $account = $container->getSession()->getAccount();
        $config = $container->getConfig();
        $uploadDir = $config['dir/upload'] . '/' . $account->getId();

        print_r($uploadDir);
        die();

        $messager = $this
            ->getContainer()
            ->getMessager()
            ->addMessage("Photos have been imported");

        return new RedirectResponse('app/index');
    }

    public function postAction(RequestInterface $request, array $args)
    {
        $form = $this->getForm();
        $form->setValues($request->getContent());

        if ($form->validate($form->getValues())) {

            $this
                ->getContainer()
                ->getConfig()
                ->offsetSet('identity', $form->getValues());

            $this
                ->getContainer()
                ->getMessager()
                ->addMessage("Your preferences have been saved", Message::TYPE_SUCCESS);

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
            ), 'app/settings/index');
        }
    }
}
