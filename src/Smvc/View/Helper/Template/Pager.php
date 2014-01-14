<?php

namespace Smvc\View\Helper\Template;

use Smvc\Model\Helper\PagerQuery;
use Smvc\Model\Helper\Query;

class Pager extends AbstractHelper
{
    public function __invoke(PagerQuery $pager)
    {
        $pageSize    = $pager->getLimit();
        $currentPage = $pager->getPage();
        $pageCount   = $pager->getPageCount();
        $path        = $pager->getResource();
        $args        = $pager->getResourceOptions();

        // 3 use cases where we cannot compute a pager:
        //  - First means no limit so the page displays everything
        //  - Second means the controller did not set the total, this is
        //    an error and we cannot compute the pager
        //  - Third means there is only one page and pager is useless
        if (Query::LIMIT_NONE === $pageSize || null === $pageCount || $pageCount < 2) {
            return '';
        }

        // Yes, this will be our pager
        $items = array();

        if (0 < $currentPage) {
            $args['page'] = 0;
            $items[] = '<a href="' . $this->url($path, $args) . '" title="Go to first page">«</a>';
        }
        if (1 < $currentPage) {
            $args['page'] = $currentPage - 1;
            $items[] = '<a href="' . $this->url($path, $args) . '" title="Go to previous page">‹</a>';
        }

        // @todo Page numbers

        $args['page'] = $currentPage;
        $items[] = '<a href="' . $this->url($path, $args) . '" title="Current page">' . ($currentPage + 1) . '</a>';

        // @todo Page numbers

        if ($currentPage < $pageCount - 1) {
            $args['page'] = $currentPage + 1;
            $items[] = '<a href="' . $this->url($path, $args) . '" title="Go to next page">›</a>';
        }
        if ($currentPage < $pageCount) {
            $args['page'] = $pageCount;
            $items[] = '<a href="' . $this->url($path, $args) . '" title="Go to last page">»</a>';
        }

        return '<ul class="pager clear centered"><li>' . implode('</li><li>', $items) . '</li></ul>';
    }
}
