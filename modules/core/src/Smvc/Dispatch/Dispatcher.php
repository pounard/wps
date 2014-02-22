<?php

namespace Smvc\Dispatch;

use Smvc\Controller\ControllerInterface;
use Smvc\Core\AbstractApplicationAware;
use Smvc\Core\ApplicationAwareInterface;
use Smvc\Dispatch\Http\HttpResponse;
use Smvc\Dispatch\Http\RedirectResponse;
use Smvc\Dispatch\Router\DefaultRouter;
use Smvc\Dispatch\Router\RouterInterface;
use Smvc\Error\LogicError;
use Smvc\Error\UnauthorizedError;
use Smvc\View\ErrorView;
use Smvc\View\HtmlRenderer;
use Smvc\View\NullRenderer;
use Smvc\View\View;

/**
 * Front dispatcher (application runner)
 */
class Dispatcher extends AbstractApplicationAware
{
    /**
     * Not ideal but working map of mime types and class to use
     */
    static $responseMap = array(
        'text/html' => '\\Smvc\\View\\HtmlRenderer',
        'application/xhtml+xml' => '\\Smvc\\View\\HtmlRenderer',
        'application/json' => '\\Smvc\\View\\JsonRenderer',
        'text/javascript' => '\\Smvc\\View\\JsonRenderer',
    );

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * Set router
     *
     * @param RouterInterface $router
     */
    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;

        if ($this->router instanceof ApplicationAwareInterface) {
            $this->router->setApplication($this->getApplication());
        }
    }

    /**
     * Get router
     *
     * @return RouterInterface
     */
    public function getRouter()
    {
        if (null === $this->router) {
            $this->setRouter(new DefaultRouter());
        }

        return $this->router;
    }

    /**
     * Execute controller and fetch a view
     *
     * @param RequestInterface $request
     * @param callable|ControllerInterface $controller
     * @param array $args
     *
     * @return View
     */
    protected function executeController(
        RequestInterface $request,
        $controller,
        array $args)
    {
        $view = null;

        if ($controller instanceof ApplicationAwareInterface) {
            $controller->setApplication($this->getApplication());
        }

        if ($controller instanceof ControllerInterface) {
            $view = $controller->dispatch($request, $args);
        } else if (is_callable($controller)) {
            $view = call_user_func($controller, $request, $args);
        } else if ($controller instanceof ResponseInterface) {
            return $controller;
        } else {
            throw new LogicError("Controller is broken");
        }

        // Allows controller to return the response directly
        // and bypass the native rendering pipeline
        if (!$view instanceof ResponseInterface && !$view instanceof View) {
            $view = new View($view);
        }
        if ($view instanceof ApplicationAwareInterface) {
            $view->setApplication($this->getApplication());
        }

        return $view;
    }

    /**
     * Dispatch incomming request
     *
     * @param RequestInterface $request
     */
    public function dispatch(RequestInterface $request)
    {
        try {
            // Response highly depend on request so let the request
            // a chance to give the appropriate response implementation
            $response = $request->createResponse();
            if (null === $response) {
                $response = new DefaultResponse();
            }

            // Attempt to determine the renderer depending on the incomming
            // request. I'm not proud of this algorithm but it works quite
            // well: ideally I'll move it out
            $renderer = null;
            foreach ($request->getOutputContentTypes() as $type) {
                if (isset(self::$responseMap[$type])) {
                    $renderer = new self::$responseMap[$type]();
                    break;
                }
            }
            if (null === $renderer) {
                $renderer = new \Smvc\View\HtmlRenderer();
            }

            if ($renderer instanceof ApplicationAwareInterface) {
                $renderer->setApplication($this->getApplication());
            }
            if ($response instanceof ApplicationAwareInterface) {
                $response->setApplication($this->getApplication());
            }

            try {
                // Most dispatching magic happens here
                list($controller, $args) = $this->getRouter()->findController($request);
                $view = $this->executeController($request, $controller, $args);
                $contentType = $renderer->getContentType();

                if ($view instanceof ResponseInterface) {
                    $view->send(null);
                } else {
                    // Where there is nothing to render just switch to a null
                    // implementation that will put nothing into the payload
                    if (!$response instanceof HttpResponse && $view->isEmpty()) {
                        $renderer = new NullRenderer();
                    }
                    // Because one liners are too mainstream
                    $response->send($renderer->render($view, $request), $contentType);
                }

            // Within exception handling the dispatcher will act as a controller
            } catch (UnauthorizedError $e) {
                // FIXME: This code should not live here
                if ($renderer instanceof HtmlRenderer) {
                    // If HTML is the demanded protocol then redirect to the
                    // login controller whenever the user is not authenticated
                    if ($this->getApplication()->getSession()->isAuthenticated()) {
                        $response->send(
                            $renderer->render(new View(array('e' => $e), 'core/unauth'), $request),
                            null,
                            $e->getCode(),
                            $e->getMessage()
                        );
                    } else {
                        $response = new RedirectResponse('account/login');
                        $response->send(null, null, $e->getCode(), $e->getMessage());
                    }
                } else {
                    // Unauthorized error will end up releasing a 403 error in
                    // the client demanded protocol
                    $response->send(
                        $renderer->render(new ErrorView($e), $request),
                        null,
                        $e->getCode(),
                        $e->getMessage()
                    );
                }
            } catch (\Exception $e) {
                // When we are in HTTP/HTML context we cannot throw back
                // specialized error codes because the browser might choose
                // not to display content, just send the 500 generic error
                // code
                if ($renderer instanceof HtmlRenderer) {
                    $code = 500;
                } else {
                    $code = $e->getCode();
                }
                $response->send(
                    $renderer->render(new ErrorView($e), $request),
                    null,
                    $code,
                    $e->getMessage()
                );
            }
        } catch (\Exception $e) {
            $response = new DefaultResponse();
            $response->send(
                $e->getMessage() . "\n" . $e->getTraceAsString(),
                null,
                $e->getCode(),
                $e->getMessage()
            );
        }
    }
}
