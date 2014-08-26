<?php

namespace Smvc\View\Helper\Template;

use Smvc\Dispatch\DefaultRequest;
use Smvc\Dispatch\RequestInterface;

/**
 * Build application URL for HTML rendering
 *
 * Sad but true story this is actually the only view helper that will need
 * to be contextually tied to the Request thus breaking encapsulation
 */
class Url extends AbstractHelper
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * Set request
     *
     * @param RequestInterface $request
     */
    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Build a URL
     *
     * @param string $path
     *   Resource path where to link; If null will repeat the current
     *   request resource path
     * @param array $args
     *   Query parameters (GET)
     * @param string $fragment
     *   Hashed URL fragment
     * @param string $keepArguments
     *   Should this link include the current query parameters if the given
     *   resource path is the same as the current resource path? Note that
     *   if you give conflicting parameters in $args, those will superseed
     *   the current request's one
     *
     * @return string
     */
    public function __invoke(
        $path          = null,
        array $args    = null,
        $fragment      = null,
        $keepArguments = false)
    {
        return DefaultRequest::createUrlFromRequest($this->request, $path, $args, $fragment, $keepArguments);
    }
}
