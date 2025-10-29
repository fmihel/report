<?php
// http://work/fmihel/report/report/examples/ex2/?jpg

// ini_set("error_log", "/var/tmp/php-error.log");
// ini_set('display_errors', 0);
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../autoload.php';

use fmihel\console;
use fmihel\report\driver\PdfDriver;
use fmihel\report\Report;

// require_once __DIR__ . '/../../src/maps/ReportMap.php';
// require_once __DIR__ . '/../../src/Report.php';
// require_once __DIR__ . '/../../src/driver/ReportDriver.php';
// require_once __DIR__ . '/../../src/driver/ImagickDriver.php';
// require_once __DIR__ . '/../../src/driver/PdfDriver.php';

$MEDIA = __DIR__ . '/../media';

try {
    PdfDriver::$PATH_TCPDF_FONTS = __DIR__ . '/../../vendor/tecnickcom/tcpdf/fonts';

    $report = new Report();

    $report->newPage(['orientation' => Report::PORTRAIT]);

    $report->image(50, 50, 150, $MEDIA . '/img1.png');
    $report->image(50, 200, 500, $MEDIA . '/img2.jpeg');

    $report->markup();
    $report->out(new PdfDriver(), 0, 'echo', __DIR__ . '/out_report.pdf');

} catch (\Exception $e) {
    console::error($e);

}
