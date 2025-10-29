<?php
namespace fmihel\report\utils;

class Img
{
    public static function size(string $filename): array
    {
        return [0, 0];
    }

    public static function sizeFromImgStream($stream): array
    {
        if (gettype($stream) !== 'string') {
            return [0, 0];
        }

        if (mb_strlen(trim($stream)) === 0) {
            return [0, 0];
        }

        $uri = 'data://application/octet-stream;base64,' . base64_encode($stream);

        return getimagesize($uri);
    }

}
