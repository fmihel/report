<?php
// http://work/fmihel/report/report/examples/ex2/?jpg

// ini_set("error_log", "/var/tmp/php-error.log");
// ini_set('display_errors', 0);
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../autoload.php';

use fmihel\console;
use fmihel\report\driver\ImagickDriver;
use fmihel\report\driver\PdfDriver;
use fmihel\report\Report;

$MEDIA = __DIR__ . '/../media';

try {
    PdfDriver::$PATH_TCPDF_FONTS = __DIR__ . '/../../vendor/tecnickcom/tcpdf/fonts';
    if (true) {
        $driver   = new PdfDriver();
        $filename = __DIR__ . '/out_report.pdf';
    } else {
        $driver   = new ImagickDriver();
        $filename = __DIR__ . '/out_report.jpg';
    }

    $report = new Report();

    $report->newPage(['orientation' => Report::PORTRAIT]);

    $w = 100;
    $h = 1000;
    $report->image(50, 200, $w, $h, $MEDIA . '/img3.png', ['scale' => 'inscribe']);
    // $report->image(50, 200, 100, 200, $MEDIA . '/img3.png', ['scale' => 'inscribe']);

    // $report->box(50, 200, 500, 300);
    $report->box(50, 200, $w, $h, ['color' => '#ff0000', 'width' => 3]);
    $report->markup();

    $report->out($driver, 0, 'echo', $filename);

} catch (\Exception $e) {
    console::error($e);

}
