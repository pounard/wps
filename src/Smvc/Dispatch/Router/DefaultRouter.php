<?php

namespace Smvc\Dispatch\Router;

use Smvc\Controller\ControllerInterface;
use Smvc\Core\AbstractContainerAware;
use Smvc\Dispatch\Http\RedirectResponse;
use Smvc\Dispatch\RequestInterface;
use Smvc\Error\NotFoundError;

/**
 * Router interface
 */
class DefaultRouter extends AbstractContainerAware implements RouterInterface
{
    public function findController(RequestInterface $request)
    {
        $resource = $request->getResource();
        $resource = trim($resource);
        $resource = trim($resource, '/\\');

        $container = $this->getContainer();

        // Special case: when requested is HTML and no path is given
        // redirect to the index controler, that should exist in config
        if (empty($resource)) {
            $accept = $request->getOutputContentTypes();
            if (in_array("text/html", $accept) || in_array("application/html", $accept)) {
                // Redirect to default path if any
                return array(new RedirectResponse(), array());
            }
        }

        $path = explode('/', $resource);
        $args = array();

        $applications = $this->getContainer()->getParameter('applications', array());
        $applications += array('core' => "\\Smvc");

        while (!empty($path)) {

            $name = $path;
            array_walk($name, function (&$value) {
                $value = ucfirst(strtolower($value));
            });

            foreach ($applications as $namespace) {
                $className = $namespace . '\\Controller\\' . implode('\\', $name) . 'Controller';

                if (class_exists($className)) {
                    return array(new $className(), $args);
                }
            }

            array_unshift($args, array_pop($path));
        }

        throw new NotFoundError("Not found");
    }
}
