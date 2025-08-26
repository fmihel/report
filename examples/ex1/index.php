<?php

// ini_set("error_log", "/var/tmp/php-error.log");
// ini_set('display_errors', 0);

use fmihel\console;
use fmihel\report\driver\ImagickDriver;
use fmihel\report\driver\PdfDriver;
use fmihel\report\Report;
use fmihel\report\ReportConsts;
use fmihel\report\ReportFonts;

require_once __DIR__ . '/../../vendor/autoload.php';

require_once __DIR__ . '/../../src/ReportConsts.php';
require_once __DIR__ . '/../../src/Report.php';
require_once __DIR__ . '/../../src/driver/ReportDriver.php';
require_once __DIR__ . '/../../src/driver/ImagickDriver.php';
require_once __DIR__ . '/../../src/driver/PdfDriver.php';

console::line();
console::log((isset($_REQUEST['pdf']) ? 'pdf' : 'jpg') . ' start');

try {
    $report = new Report();

    $report->newPage(['orientation' => ReportConsts::PORTRAIT]);
    $report->markup();
    // $report->line(10, 10, 100, 100, ['color' => '#000000', 'width' => 1]);
    // $report->line(10, 50, 100, 140, ['color' => '#000000', 'width' => 3]);
    // $report->line(10, 100, 100, 190, ['color' => '#000000', 'width' => 6]);
    $report->box(200, 200, 400, 50, ['color' => '#ff0000', 'bg' => '#00ff0099']);
    $vert  = 'bottom';
    $horiz = 'center';
    $report->textInRect(200, 200, 200, 100, "emglish съешь еще этих\n французски\n булок no english!",
        ['fontName' => 'roboto', 'fontSize' => 10, 'alignVert' => 'bottom', 'alignHoriz' => 'left']);

    // $report->text(400, 250, 'roboto русский', ['fontName' => 'roboto', 'fontSize' => 10, 'alignVert' => $vert, 'alignHoriz' => $horiz]);

    // $report->text(400, 300, 'roboto русский', ['fontName' => 'roboto', 'fontSize' => 15, 'alignVert' => $vert, 'alignHoriz' => $horiz]);
    // $report->line(400, 100, 400, 400, ['width' => 2]);
    // $report->cross(200 + 400, 200);
    // $report->cross(200 + 400, 200);

    // $report->cross(200, 200);
    // $report->text(300, 300, 'comic русский', ['color','fontName'=>'comic']);

    // $report->text(10, 10, 'text1');

    $report->newPage(['orientation' => ReportConsts::LANDSCAPE]);
    $report->markup();
    $report->cross(100, 100);

    $report->newPage();
    $report->markup();
    $report->line(10, 10, 500, 10);
    $report->cross(100, 100);

    // $report->out(ImagickDriver::create(), '');

    $FONT_PATH = __DIR__ . '/../../fonts';
    ReportFonts::add('roboto', $FONT_PATH . '/roboto/roboto.ttf', [
        'files' => [
            $FONT_PATH . '/roboto/roboto.ctg.z',
            $FONT_PATH . '/roboto/roboto.php',
            $FONT_PATH . '/roboto/roboto.z',
        ],
    ]);
    ReportFonts::add('comic', $FONT_PATH . '/comics/comics.ttf', [
        'files' => [
            $FONT_PATH . '/comics/comics.ctg.z',
            $FONT_PATH . '/comics/comics.php',
            $FONT_PATH . '/comics/comics.z',
        ],
    ]);

    if (isset($_REQUEST['pdf'])) {
        $report->out(new PdfDriver(), 0, 'echo', __DIR__ . '/out_report.pdf');
    } else {
        $report->out(new ImagickDriver(), 0, 'echo', __DIR__ . '/out_report.jpg');
    }
    console::log((isset($_REQUEST['pdf']) ? 'pdf' : 'jpg'), ' ok');
} catch (\Exception $e) {
    console::error($e);
    console::log((isset($_REQUEST['pdf']) ? 'pdf' : 'jpg'), ' error');

}
