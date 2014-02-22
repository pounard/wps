<?php

namespace Smvc\Controller\Debug;

use Smvc\Controller\AbstractController;
use Smvc\Dispatch\RequestInterface;
use Smvc\View\View;

class PhpinfoController extends AbstractController
{
    public function getAction(RequestInterface $request, array $args)
    {
        echo phpinfo();
        die();
    }
}
