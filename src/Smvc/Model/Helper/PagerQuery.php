<?php

namespace Smvc\Model\Helper;

/**
 * Represent a pager query
 *
 * Remember that when you use it, you need to manually call the
 * setTotal() method after you did your query in order for the
 * pager to be displayed
 */
class PagerQuery extends Query
{
    /**
     * @var int
     */
    private $page;

    /**
     * @var string
     */
    private $resource;

    /**
     * @var array
     */
    private $resourceOptions;

    /**
     * @var int
     */
    private $total;

    /**
     * Set total number of items
     *
     * Page count will be determined automatically
     *
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * Get total number of items
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Get page count
     *
     * @return int
     */
    public function getPageCount()
    {
        // Logical calculation would be ceil() instead but the paging starts
        // at 0 so it's ceil() - 1 which is the same as floor()
        return floor($this->total / $this->getLimit());
    }

    /**
     * Get page number
     *
     * Page numbering starts at 0 not at 1
     *
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Get resource path
     *
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Get resource path options
     *
     * @param array|null
     */
    public function getResourceOptions()
    {
        return $this->resourceOptions;
    }

    /**
     * Default constructor
     *
     * @param string $resource
     *   Resource path to link to
     * @param array $options
     *   Resource query options (GET parameters to keep)
     * @param int $limit
     *   Page limit
     * @param int $page
     *   Current page
     * @param int $sort
     *   See Query::getSort()
     * @param int $order
     *   See Query::getOrder()
     */
    public function __construct(
        $resource,
        array $options = null,
        $limit         = Query::LIMIT_DEFAULT,
        $page          = 0,
        $sort          = self::SORT_SEQ,
        $order         = self::ORDER_DESC)
    {
        parent::__construct(
            $limit,
            $limit * $page,
            $sort,
            $order
        );

        $this->page = $page;
        $this->resource = $resource;
        $this->resourceOptions = $options;
    }
}
