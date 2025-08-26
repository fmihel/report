<?php
namespace fmihel\report\driver;

use fmihel\report\ReportConsts;
use fmihel\report\ReportFonts;

require_once __DIR__ . '/../ReportConsts.php';
require_once __DIR__ . '/ReportDriver.php';
require_once __DIR__ . '/../routines/translate.php';
require_once __DIR__ . '/../ReportFonts.php';

class ImagickDriver extends ReportDriver
{
    private $default = [
        ReportConsts::PORTRAIT  => [
            'realArea'    => [
                'xmin' => 0,
                'ymin' => 0,
                'xmax' => 1024,
                'ymax' => 1448, //1024 * A4_RATIO,

                // 'xmax' => 2500,
                // 'ymax' => 2500 * ReportConsts::A4_RATIO, //1024 * A4_RATIO,
            ],
            'virtualArea' => [
                'xmin' => 0,
                'ymin' => 0,
                'xmax' => 1024,
                'ymax' => 1448, //1024 * A4_RATIO,
            ],
        ],
        ReportConsts::LANDSCAPE => [
            'realArea'    => [
                'xmin' => 0,
                'ymin' => 0,
                'xmax' => 1448,
                'ymax' => 1024,
            ],
            'virtualArea' => [
                'xmin' => 0,
                'ymin' => 0,
                'xmax' => 1448,
                'ymax' => 1024,
            ],
        ],
    ];
    private $im           = null;
    private $draws        = [];
    private $params       = [];
    private $_currentDraw = -1;

    public function __construct()
    {
        // $this->param = array_merge($this->param, $param);
        $this->im = new \Imagick();

    }

    public function newPage(array $param = [])
    {
        $orientation = isset($param['orientation']) ? $param['orientation'] : ReportConsts::PORTRAIT;

        $param = array_merge_recursive(
            $this->default[$orientation],
            $param
        );
        $this->params[] = $param;

        $this->setRealArea($param['realArea']);
        $this->setVirtualArea($param['virtualArea']);

        $this->draws[]      = new \ImagickDraw();
        $this->_currentDraw = count($this->draws) - 1;
    }
    private function getCurrentDraw()
    {
        return $this->draws[$this->_currentDraw];
    }
    private function getCurrentParam()
    {
        return $this->params[$this->_currentDraw];
    }

    public function line($x1, $y1, $x2, $y2, $param = [])
    {
        $draw = $this->getCurrentDraw();
        $draw->setStrokeColor($param['color']);
        $draw->setStrokeWidth($this->metrik('width', $param['width']));
        $draw->line($this->x($x1), $this->y($y1), $this->x($x2), $this->y($y2));
    }

    public function box($x, $y, $dx, $dy, $param = [])
    {
        $draw = $this->getCurrentDraw();
        $draw->setStrokeWidth($this->metrik('width', $param['width']));

        $color = empty($param['color']) ? '#00000000' : $param['color'];
        $bg    = empty($param['bg']) ? '#00000000' : $param['bg'];

        $draw->setStrokeColor($color);
        $draw->setFillColor($bg);
        $draw->rectangle($this->x($x), $this->y($y), $this->x($x + $dx), $this->y($y + $dy));

    }

    public function text($x, $y, $text, $param = [])
    {
        $draw = $this->getCurrentDraw();

        $offX = 0;
        $offY = 0;

        if ($param['fontName']) {
            $draw->setFont(ReportFonts::get($param['fontName'])['fontFileName']);
            $size = $this->textSize($text, $param['fontName'], $param['fontSize']);

            if ($param['alignVert']) {
                if ($param['alignVert'] === 'bottom') {
                    $offY = $size['h'];
                } elseif ($param['alignVert'] === 'center') {
                    $offY = $size['h'] / 2;
                }
            }

            if ($param['alignHoriz']) {
                if ($param['alignHoriz'] === 'right') {
                    $offX = -$size['w'];
                } elseif ($param['alignHoriz'] === 'center') {
                    $offX = -$size['w'] / 2;
                }
            }
            // $param['colorFrame'] = '#000000';
            // if ($param['colorFrame']) {
            //     $draw->setStrokeWidth($this->metrik('width', 1));
            //     $draw->setStrokeColor($param['colorFrame']);
            //     $draw->setFillColor('#00000000');

            //     $ax = 0;
            //     $ay = 0;
            //     if ($param['alignVert'] === 'top') {
            //         $ay = -$size['h'];
            //     } elseif ($param['alignVert'] === 'center') {
            //         $ay = -$size['h'] / 2;
            //     }
            //     $draw->rectangle($this->x($x) + $ax, $this->y($y) + $ay, $this->x($x) + $size['w'] + $ax, $this->y($y) + $size['h'] + $ay);
            // }

        }

        $draw->setTextAntialias(true);
        $draw->setFontSize($this->metrik('fontSize', $param['fontSize']));
        $draw->setFillColor($param['color']);
        $draw->setStrokeColor('#00000000');
        $draw->setStrokeWidth(0);
        $draw->annotation($this->x($x) + $offX, $this->y($y) + $offY, $text);

    }

    public function textInRect($x, $y, $w, $h, string $text, array $param = [])
    {
        if ($param['fontName']) {
            $draw = $this->getCurrentDraw();
            $draw->setFont(ReportFonts::get($param['fontName'])['fontFileName']);

            $strings   = [];
            $rw        = $this->delta($w);
            $lastw     = 0;
            $rowHeight = 0;

            $texts = mb_str_split($text);
            foreach ($texts as $char) {

                if (empty($strings)) {
                    $strings[] = '';
                    $lastw     = 0;
                }
                if ($char !== "\n") {
                    if ($lastw > 0 || $char !== ' ') {
                        $charSize = $this->textSize($char, $param['fontName'], $param['fontSize']);
                        if ($rowHeight === 0) {
                            $rowHeight = $this->transform('h', $charSize['h'], 'virtual');
                        }
                        if ($lastw + $charSize['w'] < $rw) {
                            $strings[count($strings) - 1] .= $char;
                            $lastw += $charSize['w'];
                        } else {
                            $strings[] = ($char !== ' ' ? $char : '');
                            $lastw     = ($char === ' ' ? 0 : $charSize['w']);
                        }
                    }
                } else {
                    $strings[] = '';
                    $lastw     = 0;
                }
            }

            $offX = 0;
            if ($param['alignHoriz'] === 'right') {
                $offX = $w;
            } elseif ($param['alignHoriz'] === 'center') {
                $offX = $w / 2;
            }
            $param['alignVert'] = 'bottom';

            $pos = $y;
            foreach ($strings as $string) {
                $this->text($x + $offX, $pos, trim($string), $param);
                $pos += $rowHeight;
            }
        } else {
            throw new \Exception('не указано имa шрифта fontName');
        }
    }

    protected function textSize($text, $alias, $fontSize)
    {

        $p = $this->getCurrentParam();
        $r = $p['realArea'];
        $k = min($r['xmax'], $r['ymax']) / 1024;

        $m = ReportFonts::metrik($text, $alias);

        return [
            // 'w' => $fontSize * $m['w'] * $k / 4.66,
            'w' => $fontSize * $m['w'] * $k / 4.53,
            'h' => $fontSize * $m['h'] * $k * 3.385,
        ];

    }

    public function markup($param = [])
    {
        $param = array_merge([
            'frame' => true,
            'grid'  => 50,
        ], $param);

        $page = $this->getCurrentParam();
        $r    = $page['realArea'];
        $v    = $page['virtualArea'];
        $draw = $this->getCurrentDraw();

        if ($param['grid']) {
            $draw->setStrokeWidth(1);
            $draw->setStrokeColor('#AAAAAA');

            $x = $v['xmin'];
            while ($x < $v['xmax']) {
                $draw->line($this->x($x), $this->y($v['ymin']), $this->x($x), $this->y($v['ymax']));
                $x += $param['grid'];
            }
            $y = $v['ymin'];
            while ($y < $v['ymax']) {
                $draw->line($this->x($v['xmin']), $this->y($y), $this->x($v['xmax']), $this->y($y));
                $y += $param['grid'];
            }

        }

        if ($param['frame']) {
            $draw->setStrokeWidth(2);
            $draw->setStrokeColor('#FF0000');

            $draw->line($r['xmin'], $r['ymin'], $r['xmax'] - 1, $r['ymin']);
            $draw->line($r['xmin'], $r['ymin'], $r['xmin'], $r['ymax'] - 1);
            $draw->line($r['xmin'], $r['ymax'] - 1, $r['xmax'] - 1, $r['ymax'] - 1);
            $draw->line($r['xmax'] - 1, $r['ymin'], $r['xmax'] - 1, $r['ymax'] - 1);
        }
    }

    public function cross($x, $y, $param = [])
    {
        $draw  = $this->getCurrentDraw();
        $param = $this->getCurrentParam();
        $d     = abs($param['realArea']['xmax'] - $param['realArea']['xmin']) * 0.01;

        $draw->setStrokeColor('#000000');
        $draw->setStrokeWidth(1);
        $draw->line($this->x($x) + $d, $this->y($y), $this->x($x) - $d, $this->y($y));
        $draw->line($this->x($x), $this->y($y) + $d, $this->x($x), $this->y($y) - $d);

    }

    public function out(string $outTo = 'echo')
    {

        $im = $this->im;
        //--------------------------------------------------------
        $width  = 0;
        $height = 0;
        foreach ($this->params as $p) {
            $r      = $p['realArea'];
            $width  = max($r['xmax'] - $r['xmin'], $width);
            $height = $height + ($r['ymax'] - $r['ymin']);
        }
        $im->newImage($width, $height, new \ImagickPixel('white'));
        $im->setImageFormat('jpg');
        //--------------------------------------------------------

        $height = 0;
        foreach ($this->draws as $i => $draw) {
            $param = $this->params[$i];
            $r     = $param['realArea'];
            $w     = $r['xmax'] - $r['xmin'];
            $h     = $r['ymax'] - $r['ymin'];

            if ($i === 0) {
                $this->im->drawImage($draw);
            } else {
                // сложный механизм отрисовки ImagickDraw со смещением
                $from = new \Imagick();
                $from->newImage($w, $h, new \ImagickPixel('white'));
                $from->drawImage($draw);

                $draw = new \ImagickDraw();
                $draw->composite(\Imagick::COMPOSITE_DEFAULT, 0, $height, $w, $h, $from);

                // $draw->destroy();
                $from->destroy();
            }
            $this->im->drawImage($draw);
            $height += $h;
        }

        if ($outTo === 'echo') {
            header('Content-type: image/jpeg');
            echo $im->getImageBlob();
        } else {
            $im->writeImage($outTo);
        }
    }

    protected function metrik($name, $value)
    {
        $p = $this->getCurrentParam();
        $r = $p['realArea'];

        if ($name === 'width') {
            // 1 - 1000
            // 2 - 2000;
            return $value * (min($r['xmax'], $r['ymax']) / 1024);
        }
        if ($name === 'fontSize') {
            return $value * 3.5 * (min($r['xmax'], $r['ymax']) / 1024);
        }
        parent::metrik($name, $value);

    }

    public static function addFont(string $alias, string $fontFileName, array $param = [])
    {
    }

}
