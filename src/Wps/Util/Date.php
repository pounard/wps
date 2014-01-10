<?php

namespace Wps\Util;

final class Date
{
    /**
     * MySQL Datetime
     */
    const FORMAT_MYSQL_DATETIME = 'Y-m-d H:i:s';

    /**
     * MySQL Date
     */
    const FORMAT_MYSQL_DATE = 'Y-m-d';

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

    static public function fromFormat($value, $format = self::FORMAT_MYSQL_DATETIME)
    {
        return \DateTime::createFromFormat($format, $value);
    }

    static public function toTimestamp($value, $nullValue = null)
    {
        if (null === $value || !$value instanceof \DateTime) {
            return $nullValue;
        }
        return $value->getTimestamp();
    }

    static public function nullDate($date = null)
    {
        if ($date instanceof \DateTime) {
            return $date;
        }
        return new \DateTime('@0');
    }
}
