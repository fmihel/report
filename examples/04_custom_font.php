<?php
// http://work/fmihel/report/report/examples/04_custom_font

// ini_set("error_log", "/var/tmp/php-error.log");
// ini_set('display_errors', 0);

use fmihel\console;
use fmihel\report\driver\ImagickDriver;
use fmihel\report\driver\PdfDriver;
use fmihel\report\Report;
use fmihel\report\ReportFonts;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/autoload.php';

const AS_PDF    = true;
const FONT_PATH = __DIR__ . '/../fonts';

try {

    PdfDriver::$PATH_TCPDF_FONTS = __DIR__ . '/../vendor/tecnickcom/tcpdf/fonts';
    ReportFonts::add('comic', FONT_PATH . '/comics/comics.ttf', [
        'files' => [
            FONT_PATH . '/comics/comics.ctg.z',
            FONT_PATH . '/comics/comics.php',
            FONT_PATH . '/comics/comics.z',
        ],
    ]);

    $report = new Report();

    $report->newPage(['orientation' => Report::PORTRAIT]);
    $report->markup();

    $report->text(100, 50, 'default font Roboto ', ['fontSize' => 8]);
    $report->text(100, 100, 'use font Comic ', ['fontSize' => 8, 'fontName' => 'comic']);
    $report->text(100, 150, 'русский текст по умолчанию', ['fontSize' => 8]);
    $report->text(100, 200, 'русский текст Comic', ['fontSize' => 8, 'fontName' => 'comic']);

    $text = file_get_contents(__DIR__ . '/media/long-text.txt');

    $report->textInRect(100, 250, 350, 200, $text, ['fontSize' => 4, 'overflow' => 'hidden']);
    $report->box(100, 250, 350, 200);

    $report->textInRect(550, 250, 350, 200, $text, ['fontSize' => 4, 'overflow' => 'visible']);
    $report->box(550, 250, 350, 200);

    //----------------------------------------------------------------------------------------
    // для comic шрифта отсутствует карта 
    $report->textInRect(100, 500, 350, 200, $text, ['fontSize' => 4, 'overflow' => 'hidden', 'fontName' => 'comic']);
    $report->box(100, 500, 350, 200);

    $report->textInRect(550, 500, 350, 200, $text, ['fontSize' => 4, 'overflow' => 'visible', 'fontName' => 'comic']);
    $report->box(550, 500, 350, 200);

    if (AS_PDF) {
        $report->out(new PdfDriver(), 'all', 'echo', __DIR__ . '/out_report.pdf');
    } else {
        $report->out(new ImagickDriver(), 'all', 'echo', __DIR__ . '/out_report.jpg');
    }
} catch (\Exception $e) {
    console::error($e);
    console::log((isset($_REQUEST['pdf']) ? 'pdf' : 'jpg'), ' error');

}
