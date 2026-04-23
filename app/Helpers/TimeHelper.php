<?php

namespace App\Helpers;

class TimeHelper
{
    /**
     * Convert decimal hours to human-readable format
     * Examples:
     *  - 0.8 → "48 minutes"
     *  - 1.5 → "1h 30min"
     *  - 2.0 → "2h"
     *  - 0.5 → "30 minutes"
     *
     * @param  float  $decimalHours
     * @return string
     */
    public static function hoursToReadable($decimalHours)
    {
        if ($decimalHours <= 0) {
            return '0 minutes';
        }

        $hours = floor($decimalHours);
        $minutes = round(($decimalHours - $hours) * 60);

        // Less than 1 hour: show only minutes
        if ($hours == 0) {
            return $minutes.' minute'.($minutes != 1 ? 's' : '');
        }

        // Exactly whole hours: show only hours
        if ($minutes == 0) {
            return $hours.'h';
        }

        // Hours and minutes
        return $hours.'h '.$minutes.'min';
    }

    /**
     * Convert minutes to decimal hours
     *
     * @param  int  $minutes
     * @return float
     */
    public static function minutesToHours($minutes)
    {
        return round($minutes / 60, 2);
    }
}
