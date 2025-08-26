<?php
namespace fmihel\report;

require_once __DIR__ . '/ReportFonts.php';

class Report
{
    const RE_PAGE         = 'page';
    const RE_LINE         = 'line';
    const RE_BOX          = 'box';
    const RE_TEXT         = 'text';
    const RE_TEXT_IN_RECT = 'textInRect';
    const RE_CROSS        = 'cross';
    const RE_IMAGE        = 'image';
    const RE_MARKUP       = 'markup';

    public $pages         = [];
    private $_currentPage = -1;

    public $default = [
        'page'    => [],
        'objects' => [
            self::RE_LINE         => [
                'color' => '#000000',
                'width' => 1,
            ],
            self::RE_BOX          => [
                'color' => '#000000',
                'bg'    => '',
                'width' => 1,
            ],

            self::RE_TEXT         => [
                'color'      => '#000000',
                'fontSize'   => 12,
                'fontName'   => 'roboto',
                'alignVert'  => 'bottom',
                'alignHoriz' => 'left',
                // 'colorFrame' => '',
            ],
            self::RE_TEXT_IN_RECT => [
                'color'      => '#000000',
                'fontSize'   => 12,
                'fontName'   => 'roboto',
                'alignHoriz' => 'left',
            ],
            self::RE_IMAGE        => [

            ],
            self::RE_CROSS        => [],
            self::RE_MARKUP       => [],
        ],
    ];

    function __construct()
    {
    }

    public function out($driver, $outPage = 'all', string $target = 'echo', string $filename = '')
    {

        $default = $this->default;
        ReportFonts::assignToDriver($driver);

        foreach ($this->pages as $pageNum => $page) {

            if ($outPage === 'all' || $outPage == $pageNum) {
                $driver->newPage($page['param']);

                foreach ($page['objects'] as $item) {

                    $name = $item['name'];
                    $data = $item['data'];

                    if ($name === self::RE_LINE) {
                        $driver->line($data['x1'], $data['y1'], $data['x2'], $data['y2'], array_merge($default['objects'][self::RE_LINE], $data['param']));
                    } elseif ($name === self::RE_BOX) {
                        $driver->box($data['x'], $data['y'], $data['dx'], $data['dy'], array_merge($default['objects'][self::RE_BOX], $data['param']));
                    } elseif ($name === self::RE_TEXT) {
                        $driver->text($data['x'], $data['y'], $data['text'], array_merge($default['objects'][self::RE_TEXT], $data['param']));
                    } elseif ($name === self::RE_TEXT_IN_RECT) {
                        $driver->textInRect($data['x'], $data['y'], $data['w'], $data['h'], $data['text'], array_merge($default['objects'][self::RE_TEXT_IN_RECT], $data['param']));
                    } elseif ($name === self::RE_CROSS) {
                        $driver->cross($data['x'], $data['y'], array_merge($default['objects'][self::RE_CROSS], $data['param']));
                    } elseif ($name === self::RE_MARKUP) {
                        $driver->markup(array_merge($default['objects'][self::RE_MARKUP], $data['param']));
                    } elseif ($name === self::RE_IMAGE) {
                        $driver->image($data['x'], $data['y'], $data['w'], $data['filename'], array_merge($default['objects'][self::RE_IMAGE], $data['param']));
                    }
                }
            }

        }

        $driver->out($target === 'echo' ? $target : $filename);
    }

    public function newPage(array $param = [])
    {
        $this->pages[] = [
            'param'   => array_merge($this->default['page'], $param),
            'objects' => [],
        ];
        $this->_currentPage = count($this->pages) - 1;
    }

    private function addObject(array $object)
    {
        if ($this->_currentPage === -1) {
            $this->newPage();
        }
        $this->pages[$this->_currentPage]['objects'][] = $object;
    }

    public function line($x1, $y1, $x2, $y2, array $param = [])
    {
        $this->addObject(['name' => self::RE_LINE, 'data' => ['x1' => $x1, 'y1' => $y1, 'x2' => $x2, 'y2' => $y2, 'param' => $param]]);
    }
    public function box($x, $y, $dx, $dy, array $param = [])
    {
        $this->addObject(['name' => self::RE_BOX, 'data' => ['x' => $x, 'y' => $y, 'dx' => $dx, 'dy' => $dy, 'param' => $param]]);
    }

    public function cross($x, $y, array $param = [])
    {
        $this->addObject(['name' => self::RE_CROSS, 'data' => ['x' => $x, 'y' => $y, 'param' => $param]]);
    }

    public function text($x, $y, $text, array $param = [])
    {
        $this->addObject(['name' => self::RE_TEXT, 'data' => ['x' => $x, 'y' => $y, 'text' => $text, 'param' => $param]]);
    }

    public function textInRect($x, $y, $w, $h, string $text, array $param = [])
    {
        $this->addObject(['name' => self::RE_TEXT_IN_RECT, 'data' => ['x' => $x, 'y' => $y, 'w' => $w, 'h' => $h, 'text' => $text, 'param' => $param]]);
    }
    public function image($x, $y, $w, string $filename, array $param = [])
    {
        $this->addObject(['name' => self::RE_IMAGE, 'data' => ['x' => $x, 'y' => $y, 'w' => $w, 'filename' => $filename, 'param' => $param]]);
    }

    public function markup($param = [])
    {
        $this->addObject(['name' => self::RE_MARKUP, 'data' => ['param' => $param]]);
    }

}
