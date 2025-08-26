<?php
namespace fmihel\report\routines;

function randomString($count)
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
