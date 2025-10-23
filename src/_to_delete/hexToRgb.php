<?php
namespace fmihel\report\_utils;

trait hexToRgb
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
}
