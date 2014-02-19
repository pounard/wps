<?php

namespace Smvc\Dispatch\Router;

use Smvc\Controller\ControllerInterface;
use Smvc\Core\AbstractApplicationAware;
use Smvc\Dispatch\Http\RedirectResponse;
use Smvc\Dispatch\RequestInterface;
use Smvc\Error\NotFoundError;

/**
 * Router interface
 */
class DefaultRouter extends AbstractApplicationAware implements RouterInterface
{
    public function findController(RequestInterface $request)
    {
        $resource = $request->getResource();
        $resource = trim($resource);
        $resource = trim($resource, '/\\');

        $app = $this->getApplication();

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

        $applications = $this->getApplication()->getParameter('applications', array());
        $applications += array('core' => "\\Smvc");

        while (!empty($path)) {

            $previous = null;
            if (is_numeric($path[count($path) - 1])) {
                // This is a simple shortcut; Just consider numeric values as
                // arguments. There is no logical explaination, just do it
                // because it's easier. URLs such as: foo/1/bar will call
                // the Foo\BarController action if found using 1 as first
                // parameter
                if (!empty($args)) {
                    $previous = array_pop($args);
                    array_unshift($args, array_pop($path));
                    $path[] = $previous;
                } else {
                    array_unshift($args, array_pop($path));
                }
            }

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

            if (isset($previous) && !empty($args)) {
                // We actually messed up a bit with parameters order
                // and need to get them right back on track
                $args[] = array_pop($path);
            } else {
                array_unshift($args, array_pop($path));
            }
        }

        throw new NotFoundError("Not found");
    }
}
