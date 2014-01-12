<?php

namespace Smvc\Dispatch\Http;

use Smvc\Core\AbstractContainerAware;
use Smvc\Dispatch\RequestInterface;
use Smvc\Dispatch\ResponseInterface;

class RedirectResponse extends AbstractContainerAware implements
    ResponseInterface
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var int
     */
    private $code;

    /**
     * Default constructor
     *
     * @param string $url
     *   Path or resource where to redirect, null for front page
     * @param int $code
     *   HTTP response code
     */
    public function __construct($url = null, $code = 302)
    {
        $this->url = $url;
        $this->code = $code;
    }

    public function send(
        $output,
        $contentType   = null,
        $statusCode    = null, 
        $statusMessage = null)
    {
        if (null === $this->url) {
            $config = $this->getContainer()->getConfig();
            $url = $config['index'];
        } else {
            $url = $this->url;
        }

        if (false === strpos($url, '://')) {
            // Got a resource
            // @todo Prefix with scheme and host
            $url = sprintf("%s%s", '/', $url);
            if (empty($url)) {
                $url = '/';
            }
        } // Else this is a full URL

        header(sprintf('HTTP/1.1 %s %s', $this->code, "Moved"), true, $this->code);
        header(sprintf('Location: %s', $url));
    }
}
