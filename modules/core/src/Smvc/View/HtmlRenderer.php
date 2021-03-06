<?php

namespace Smvc\View;

use Smvc\Core\AbstractApplicationAware;
use Smvc\Core\ApplicationInterface;
use Smvc\Dispatch\RequestInterface;
use Smvc\Error\LogicError;
use Smvc\Error\TechnicalError;

/**
 * Application is needed here because we need site configuration for default
 * HTML variables such as site name
 */
class HtmlRenderer extends AbstractApplicationAware implements RendererInterface
{
    /**
     * @var TemplateResolver
     */
    private $resolver;

    public function setApplication(ApplicationInterface $application)
    {
        parent::setApplication($application);

        $this->resolver = new TemplateResolver();
        $this->resolver->setApplication($application);
    }

    /**
     * Prepare variables from the view
     *
     * @param mixed $values
     *   View values
     *
     * @return array
     *   Variables for the template
     */
    protected function prepareVariables(RequestInterface $request, $values)
    {
        if (is_array($values)) {
            $ret = $values;
        } else {
            $ret = array('content' => $values);
        }

        $app = $this->getApplication();
        $session = $app->getSession();
        $config = $app->getConfig();

        $ret['title'] = $config['html/title'];
        $ret['basepath'] = $request->getBasePath();
        $ret['path'] = $request->getResource();
        $ret['url'] = $ret['basepath'] . $ret['path'];
        $ret['session'] = $session;
        $ret['account'] = $session->getAccount();
        $ret['isAuthenticated'] = $session->isAuthenticated();
        $ret['pagetitle'] = isset($ret['pagetitle']) ? $ret['pagetitle'] : null;
        $ret['index'] = $config['index'];

        // The URL helper needs to know the Request in order to build
        // correct URLs, this breaks encapsulation and isolation but
        // it needs it, this will be the only exception
        $this->getApplication()
            ->getTemplateFactory()
            ->getInstance('url')
            ->setRequest($request);

        return $ret;
    }

    public function render(View $view, RequestInterface $request)
    {
        $templateFactory = $this->getApplication()->getTemplateFactory();
        $templateResolver = new TemplateResolver();

        // Current controller return
        $template = new Template(
            new View(
                $this->prepareVariables($request, $view->getValues()),
                $view->getTemplate()
            ),
            $templateFactory,
            $this->resolver
        );

        // Main layout
        $layout = new Template(
            new View(
                $this->prepareVariables($request, $template),
                'core/layout'
            ),
            $templateFactory,
            $this->resolver
        );

        return $layout;
    }

    public function getContentType()
    {
        return "text/html";
    }
}
