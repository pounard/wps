<?php

namespace Smvc\View\Helper\Template;

use Smvc\Core\AbstractApplicationAware;
use Smvc\Core\ApplicationInterface;
use Smvc\View\Helper\FilterInterface;

class DateRange extends AbstractHelper
{
    public function __invoke($date1, $date2, $format = null)
    {
        $rot = null;
        $empty1 = empty($date1) || !$date1 instanceof \DateTime;
        $empty2 = empty($date2) || !$date2 instanceof \DateTime;

        if ($empty1 && $empty1) {
            return '';
        }

        if ($empty1) {
            return $this->date($date2, $format);
        } else if ($empty2) {
            return $this->date($date1, $format);
        } else if ($date1 == $date2) {
            return $this->date($date1, $format);
        } else if ($date2 < $date1) {
            $rot = $date2;
            $date2 = $date1;
            $date1 = $rot;
        }

        return $this->date($date1, $format) . ' - ' . $this->date($date2, $format);
    }
}
