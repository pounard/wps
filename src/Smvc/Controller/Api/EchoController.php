<?php

namespace Smvc\Controller\Api;

use Smvc\Controller\AbstractController;
use Smvc\Dispatch\Request;
use Smvc\Dispatch\RequestInterface;

/**
 * Return parameters from the request
 */
class EchoController extends AbstractController
{
    public function deleteAction(RequestInterface $request, array $args)
    {
        return array(
            'resource' => $request->getResource(),
            'method'   => Request::methodToString($request->getMethod()),
            'options'  => $request->getOptions(),
            'content'  => $request->getContent(),
            'args'     => $args,
        );
    }

    public function getAction(RequestInterface $request, array $args)
    {
        return array(
            'resource' => $request->getResource(),
            'method'   => Request::methodToString($request->getMethod()),
            'options'  => $request->getOptions(),
            'args'     => $args,
        );
    }

    public function postAction(RequestInterface $request, array $args)
    {
        return array(
            'resource' => $request->getResource(),
            'method'   => Request::methodToString($request->getMethod()),
            'options'  => $request->getOptions(),
            'content'  => $request->getContent(),
            'args'     => $args,
        );
    }

    public function putAction(RequestInterface $request, array $args)
    {
        return array(
            'resource' => $request->getResource(),
            'method'   => Request::methodToString($request->getMethod()),
            'options'  => $request->getOptions(),
            'content'  => $request->getContent(),
            'args'     => $args,
        );
    }
}
