<?php
namespace fmihel\report\routines;

function translate($y, $y1, $y2, $x1, $x2, $prec = 0)
{
    return round(($x2 * ($y - $y1) + $x1 * ($y2 - $y)) / ($y2 - $y1), $prec);
}
