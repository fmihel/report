<?php
// http://work/fmihel/report/report/examples/ex3/

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
    $report->markup();
    $report->line(10, 10, 100, 100);

    $report->addPdf($MEDIA . '/doc4.pdf');

    $report->newPage(['orientation' => Report::PORTRAIT]);
    $report->markup();
    $report->line(10, 10, 100, 100);

    $report->out($driver, 'all', 'echo', $filename);

} catch (\Exception $e) {
    console::error($e);

}
