<?php
namespace fmihel\report\_utils;

trait hexToRgbw
{
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
}
