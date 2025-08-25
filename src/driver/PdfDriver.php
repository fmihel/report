<?php
namespace fmihel\report\driver;

require_once __DIR__ . '/ReportDriver.php';
require_once __DIR__ . '/../routines/hexToRgb.php';
require_once __DIR__ . '/../routines/hexToRgba.php';
require_once __DIR__ . '/../routines/hexToRgbw.php';
require_once __DIR__ . '/../ReportFonts.php';

use fmihel\report\ReportFonts;
use function fmihel\report\routines\hexToRgb;
use function fmihel\report\routines\hexToRgbw;
use TCPDF;

// use TCPDF_FONTS;
const PATH_TCPDF_FONTS_DEFAULT = __DIR__ . '/../../vendor/tecnickcom/tcpdf/fonts';

class PdfDriver extends ReportDriver
{
    private $default = [
        PORTRAIT  => [
            'realArea'    => [
                'xmin' => 0,
                'ymin' => 0,
                'xmax' => 211,
                'ymax' => 298, //210 * A4_RATIO
            ],
            'virtualArea' => [
                'xmin' => 0,
                'ymin' => 0,
                'xmax' => 1024,
                'ymax' => 1448, //1024 * A4_RATIO,
            ],
        ],
        LANDSCAPE => [
            'realArea'    => [
                'xmin' => 0,
                'ymin' => 0,
                'xmax' => 298,
                'ymax' => 211,
            ],
            'virtualArea' => [
                'xmin' => 0,
                'ymin' => 0,
                'xmax' => 1448,
                'ymax' => 1024,
            ],
        ],
    ];

    private $pdf;
    private $params      = [];
    private $currentPage = -1;

    public static $PATH_TCPDF_FONTS = PATH_TCPDF_FONTS_DEFAULT;

    public function __construct()
    {
        $this->pdf = new TCPDF("P", "mm", "A4", true, 'utf8', false);
        $this->pdf->SetAutoPageBreak(false);
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);

        // $fontname = TCPDF_FONTS::addTTFfont(__DIR__.'/fonts/comic/font.ttf', 'cp1252', '', 96);
        // $this->pdf->SetFont('roboto','',10);
        // $fontname = TCPDF_FONTS::addTTFfont(__DIR__.'/fonts/comics/comics.ttf' );
        // $this->pdf->SetFont($fontname, '', 14, '', false);

        //  $this->pdf->SetFont(__DIR__.'/fonts/roboto/roboto.ttf','',10);
        // $this->pdf->SetFont('roboto','',);
        // use the font

    }
    public function newPage(array $param = [])
    {
        $orientation = isset($param['orientation']) ? $param['orientation'] : PORTRAIT;
        $param       = array_merge_recursive(
            $this->default[$orientation],
            $param
        );
        $this->params[] = $param;
        $this->setRealArea($param['realArea']);
        $this->setVirtualArea($param['virtualArea']);

        $this->pdf->AddPage($orientation === LANDSCAPE ? 'L' : 'P');
        $this->currentPage = count($this->params) - 1;

    }
    private function getCurrentParam(): array
    {
        return $this->params[$this->currentPage];
    }

    public function line($x1, $y1, $x2, $y2, $param = [])
    {
        $pdf = $this->pdf;

        $pdf->SetLineWidth($this->metrik('width', $param['width']));
        $pdf->SetDrawColorArray(hexToRgb($param['color']));

        $pdf->Line($this->x($x1), $this->y($y1), $this->x($x2), $this->y($y2));

    }
    public function box($x, $y, $dx, $dy, $param = [])
    {
        $pdf = $this->pdf;
        $pdf->SetLineWidth($this->metrik('width', $param['width']));

        $out = '';
        if (! empty($param['color'])) {
            $out .= 'D';
            $pdf->SetDrawColorArray(hexToRgb($param['color']));
        }
        if (! empty($param['bg'])) {
            $out .= 'F';
        }

        if ($out) {
            $this->pdf->Rect($this->x($x), $this->y($y), $this->delta($dx), $this->delta($dy), $out, [], hexToRgbw($param['bg']));
        }

    }

    public function text($x, $y, $text, $param = [])
    {
        if ($param['fontName']) {
            $this->pdf->SetFont(ReportFonts::get($param['fontName'])['name']);
        }
        $this->pdf->SetFontSize($this->metrik('fontSize', $param['fontSize']));
        $this->pdf->SetTextColorArray(hexToRgb($param['color']));
        $this->pdf->Text($this->x($x), $this->y($y), $text);

    }
    public function cross($x, $y, $param = [])
    {
        $pdf   = $this->pdf;
        $param = $this->getCurrentParam();
        $d     = abs($param['realArea']['xmax'] - $param['realArea']['xmin']) * 0.01;

        $pdf->SetLineWidth(0.2);
        $pdf->SetDrawColorArray(hexToRgb('#000000'));

        $pdf->Line($this->x($x), $this->y($y) - $d, $this->x($x), $this->y($y) + $d);
        $pdf->Line($this->x($x) - $d, $this->y($y), $this->x($x) + $d, $this->y($y));

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
        $pdf  = $this->pdf;
        $pdf->SetLineWidth(0.1);

        if ($param['grid']) {
            $pdf->SetDrawColorArray(hexToRgb('#cccccc'));
            $pdf->SetLineWidth(0.1);

            $x = $v['xmin'];
            while ($x < $v['xmax']) {
                $pdf->line($this->x($x), $this->y($v['ymin']), $this->x($x), $this->y($v['ymax']));
                $x += $param['grid'];
            }
            $y = $v['ymin'];
            while ($y < $v['ymax']) {
                $pdf->line($this->x($v['xmin']), $this->y($y), $this->x($v['xmax']), $this->y($y));
                $y += $param['grid'];
            }

        }

        if ($param['frame']) {
            $pdf->SetLineWidth(1);
            $pdf->SetDrawColorArray(hexToRgb('#ff0000'));

            $pdf->line($r['xmin'], $r['ymin'], $r['xmax'] - 1, $r['ymin']);
            $pdf->line($r['xmin'], $r['ymin'], $r['xmin'], $r['ymax'] - 1);
            $pdf->line($r['xmin'], $r['ymax'] - 1, $r['xmax'] - 1, $r['ymax'] - 1);
            $pdf->line($r['xmax'] - 1, $r['ymin'], $r['xmax'] - 1, $r['ymax'] - 1);
        }

    }

    public function out(string $outTo = 'echo')
    {
        if ($outTo === 'echo') {
            $this->pdf->Output('report', 'I');
        } else {
            $this->pdf->Output($outTo, 'F');
        }

    }

    // private function toColor256(array $rgb)
    // {
    //     return [$rgb[0] * 256, $rgb[1] * 256, $rgb[2] * 256];
    // }

    protected function metrik($name, $value)
    {

        if ($name === 'width') {
            return $value * 0.2;
        }

        if ($name === 'fontSize') {
            return $value * 2;
        }
        parent::metrik($name, $value);
    }

    public static function addFont(string $alias, string $fontFileName, array $param = [])
    {

        $pathFonts = self::$PATH_TCPDF_FONTS;

        $param = array_merge([
            'rewrite' => false,
            'files'   => [],

        ], $param);

        $all = array_merge([$fontFileName], $param['files']);
        foreach ($all as $file) {

            $to = $pathFonts . "/" . basename($file);
            if (! file_exists($to) || $param['rewrite']) {
                copy($file, $to);
            }
        }
    }
}
