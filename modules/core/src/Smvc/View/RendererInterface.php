<?php

namespace Smvc\View;

use Smvc\Dispatch\RequestInterface;

interface RendererInterface
{
    /**
     * Render content
     *
     * FIXME: Not proud of passing the request here but it is needed for
     * the HtmlRenderer
     *
     * @param mixed $return
     */
    public function render(View $view, RequestInterface $request);

    /**
     * Get return content mime type
     *
     * @return string
     */
    public function getContentType();
}
