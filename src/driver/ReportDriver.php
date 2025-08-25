<?php
namespace fmihel\report\driver;

require_once __DIR__ . '/../routines/translate.php';

use function fmihel\report\routines\translate;

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
    public function text($x, $y, $text, $param = [])
    {
        throw new \Exception('не реализован метод ' . __METHOD__);
    }

    public function fontName(string $name, $param = [])
    {
        throw new \Exception('не реализован метод ' . __METHOD__);
    }
    public function fontSize(string $name, $param = [])
    {
        throw new \Exception('не реализован метод ' . __METHOD__);
    }

    public function markup($param = [])
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
        return translate($virtualX, $v['xmin'], $v['xmax'], $r['xmin'], $r['xmax'], 2);
    }
    public function y($virtualY)
    {
        $r = $this->realArea;
        $v = $this->virtualArea;
        return translate($virtualY, $v['ymin'], $v['ymax'], $r['ymin'], $r['ymax'], 2);
    }
    public function delta($virtualDelta)
    {
        $r = $this->realArea;
        $v = $this->virtualArea;
        return abs($this->x($virtualDelta) - $this->x(0));
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

    public function textSize($text, $alias, $fontSize)
    {
        throw new \Exception('не реализован метод ' . __METHOD__);
    }

    public static function addFont(string $alias, string $fontFileName, array $param = [])
    {
        throw new \Exception('не реализован метод ' . __METHOD__);
    }

}
