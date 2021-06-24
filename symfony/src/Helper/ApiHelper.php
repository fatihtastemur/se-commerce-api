<?php

namespace App\Helper;

class ApiHelper
{
    /**
     * @param $number
     * @return string
     */
    public static function numberFormater($number): string
    {
        return number_format((float)$number, 2, '.', '');
    }
}