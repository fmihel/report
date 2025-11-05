<?php
namespace fmihel\report\driver;

use fmihel\pdf\drivers\GSDriver;
use fmihel\pdf\PDF;
use fmihel\report\Report;
use fmihel\report\ReportFonts;
use fmihel\report\utils\Img;
use fmihel\report\utils\Math;
use fmihel\report\utils\Str;

// require_once __DIR__ . '/../Report.php';
// require_once __DIR__ . '/ReportDriver.php';
// require_once __DIR__ . '/../ReportFonts.php';
// require_once __DIR__ . '/../ReportUtils.php';

class ImagickDriver extends ReportDriver
{

    private $default = [
        Report::PORTRAIT  => [
            'realArea'    => [
                'xmin' => 0,
                'ymin' => 0,
                'xmax' => 1024,
                'ymax' => 1448, //1024 * A4_RATIO,

                // 'xmax' => 2500,
                // 'ymax' => 2500 * Report::A4_RATIO, //1024 * A4_RATIO,
            ],
            'virtualArea' => [
                'xmin' => 0,
                'ymin' => 0,
                'xmax' => 1024,
                'ymax' => 1448, //1024 * A4_RATIO,
            ],
        ],
        Report::LANDSCAPE => [
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
        $orientation = isset($param['orientation']) ? $param['orientation'] : Report::PORTRAIT;

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
        if ($param['width'] > 0) {
            $draw = $this->getCurrentDraw();
            $draw->setStrokeColor($param['color']);
            $draw->setStrokeWidth($this->metrik('width', $param['width']));
            $draw->line($this->x($x1), $this->y($y1), $this->x($x2), $this->y($y2));
        }
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

        $haveFont = isset($param['fontName']) && $param['fontName'];
        if ($haveFont) {
            $draw->setFont(ReportFonts::get($param['fontName'])['fontFileName']);
            if (isset($param['maxWidth']) && $param['maxWidth'] > 0) {
                $text = $this->textCrop($text, $param['maxWidth'], $param['fontName'], $param['fontSize']);
            }

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
        if ($haveFont) {
            $draw->setFontSize($this->metrik('fontSize', $param['fontSize']));
        }

        if (isset($param['color'])) {
            $draw->setFillColor($param['color']);
        }

        $draw->setStrokeColor('#00000000');
        $draw->setStrokeWidth(0);
        $draw->annotation($this->x($x) + $offX, $this->y($y) + $offY, $text);

    }

    public function textInRect($x, $y, $w, $h, string $text, array $param = [])
    {
        if (! $param['fontName']) {
            throw new \Exception('не указано имa шрифта fontName');
        }

        $prepare = $this->prepareText($text, $w, 0, $param['fontName'], $param['fontSize']);

        $offX = 0;
        if ($param['alignHoriz'] === 'right') {
            $offX = $w;
        } elseif ($param['alignHoriz'] === 'center') {
            $offX = $w / 2;
        }
        $param['alignVert'] = 'bottom';
        $pos                = $y;

        foreach ($prepare['strings'] as $string) {
            $this->text($x + $offX, $pos, trim($string), $param);
            $pos += $prepare['rowHeight'];
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

    protected function textCrop($text, $width, $alias, $fontSize): string
    {
        $metrik = $this->textSize($text, $alias, $fontSize);
        $width  = $this->deltaX($width);
        if ($metrik['w'] <= $width) {
            return $text;
        }

        $result   = '';
        $w_result = 0;
        $length   = mb_strlen($text);
        while ($length > 0) {

            if (($len_left = (int) ($length / 2)) === 0) {
                break;
            }

            // $len_right = $length - $len_left;

            $left  = mb_substr($text, 0, $len_left);
            $right = mb_substr($text, $len_left);

            $metrik = $this->textSize($left, $alias, $fontSize);

            $w = $w_result + $metrik['w'];
            if ($w === $width) {
                $result .= $left;
                break;
            }

            if ($w > $width) {
                $text = $left;
            } else {
                $result .= $left;
                $w_result = $w;
                $text     = $right;
            }

            $length = mb_strlen($text);
        }

        return $result;

    }

    public function image($x, $y, $w, $h, string $filename, array $param = [])
    {
        $left = $this->x($x);
        $top  = $this->y($y);
        $w    = $this->percentX($w);
        $h    = $this->percentY($h);

        $width  = 0;
        $height = 0;

        $tmp  = __DIR__ . '/tmp_' . Str::random(5) . '.jpg';
        $data = @file_get_contents($filename);
        @file_put_contents($tmp, $data);
        $img = new \Imagick();
        $img->readImage($tmp);

        if ($param['scale'] === 'h' || $param['scale'] === 'w' || $param['scale'] === 'inscribe') {

            $size  = Img::sizeFromImgStream($data);
            $scale = $size[1] / $size[0];

            if ($param['scale'] === 'h') {
                $width  = $this->deltaX(Math::translate($size[0], 0, $size[0], 0, $w));
                $height = $this->deltaY(Math::translate($size[1], 0, $size[1], 0, $w * $scale));
            } elseif ($param['scale'] === 'w') {
                $width  = $this->deltaX(Math::translate($size[0], 0, $size[0], 0, $h / $scale));
                $height = $this->deltaY(Math::translate($size[1], 0, $size[1], 0, $h));
            } else { // inscribe

                $width  = Math::translate($size[0], 0, $size[0], 0, $w);
                $height = Math::translate($size[1], 0, $size[1], 0, $w * $scale);
                if ($height > $h) {
                    $width  = Math::translate($size[0], 0, $size[0], 0, $h / $scale);
                    $height = Math::translate($size[1], 0, $size[1], 0, $h);
                }
                $width  = $this->deltaX($width);
                $height = $this->deltaY($height);
            }
        } else {
            $width  = $this->deltaX($w);
            $height = $this->deltaY($h);

        }

        $draw = $this->getCurrentDraw();
        $draw->composite(\Imagick::COMPOSITE_DEFAULT, $left, $top, $width, $height, $img);

        if (isset($param['border']) && $param['border']) {

            $draw->setStrokeColor($param['border']);
            $draw->setFillColor('#00000000');
            $draw->rectangle($left, $top, $left + $width, $top + $height);
        }

        if (file_exists($tmp)) {
            unlink($tmp);
        }

    }
    public function addPdf(string $filename, $pageNum = 0, array $param = [])
    {
        $pdf   = new PDF(new GSDriver());
        $files = $pdf->convert($filename, __DIR__, 'jpg', '$name_$i');

        $file = $files[$pageNum - 1];
        $area = $this->params[count($this->params) - 1]['virtualArea'];
        $gap  = $area['xmax'] * 0.01;
        $this->image($gap, $gap, $area['xmax'] - $gap * 2, 0, $file, ['scale' => 'h']);

        foreach ($files as $file) {
            unlink($file);
        }

    }

    public function markup($param = [])
    {
        $param = array_merge([
            'frame' => true,
            'grid'  => 50,
            'scale' => true,
        ], $param);

        $page = $this->getCurrentParam();
        $r    = $page['realArea'];
        $v    = $page['virtualArea'];
        $draw = $this->getCurrentDraw();

        if ($param['grid']) {
            $draw->setStrokeWidth(1);
            $color = '#AAAAAA';
            $x     = $v['xmin'];
            while ($x < $v['xmax']) {
                $draw->setStrokeColor($color);
                $draw->line($this->x($x), $this->y($v['ymin']), $this->x($x), $this->y($v['ymax']));
                $x += $param['grid'];
                if ($param['scale']) {
                    $this->text($x - 10, 10, $x, ['color' => '#000000']);
                }
            }
            $y = $v['ymin'];
            while ($y < $v['ymax']) {
                $draw->setStrokeColor($color);
                $draw->line($this->x($v['xmin']), $this->y($y), $this->x($v['xmax']), $this->y($y));
                $y += $param['grid'];
                if ($param['scale']) {
                    $this->text(10, $y + 5, $y, ['color' => '#000000']);
                    $this->text(990, $y + 5, $y, ['color' => '#000000']);
                }
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
