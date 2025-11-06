<?php
namespace fmihel\report\driver;

use fmihel\console;
use fmihel\pdf\drivers\GSDriver;
use fmihel\pdf\PDF;
use fmihel\report\Report;
use fmihel\report\ReportFonts;
use fmihel\report\utils\Color;
use fmihel\report\utils\Img;
use fmihel\report\utils\Math;
use setasign\Fpdi\Tcpdf\Fpdi;

class PdfDriver extends ReportDriver
{
    const PATH_TCPDF_FONTS_DEFAULT = __DIR__ . '/../../../../../vendor/tecnickcom/tcpdf/fonts';

    private $default = [
        Report::PORTRAIT  => [
            'realArea'    => [
                'xmin' => 0,
                'ymin' => 0,
                'xmax' => 211,
                'ymax' => 298, //211 * A4_RATIO
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

    public static $PATH_TCPDF_FONTS = self::PATH_TCPDF_FONTS_DEFAULT;

    public function __construct()
    {
        // $this->pdf = new TCPDF("P", "mm", "A4", true, 'utf8', false);
        $this->pdf = new Fpdi("P", "mm", "A4", true, 'utf8', false);
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

        $orientation = isset($param['orientation']) ? $param['orientation'] : Report::PORTRAIT;
        $param       = array_merge(
            $this->default[$orientation],
            $param
        );
        $this->params[] = $param;
        $this->setRealArea($param['realArea']);
        $this->setVirtualArea($param['virtualArea']);

        $this->pdf->AddPage($orientation === Report::LANDSCAPE ? 'L' : 'P');
        $this->currentPage = count($this->params) - 1;

    }
    private function getCurrentParam(): array
    {
        return empty($this->params) ? $this->default[Report::PORTRAIT] : $this->params[$this->currentPage];
    }

    public function line($x1, $y1, $x2, $y2, $param = [])
    {
        if ($param['width'] > 0) {
            $pdf = $this->pdf;
            $pdf->SetLineWidth($this->metrik('width', $param['width']));
            $pdf->SetDrawColorArray(Color::hexToRgb($param['color']));
            $pdf->Line($this->x($x1), $this->y($y1), $this->x($x2), $this->y($y2));
        }

    }
    public function box($x, $y, $dx, $dy, $param = [])
    {
        $pdf = $this->pdf;
        $out = '';
        if (isset($param['width']) && $param['width'] > 0) {
            $pdf->SetLineWidth($this->metrik('width', $param['width']));

            if (! empty($param['color'])) {
                $out .= 'D';
                $pdf->SetDrawColorArray(Color::hexToRgb($param['color']));
            }
        }
        if (isset($param['bg']) && (! empty($param['bg']))) {
            $out .= 'F';
        }

        if ($out) {
            $this->pdf->Rect($this->x($x), $this->y($y), $this->deltaX($dx), $this->deltaY($dy), $out, [], Color::hexToRgbw($param['bg']));
        }
    }

    public function text($x, $y, $text, $param = [])
    {

        $offX = 0;
        $offY = 0;

        $haveFont = isset($param['fontName']) && $param['fontName'];

        if ($haveFont) {

            $this->pdf->SetFont(ReportFonts::get($param['fontName'])['name']);
            if (isset($param['maxWidth']) && $param['maxWidth'] > 0) {
                $text = $this->textCrop($text, $param['maxWidth'], $param['fontName'], $param['fontSize']);
            }
            $size = $this->textSize($text, $param['fontName'], $param['fontSize']);

            if ($param['alignVert']) {
                if ($param['alignVert'] === 'top') {
                    $offY = -$size['h'];
                } elseif ($param['alignVert'] === 'center') {
                    $offY = -$size['h'] / 2;
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
            //     $ax = 0;
            //     $ay = 0;
            //     if ($param['alignVert'] === 'top') {
            //         $ay = -$size['h'];
            //     } elseif ($param['alignVert'] === 'center') {
            //         $ay = -$size['h'] / 2;
            //     }
            //     $this->pdf->SetDrawColorArray(hexToRgb($param['colorFrame']));
            //     $this->pdf->Rect($this->x($x) + $ax, $this->y($y) + $ay, $size['w'], $size['h'], 'D', [], []);
            // }

        }
        if ($haveFont) {

            $this->pdf->SetFontSize($this->metrik('fontSize', $param['fontSize']));
        }
        if (isset($param['color'])) {
            $this->pdf->SetTextColorArray(Color::hexToRgb($param['color']));
        }

        // $size = $this->textSize($text, $param['fontName'], $param['fontSize']);

        $this->pdf->Text($this->x($x) + $offX, $this->y($y) + $offY, $text);

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
            if ($param['overflow'] === 'hidden' && ($pos + $prepare['rowHeight'] >= ($y + $h))) {
                break;
            }
        }
    }
    protected function textSize($text, $alias, $fontSize)
    {
        $p = $this->getCurrentParam();
        $r = $p['realArea'];
        $k = min($r['xmax'], $r['ymax']) / 211;

        $m = ReportFonts::metrik($text, $alias);

        return [
            'w' => $fontSize * $m['w'] * $k / 22,
            'h' => $fontSize * $m['h'] * $k * 0.7,
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

        if ($param['scale'] === 'h' || $param['scale'] === 'w' || $param['scale'] === 'inscribe') {

            if ($stream = @fopen($filename, 'r')) {

                $data = stream_get_contents($stream, -1);
                fclose($stream);

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
            }
        } else {
            $width  = $this->deltaX($w);
            $height = $this->deltaY($h);
        }

        $this->pdf->Image($filename, $left, $top, $width, $height);

        if (isset($param['border']) && $param['border']) {
            $this->pdf->SetDrawColorArray(Color::hexToRgb($param['border']));
            $this->pdf->Rect($left, $top, $width, $height, 'D');
        }
    }

    public function cross($x, $y, $param = [])
    {
        $pdf   = $this->pdf;
        $param = $this->getCurrentParam();
        $d     = abs($param['realArea']['xmax'] - $param['realArea']['xmin']) * 0.01;

        $pdf->SetLineWidth(0.2);
        $pdf->SetDrawColorArray(Color::hexToRgb('#000000'));

        $pdf->Line($this->x($x), $this->y($y) - $d, $this->x($x), $this->y($y) + $d);
        $pdf->Line($this->x($x) - $d, $this->y($y), $this->x($x) + $d, $this->y($y));

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
        $pdf  = $this->pdf;
        $pdf->SetLineWidth(0.1);

        if ($param['grid']) {
            $pdf->SetDrawColorArray(Color::hexToRgb('#cccccc'));
            $pdf->SetLineWidth(0.1);

            $x = $v['xmin'];
            while ($x < $v['xmax']) {
                $pdf->line($this->x($x), $this->y($v['ymin']), $this->x($x), $this->y($v['ymax']));
                $x += $param['grid'];
                if ($param['scale']) {
                    $this->text($x - 22, 10, $x, ['color' => '#9F9F9F']);
                }

            }
            $y = $v['ymin'];
            while ($y < $v['ymax']) {
                $pdf->line($this->x($v['xmin']), $this->y($y), $this->x($v['xmax']), $this->y($y));
                $y += $param['grid'];
                if ($param['scale']) {
                    $this->text(6, $y - 14, $y, ['color' => '#9F9F9F']);
                    $this->text(960, $y - 14, $y, ['color' => '#9F9F9F']);
                }

            }

        }

        if ($param['frame']) {
            $pdf->SetLineWidth(1);
            $pdf->SetDrawColorArray(Color::hexToRgb('#ff0000'));

            $pdf->line($r['xmin'], $r['ymin'], $r['xmax'] - 1, $r['ymin']);
            $pdf->line($r['xmin'], $r['ymin'], $r['xmin'], $r['ymax'] - 1);
            $pdf->line($r['xmin'], $r['ymax'] - 1, $r['xmax'] - 1, $r['ymax'] - 1);
            $pdf->line($r['xmax'] - 1, $r['ymin'], $r['xmax'] - 1, $r['ymax'] - 1);
        }

    }

    private function _add_pdf_use_fpdi($filename, $pageNum, array $param = []): bool
    {
        try {
            $count = $this->pdf->setSourceFile($filename);
            // for ($i = 1; $i <= $count; $i++) {
            // $tplidx      = $this->pdf->importPage($i); // Import the page

            $tplidx = $this->pdf->importPage($pageNum); // Import the page

            // $info   = $this->pdf->getTemplateSize($tplidx);
            // $orientation = substr(strtolower($info['orientation']), 0, 1);
            // $this->newPage(['orientation' => ($orientation === 'l' ? Report::LANDSCAPE : Report::PORTRAIT)]);

            $this->pdf->useTemplate($tplidx); // Use the imported page as a template

            // }
            return true;
        } catch (\Exception $e) {
        }
        return false;

    }
    private function _add_pdf_use_pdf($filename, $pageNum, array $param = []): bool
    {
        try {
            $driver = new GSDriver();
            $pdf    = new PDF($driver);
            // $count  = $pdf->countPage($filename);
            $files = $pdf->convert($filename, __DIR__, 'jpg', '$name_$i', ['dpi' => 200]);

            $file = $files[$pageNum - 1];
            $area = $this->params[$this->currentPage]['virtualArea'];
            $gap  = $area['xmax'] * 0.01;
            $this->image($gap, $gap, $area['xmax'] - $gap * 2, 0, $file, ['scale' => 'h']);

            foreach ($files as $file) {
                unlink($file);
            }
            return true;
        } catch (\Exception $e) {
            console::log($e);
        }
        return false;
    }
    public function addPdf(string $filename, $pageNum = 0, array $param = [])
    {
        try {
            if (! $this->_add_pdf_use_fpdi($filename, $pageNum, $param)) {
                if (! $this->_add_pdf_use_pdf($filename, $pageNum, $param)) {
                    throw new \Exception('не удалось добавить pdf ' . $filename);
                }
            }

        } catch (\Exception $e) {
            console::error($e);
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
