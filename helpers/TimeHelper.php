<?php

namespace abcms\library\helpers;

class TimeHelper
{

    /**
     * Returns Time and Date at the same format as MySql datetime type,
     * Returns current date if no timestamp is provided. 
     * @param timestamp $timestamp  timestamp to be formatted
     * @param boolean $dateTime if date time format or only date
     * @return string Date
     */
    public static function MysqlFormat($timestamp = NULL, $dateTime = true)
    {
        $format = ($dateTime) ? 'Y-m-d H:i:s' : 'Y-m-d';
        if($timestamp) {
            $date = date($format, $timestamp);
        }
        else {
            $date = date($format);
        }
        return $date;
    }

}
