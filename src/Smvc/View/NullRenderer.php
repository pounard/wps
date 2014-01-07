<?php

namespace Smvc\View;

use Smvc\Dispatch\RequestInterface;

class NullRenderer implements RendererInterface
{
    public function render(View $view, RequestInterface $request)
    {
        return null;
    }

    public function getContentType()
    {
        return null;
    }
}
