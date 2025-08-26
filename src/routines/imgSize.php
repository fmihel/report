<?php
namespace fmihel\report\routines;

function imgSize($content)
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
