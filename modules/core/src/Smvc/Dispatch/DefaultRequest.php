<?php

namespace Smvc\Dispatch;

class DefaultRequest implements RequestInterface
{
    /**
     * Create a valid URL using the given request as base
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
    static public function createUrlFromRequest(
        RequestInterface $request,
        $path          = null,
        array $args    = null,
        $fragment      = null,
        $keepArguments = false)
    {
        if (null !== $request) {
            if ($keepArguments) {
                $query = $request->getOptions();
            }
            if (null === $path) {
                $path = $request->getResource();
            }
            $basepath = $request->getBasePath();
        } else {
            // Can't determine a base path so use a sensible default
            $basepath = '/';
        }

        if (!empty($args)) {
            // Parse and cleanup user provided query parameters
            // This will overwrite request driven ones if any set
            foreach ($args as $key => $value) {
                $query[$key] = $value;
            }
        }

        if (!empty($query)) {
            // Properly encode query parameters if needed
            foreach ($args as $key => $value) {
                $query[$key] = urlencode($key) . '=' . urlencode($value);
            }
            $suffix = '?' . implode('&', $query);
        } else {
            $suffix = '';
        }

        return $basepath . $path . $suffix;
    }

    /**
     * @var int
     */
    protected $method;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var string
     */
    protected $inputType;

    /**
     * @var array
     */
    protected $outputType = array();

    /**
     * @var string
     */
    protected $variant;

    /**
     * @var string
     */
    protected $charset;

    /**
     * Default constructor
     *
     * @param string $path
     * @param string $content
     * @param array $options
     */
    public function __construct(
        $path,
        $content       = null,
        array $options = array(),
        $method        = Request::METHOD_GET,
        $variant       = null)
    {
        $this->path    = $path;
        $this->content = $content;
        $this->options = $options;
        $this->method  = $method;
        $this->variant = $variant;
    }

    public function getBasePath()
    {
        // @todo DYNAMIC!
        return '/';
    }

    public function getResource()
    {
        return $this->path;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
    }

    public function getOption($name, $default = null)
    {
        if ($this->hasOption($name)) {
            return $this->options[$name];
        } else {
            return $default;
        }
    }

    public function setInputContentType($contentType)
    {
        $this->inputType[] = $contentType;
    }

    public function getInputContentType()
    {
        return $this->inputType;
    }

    public function setOutputContentTypes(array $contentTypeList)
    {
        $this->outputType = $contentTypeList;
    }

    public function getOutputContentTypes()
    {
        return $this->outputType;
    }

    public function getPreferredOutputContentType()
    {
        return reset($this->outputType);
    }

    public function createResponse()
    {
        return new DefaultResponse();
    }

    public function getVariant()
    {
        return $this->variant;
    }

    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    public function getCharset()
    {
        return $this->charset;
    }
}
