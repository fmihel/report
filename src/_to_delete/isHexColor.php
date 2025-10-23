<?php
namespace fmihel\report\_utils;

trait isHexColor
{
    public static function isHexColor($color): bool
    {
        $color  = strtolower($color);
        $colors = str_split($color);
        $count  = count($colors);

        if ($count !== 7 && $count !== 9) {
            return false;
        }
        if ($colors[0] !== '#') {
            return false;
        }

        for ($i = 1; $i < $count; $i++) {
            $code = ord($colors[$i]);
            if (! (($code >= 97 && $code <= 102) || ($code >= 48 && $code <= 57))) {
                return false;
            }
        }
        return true;
    }
}
