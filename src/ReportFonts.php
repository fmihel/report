<?php
namespace fmihel\report;

require_once __DIR__ . '/maps/roboto.php';

class ReportFonts
{

    private static $fonts = [];
    private static $maps  = [];

    public static function add(string $alias, string $fontFileName, array $param = [])
    {

        self::$fonts[$alias] = [
            'name'         => pathinfo($fontFileName, PATHINFO_FILENAME),
            'fontFileName' => $fontFileName,
            'param'        => $param,
        ];

    }

    public static function get(string $alias): array
    {
        return self::$fonts[$alias];
    }

    public static function assignToDriver($driver)
    {
        foreach (self::$fonts as $alias => $font) {

            $driver->addFont($alias, $font['fontFileName'], $font['param']);

        }
    }

    public static function addMap(string $alias, string $map)
    {
        self::$maps[$alias] = $map;
    }

    public static function metrik(string $text, string $alias)
    {
        $common = 8.5;
        $h      = '1';
        $w      = 0;
        $len    = strlen($text);

        if (isset(self::$maps[$alias])) {
            $map = self::$maps[$alias];
            $w   = $map::stringWidth($text);
        } else {
            $w = $len * $common;
        }

        return ['h' => $h, 'w' => $w, 'len' => $len];
    }

}

ReportFonts::addMap('roboto', 'rmap\roboto');
