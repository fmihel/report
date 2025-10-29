<?php
namespace fmihel\report\utils;

class Img
{
    private static $cache = [
        'size' => [],
    ];

    public static function size(string $filename): array
    {
        if (! isset(self::$cache['size'][$filename])) {
            if ($stream = @fopen($filename, 'r')) {

                $data = stream_get_contents($stream, -1);
                fclose($stream);

                self::$cache['size'][$filename] = Img::sizeFromImgStream($data);
            } else {
                self::$cache['size'][$filename] = [0, 0];
            }
        }
        return self::$cache['size'][$filename];
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
