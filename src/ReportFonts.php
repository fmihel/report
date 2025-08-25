<?php
namespace fmihel\report;

class ReportFonts
{

    private static $fonts = [];

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
}
