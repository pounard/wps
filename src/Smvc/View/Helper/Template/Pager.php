<?php

namespace Smvc\View\Helper\Template;

use Smvc\Model\Query;

class Pager extends AbstractHelper
{
    public function __invoke(Query $query, $total = 80)
    {
        $pageSize    = $query->getLimit();
        $currentPage = floor($query->getOffset() / $pageSize);
        $pageCount   = ceil($total / $pageSize);

        if (Query::LIMIT_NONE === $pageSize || $pageCount < 2) {
            return '';
        }

        $items = array();
        if (0 !== $currentPage) {
            // Link to first
            $items[] = '<a href="' . $this->url('test', array('page' => $currentPage - 1)) . '" title="Go to first page">«</a>';
        }
        if (1 < $currentPage) {
            // Link to previous
            $items[] = '<a href="' . $this->url('test', array('page' => $currentPage - 1)) . '" title="Go to previous page">‹</a>';
        }
        // @todo Page numbers
        if ($currentPage < $pageCount - 1) {
            // Link to next
            $items[] = '<a href="' . $this->url('test', array('page' => $pageCount - 1)) . '" title="Go to next page">›</a>';
        }
        if ($currentPage !== $pageCount) {
            // Link to last
            $items[] = '<a href="' . $this->url('test', array('page' => $pageCount)) . '" title="Go to last page">»</a>';
        }

        return '<ul class="pager"><li>' . implode('</li><li>', $items) . '</li></ul>';
    }
}
