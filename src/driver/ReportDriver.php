<?php
namespace fmihel\report\driver;

use fmihel\report\utils\Math;

class ReportDriver
{

    private $realArea = [
        'xmax' => 100,
        'ymax' => 100,
        'xmin' => 0,
        'ymin' => 0,
    ];

    private $virtualArea = [
        'xmax' => 1000,
        'ymax' => 1000,
        'xmin' => 0,
        'ymin' => 0,
    ];

    public function newPage()
    {
        throw new \Exception('не реализован метод ' . __METHOD__);
    }
    public function out(string $outTo = 'echo')
    {
        throw new \Exception('не реализован метод ' . __METHOD__);
    }

    public function line($x1, $y1, $x2, $y2, $param = [])
    {
        throw new \Exception('не реализован метод ' . __METHOD__);
    }
    public function box($x, $y, $dx, $dy, $param = [])
    {
        throw new \Exception('не реализован метод ' . __METHOD__);
    }

    public function cross($x, $y, $param = [])
    {
        throw new \Exception('не реализован метод ' . __METHOD__);
    }
    public function text($x, $y, string $text, $param = [])
    {
        throw new \Exception('не реализован метод ' . __METHOD__);
    }

    public function textInRect($x, $y, $w, $h, string $text, array $param = [])
    {
        throw new \Exception('не реализован метод ' . __METHOD__);
    }

    /** разбивает текст на строки, чтобы вписать его в область шириной $w
     *  в $rowHeight можно указать высоту строки если не указать (0)
     *  тоs будет использоваться высота символа для текущешго шрифта и размера
     */
    protected function prepareText(string $text, $w, $rowHeight, string $alias, string $fontSize): array
    {
        if (! $alias) {
            throw new \Exception('не указано имя шрифта $alias');
        }

        $strings   = [];
        $rw        = $this->delta($w);
        $lastw     = 0;
        $allHeight = 0;
        $texts     = mb_str_split($text);

        foreach ($texts as $char) {

            if (empty($strings)) {
                $strings[] = '';
                $lastw     = 0;
            }
            if ($char !== "\n") {
                if ($lastw > 0 || $char !== ' ') {
                    $charSize = $this->textSize($char, $alias, $fontSize);
                    if ($rowHeight === 0) {
                        $rowHeight = $this->transform('h', $charSize['h'], 'virtual');
                    }
                    $allHeight += $rowHeight;

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

        return [
            'strings'   => $strings,
            'height'    => $allHeight,
            'width'     => $w,
            'rowHeight' => $rowHeight,
        ];

    }

    protected function textSize($text, $alias, $fontSize)
    {
        throw new \Exception('не реализован метод ' . __METHOD__);
    }
    protected function textCrop($text, $width, $alias, $fontSize): string
    {
        throw new \Exception('не реализован метод ' . __METHOD__);
    }
    public function image($x, $y, $w, $h, string $filename, array $param = [])
    {
        throw new \Exception('не реализован метод ' . __METHOD__);
    }

    public function markup($param = [])
    {
        throw new \Exception('не реализован метод ' . __METHOD__);
    }
    public function addPdf(string $filename, $pageNum = 0, array $param = [])
    {
        throw new \Exception('не реализован метод ' . __METHOD__);
    }

    public function setRealArea(array $realArea)
    {
        $this->realArea = array_merge($this->realArea, $realArea);
    }
    public function setVirtualArea(array $virtualArea)
    {
        $this->virtualArea = array_merge($this->virtualArea, $virtualArea);
    }

    public function x($virtualX)
    {
        $r = $this->realArea;
        $v = $this->virtualArea;
        return Math::translate($virtualX, $v['xmin'], $v['xmax'], $r['xmin'], $r['xmax'], 2);
    }
    public function y($virtualY)
    {
        $r = $this->realArea;
        $v = $this->virtualArea;
        return Math::translate($virtualY, $v['ymin'], $v['ymax'], $r['ymin'], $r['ymax'], 2);
    }

    public function delta($virtualDelta)
    {
        $r = $this->realArea;
        $v = $this->virtualArea;
        return abs($this->x($virtualDelta) - $this->x(0));
    }
    public function transform(string $coordName, $value, string $convertTo)
    {
        $r = $this->realArea;
        $v = $this->virtualArea;
        if ($convertTo === 'real') {
            if ($coordName === 'x') {
                return Math::translate($value, $v['xmin'], $v['xmax'], $r['xmin'], $r['xmax'], 2);
            }
            if ($coordName === 'y') {
                return Math::translate($value, $v['ymin'], $v['ymax'], $r['ymin'], $r['ymax'], 2);
            }
            if ($coordName === 'w' || $coordName === 'dx') {
                return abs(Math::translate($value, $v['xmin'], $v['xmax'], $r['xmin'], $r['xmax'], 2)
                     - Math::translate(0, $v['xmin'], $v['xmax'], $r['xmin'], $r['xmax'], 2));
            }
            if ($coordName === 'h' || $coordName === 'dy') {
                return abs(Math::translate($value, $v['ymin'], $v['ymax'], $r['ymin'], $r['ymax'], 2)
                     - Math::translate(0, $v['ymin'], $v['ymax'], $r['ymin'], $r['ymax'], 2));
            }
        } else {
            if ($coordName === 'x') {
                return Math::translate($value, $r['xmin'], $r['xmax'], $v['xmin'], $v['xmax'], 2);
            }
            if ($coordName === 'y') {
                return Math::translate($value, $r['ymin'], $r['ymax'], $v['ymin'], $v['ymax'], 2);
            }
            if ($coordName === 'w' || $coordName === 'dx') {
                return abs(Math::translate($value, $r['xmin'], $r['xmax'], $v['xmin'], $v['xmax'], 2)
                     - Math::translate(0, $r['xmin'], $r['xmax'], $v['xmin'], $v['xmax'], 2));
            }
            if ($coordName === 'h' || $coordName === 'dy') {
                return abs(Math::translate($value, $r['ymin'], $r['ymax'], $v['ymin'], $v['ymax'], 2)
                     - Math::translate(0, $r['ymin'], $r['ymax'], $v['ymin'], $v['ymax'], 2));
            }

        }
    }

    public function width()
    {
        $r = $this->realArea;
        return abs($r['xmax'] - $r['xmin']);
    }
    public function height()
    {
        $r = $this->realArea;
        return abs($r['ymax'] - $r['ymin']);
    }

    protected function metrik($name, $value)
    {
        throw new \Exception('не реализован метод ' . __METHOD__ . ' для параметра ' . $name);
    }

    public static function addFont(string $alias, string $fontFileName, array $param = [])
    {
        throw new \Exception('не реализован метод ' . __METHOD__);
    }

}
