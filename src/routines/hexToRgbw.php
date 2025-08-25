<?php
namespace fmihel\report\routines;

function hexToRgbw(string $hexColor): array
{
    // Remove the '#' if present
    $hexColor = ltrim($hexColor, '#');
    // Handle shorthand hex colors (e.g., 'F00' for 'FF0000')
    if (strlen($hexColor) > 7) {
        $rgba = hexToRgba($hexColor);
        $a    = $rgba[3];
        $r    = translate($a, 0, 1, 255, $rgba[0]);
        $g    = translate($a, 0, 1, 255, $rgba[1]);
        $b    = translate($a, 0, 1, 255, $rgba[2]);

        return [$r, $g, $b];

    } elseif (strlen($hexColor) > 5) {
        return hexToRgb($hexColor);

    }
}
