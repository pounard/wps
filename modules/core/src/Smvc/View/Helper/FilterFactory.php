<?php

namespace Smvc\View\Helper;

use Smvc\View\Helper\Filter\NullFilter;
use Smvc\Plugin\DefaultFactory;

class FilterFactory extends DefaultFactory
{
    /**
     * @var FitlerInterface[]
     */
    private $filters = array();

    public function __construct()
    {
        $this->registerAll(array(
            'autop'   => '\Smvc\View\Helper\Filter\AutoParagraph',
            'htmlesc' => '\Smvc\View\Helper\Filter\HtmlEncode',
            'lntohr'  => '\Smvc\View\Helper\Filter\StupidLinesToHr',
            'lntovd'  => '\Smvc\View\Helper\Filter\StupidLinesToVoid',
            'null'    => '\Smvc\View\Helper\Filter\NullFilter',
            'strip'   => '\Smvc\View\Helper\Filter\Strip',
            'urltoa'  => '\Smvc\View\Helper\Filter\UrlToLink',
            'urltou'  => '\Smvc\View\Helper\Filter\UrlToUrl',
        ));
    }

    public function createNullInstance()
    {
        return new NullFilter();
    }

    /**
     * Get a filter collection using the given filter types
     *
     * @param array $types
     *   Ordered array of filter types
     *
     * @return FilterCollection
     */
    private function getCollectionFrom(array $types)
    {
        foreach ($types as $index => $type) {
            $types[$index] = $this->getInstance($type);
        }

        return new FilterCollection($types);
    }

    /**
     * Get filter for the given text type
     *
     * @param string $type
     *
     * @return FilterInterface
     */
    public function getFilter($type)
    {
        if (isset($this->filters[$type])) {
            return $this->filters[$type];
        }

        // Fetch type configuration
        $config = $this->getApplication()->getConfig();
        $key = 'filters/' . $type;
        if (isset($config[$key])) {
            $types = $config[$key];
        } else {
            $types = array('strip'); // Default must be secure
        }

        return $this->filters[$type] = $this->getCollectionFrom($types);
    }
}
