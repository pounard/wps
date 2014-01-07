<?php

namespace Smvc\Controller;

use Smvc\Dispatch\RequestInterface;

/**
 * Controller interface
 */
interface ControllerInterface
{
    public function dispatch(RequestInterface $request, array $args);
}
