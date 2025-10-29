<?php
namespace fmihel\report\utils;

class Color
{
    public static function hexToRgb(string $hexColor): array
    {
        // Remove the '#' if present
        $hexColor = ltrim($hexColor, '#');
        $r        = 0;
        $g        = 0;
        $b        = 0;
        // Handle shorthand hex colors (e.g., 'F00' for 'FF0000')
        if (strlen($hexColor) > 5) {
            $r = hexdec(substr($hexColor, 0, 2));
            $g = hexdec(substr($hexColor, 2, 2));
            $b = hexdec(substr($hexColor, 4, 2));
        }

        return [$r, $g, $b];
    }
    public static function hexToRgba(string $hexColor): array
    {
        // Remove the '#' if present
        $hexColor = ltrim($hexColor, '#');
        $r        = 0;
        $g        = 0;
        $b        = 0;
        $a        = 0;
        // Handle shorthand hex colors (e.g., 'F00' for 'FF0000')
        if (strlen($hexColor) > 7) {

            $r = hexdec(substr($hexColor, 0, 2));
            $g = hexdec(substr($hexColor, 2, 2));
            $b = hexdec(substr($hexColor, 4, 2));

            $alpha = hexdec(substr($hexColor, 6, 2));
            $a     = round($alpha / 255, 1);

        } elseif (strlen($hexColor) > 5) {

            $r = hexdec(substr($hexColor, 0, 2));
            $g = hexdec(substr($hexColor, 2, 2));
            $b = hexdec(substr($hexColor, 4, 2));
        }

        return [$r, $g, $b, $a];
    }
    public static function hexToRgbw(string $hexColor): array
    {
        // Remove the '#' if present
        $hexColor = ltrim($hexColor, '#');
        // Handle shorthand hex colors (e.g., 'F00' for 'FF0000')
        if (strlen($hexColor) > 7) {
            $rgba = self::hexToRgba($hexColor);
            $a    = $rgba[3];
            $r    = Math::translate($a, 0, 1, 255, $rgba[0]);
            $g    = Math::translate($a, 0, 1, 255, $rgba[1]);
            $b    = Math::translate($a, 0, 1, 255, $rgba[2]);

            return [$r, $g, $b];

        } elseif (strlen($hexColor) > 5) {
            return self::hexToRgb($hexColor);

        }
        return [0, 0, 0];
    }
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
