<?php

namespace App\Helpers;

class NumberHelper
{
    /**
     * Format large numbers with K/M/B suffix
     * 
     * @param float $number
     * @param int $decimals
     * @return string
     */
    public static function formatShort($number, $decimals = 1)
    {
        if ($number < 1000) {
            return number_format($number, 2);
        }

        $suffixes = ['', 'K', 'M', 'B', 'T'];
        $suffixIndex = 0;

        while ($number >= 1000 && $suffixIndex < count($suffixes) - 1) {
            $number /= 1000;
            $suffixIndex++;
        }

        return number_format($number, $decimals) . $suffixes[$suffixIndex];
    }
}
