<?php


class VGL_Utils_ParseReleaseDate
{
    public static function parse($input)
    {
        $aTmpPart = explode('/', $input);
        if (count($aTmpPart) >= 3) {
            list($month, $day, $year) = $aTmpPart;
        } elseif (count($aTmpPart) === 2) {
            list($month, $year) = $aTmpPart;
            $day = '01';
        } else {
            $day = '01';
            $month = '01';
            $year = reset($aTmpPart);
        }
        return $day . '/' . $month . '/' . $year;
    }
}