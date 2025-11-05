<?php
// http://work/fmihel/report/report/examples/01_simple

// ini_set("error_log", "/var/tmp/php-error.log");
// ini_set('display_errors', 0);

use fmihel\console;
use fmihel\report\driver\ImagickDriver;
use fmihel\report\driver\PdfDriver;
use fmihel\report\Report;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/autoload.php';

const AS_PDF = true;

const FONT_PATH = __DIR__ . '/../fonts';

try {

    PdfDriver::$PATH_TCPDF_FONTS = __DIR__ . '/../vendor/tecnickcom/tcpdf/fonts';

    $report = new Report();

    $report->newPage(['orientation' => Report::PORTRAIT]);
    $report->markup();

    $report->cross(100, 50);
    $report->text(100, 50, 'line: ', ['fontSize' => 8]);
    $report->line(250, 75, 400, 75, ['color' => '#000000', 'width' => 2]);

    $report->cross(100, 150);
    $report->text(100, 150, 'box: ', ['fontSize' => 8]);
    $report->box(250, 150, 150, 50, ['color' => '#000000', 'width' => 2]);

    $report->cross(100, 250);
    $report->text(100, 250, 'box: ', ['fontSize' => 8]);
    $report->box(250, 250, 150, 50, ['color' => '#000000', 'width' => 2, 'bg' => '#ff0000']);

    $report->cross(100, 350);
    $report->text(100, 350, 'box: ', ['fontSize' => 8]);
    $report->box(250, 350, 150, 50, ['color' => '', 'bg' => '#ff000055']);

    if (AS_PDF) {
        $report->out(new PdfDriver(), 'all', 'echo', __DIR__ . '/out_report.pdf');
    } else {
        $report->out(new ImagickDriver(), 'all', 'echo', __DIR__ . '/out_report.jpg');
    }
} catch (\Exception $e) {
    console::error($e);
    console::log((isset($_REQUEST['pdf']) ? 'pdf' : 'jpg'), ' error');

}
