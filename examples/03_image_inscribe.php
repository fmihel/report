<?php
// http://work/fmihel/report/report/examples/03_image_inscribe

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
const MEDIA     = __DIR__ . '/media';
try {

    PdfDriver::$PATH_TCPDF_FONTS = __DIR__ . '/../vendor/tecnickcom/tcpdf/fonts';

    $report = new Report();

    $report->newPage(['orientation' => Report::PORTRAIT]);
    // $report->markup();

    $report->image('5%', '5%', '90%', '90%', MEDIA . '/img2.jpeg', ['scale' => 'inscribe', 'border' => '#0000ff']);

    if (AS_PDF) {
        $report->out(new PdfDriver(), 'all', 'echo', __DIR__ . '/out_report.pdf');
    } else {
        $report->out(new ImagickDriver(), 'all', 'echo', __DIR__ . '/out_report.jpg');
    }
} catch (\Exception $e) {
    console::error($e);
    console::log((isset($_REQUEST['pdf']) ? 'pdf' : 'jpg'), ' error');

}
