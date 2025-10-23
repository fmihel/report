<?php
namespace fmihel\report;

class ReportUtils
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
            $r    = self::translate($a, 0, 1, 255, $rgba[0]);
            $g    = self::translate($a, 0, 1, 255, $rgba[1]);
            $b    = self::translate($a, 0, 1, 255, $rgba[2]);

            return [$r, $g, $b];

        } elseif (strlen($hexColor) > 5) {
            return self::hexToRgb($hexColor);

        }
        return [0, 0, 0];
    }
    public static function imgSize($content)
    {
        if (gettype($content) !== 'string') {
            return [0, 0];
        }

        if (mb_strlen(trim($content)) === 0) {
            return [0, 0];
        }

        $uri = 'data://application/octet-stream;base64,' . base64_encode($content);

        return getimagesize($uri);
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
    public static function randomString($count)
    {
        $result = '';
        for ($i = 0; $i < $count; $i++) {
            if ($i === 0) {
                $result .= chr(rand(65, 90));
            } else {
                if (rand(1, 10) > 6) {
                    $result .= chr(rand(48, 57));
                } else {
                    $result .= chr(rand(65, 90));
                }

            }
        }

        return $result;

    }
    public static function translate($y, $y1, $y2, $x1, $x2, $prec = 0)
    {
        return round(($x2 * ($y - $y1) + $x1 * ($y2 - $y)) / ($y2 - $y1), $prec);
    }
}
