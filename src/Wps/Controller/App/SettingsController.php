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
    public function getAction(RequestInterface $request, array $args)
    {
        return new View(array(), 'app/settings/index');
    }
}
