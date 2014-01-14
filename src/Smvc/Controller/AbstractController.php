<?php

namespace Smvc\Controller;

use Smvc\Core\AbstractContainerAware;
use Smvc\Dispatch\Request;
use Smvc\Dispatch\RequestInterface;
use Smvc\Error\MethodNotAllowedError;
use Smvc\Error\UnauthorizedError;
use Smvc\Model\Helper\PagerQuery;
use Smvc\Model\Helper\Query;

abstract class AbstractController extends AbstractContainerAware implements
    ControllerInterface
{
    /**
     * Get query from request
     *
     * @param RequestInterface $request
     *
     * @return Query
     */
    public function getQueryFromRequest(RequestInterface $request)
    {
        return new Query(
            $request->getOption('limit',  Query::LIMIT_DEFAULT),
            $request->getOption('offset', Query::OFFSET_DEFAULT),
            $request->getOption('sort',   Query::SORT_SEQ),
            $request->getOption('order',  Query::ORDER_DESC)
        );
    }

    /**
     * Get query from pager request
     *
     * @param RequestInterface $request
     * @param string $name
     *   Page parameter name
     * @param int $limit
     *   Default limit to set to query
     *
     * @return PagerQuery
     */
    public function getPagerQueryFromRequest(
        RequestInterface $request,
        $name  = 'page',
        $limit = Query::LIMIT_DEFAULT)
    {
        return new PagerQuery(
            $request->getResource(),
            $request->getOptions(),
            (int)$limit,
            (int)$request->getOption('page', 0),
            $request->getOption('sort', Query::SORT_SEQ),
            $request->getOption('order', Query::ORDER_DESC)
        );
    }

    /**
     * Get boolean value from arbitrary value
     *
     * @param mixed $value
     *
     * @return boolean
     */
    public function parseBoolean($value)
    {
        if (is_string($value)) {
            return in_array(strtolower(trim($value)), array("yes", "y", "true"));
        }
        return (bool)(int)$value;
    }

    public function dispatch(RequestInterface $request, array $args)
    {
        if (!$this->isAuthorized($request, $args)) {
            throw new UnauthorizedError();
        }

        switch ($request->getMethod()) {

            case Request::METHOD_DELETE:
                return $this->deleteAction($request, $args);

            case Request::METHOD_GET:
                return $this->getAction($request, $args);

            case Request::METHOD_POST:
                return $this->postAction($request, $args);

            case Request::METHOD_PUT:
                return $this->putAction($request, $args);

            case Request::METHOD_PATCH:
                return $this->patchAction($request, $args);

            case Request::METHOD_OPTIONS:
                return $this->optionsAction($request, $args);

            default:
                throw new MethodNotAllowedError();
        }
    }

    public function isAuthorized(RequestInterface $request, array $args)
    {
        return $this
            ->getContainer()
            ->getSession()
            ->isAuthenticated();
    }

    public function deleteAction(RequestInterface $request, array $args)
    {
        throw new MethodNotAllowedError();
    }

    public function getAction(RequestInterface $request, array $args)
    {
        throw new MethodNotAllowedError();
    }

    public function postAction(RequestInterface $request, array $args)
    {
        throw new MethodNotAllowedError();
    }

    public function putAction(RequestInterface $request, array $args)
    {
        throw new MethodNotAllowedError();
    }

    public function patchAction(RequestInterface $request, array $args)
    {
        throw new MethodNotAllowedError();
    }

    public function optionsAction(RequestInterface $request, array $args)
    {
        // FIXME
        
    }
}
