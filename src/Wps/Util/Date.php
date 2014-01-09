<?php

namespace Wps\Util;

final class Date
{
    /**
     * Get \DateTime object from timestamp, if null given return null
     *
     * @param int $value
     *
     * @return \DateTime
     */
    static public function fromTimestamp($value)
    {
        if (empty($value)) {
            return $value;
        }
        if (!is_numeric($value)) {
            return null;
        }
        return new \DateTime("@" . $value);
    }
}