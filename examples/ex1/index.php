<?php
// http://work/fmihel/report/report/examples/ex1/?jpg

// ini_set("error_log", "/var/tmp/php-error.log");
// ini_set('display_errors', 0);

use fmihel\console;
use fmihel\report\driver\ImagickDriver;
use fmihel\report\driver\PdfDriver;
use fmihel\report\Report;
use fmihel\report\ReportFonts;

require_once __DIR__ . '/../../vendor/autoload.php';

require_once __DIR__ . '/../../src/Report.php';
require_once __DIR__ . '/../../src/driver/ReportDriver.php';
require_once __DIR__ . '/../../src/driver/ImagickDriver.php';
require_once __DIR__ . '/../../src/driver/PdfDriver.php';

const FONT_PATH = __DIR__ . '/../fonts';

console::line();
console::log((isset($_REQUEST['pdf']) ? 'pdf' : 'jpg') . ' start');

try {

    PdfDriver::$PATH_TCPDF_FONTS = __DIR__ . '/../../vendor/tecnickcom/tcpdf/fonts';
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

    // $report->line(10, 10, 100, 100, ['color' => '#000000', 'width' => 1]);
    // $report->line(10, 50, 100, 140, ['color' => '#000000', 'width' => 3]);
    // $report->line(10, 100, 100, 190, ['color' => '#000000', 'width' => 6]);

    // $report->box(200, 200, 400, 50, ['color' => '#ff0000', 'bg' => '#00ff0099']);
    $vert  = 'top';
    $horiz = 'right';
    // $report->textInRect(200, 200, 400, 100, "Как говорила в июле 1805 года известная Анна Павловна Шерер, фрейлина и приближенная императрицы Марии Феодоровны, встречая важного и чиновного князя Василия, первого приехавшего на ее вечер. Анна Павловна кашляла несколько дней, у нее был грипп, как она говорила (грипп был тогда новое слово, употреблявшееся только редкими). В записочках, разосланных утром с красным лакеем, было написано без различия во всех:«Si vous n'avez rien de mieux à faire, Monsieur le comte (или mon prince), et si la perspective de passer la soirée chez une pauvre malade ne vous effraye pas trop, je serai charmée de vous voir chez moi entre 7 et 10 heures. Annette Scherer»",
    //     ['fontSize' => 5, 'alignHoriz' => 'left']);

    // $report->image(100, 100, 200, 'D:\tmp\image.jpg');

    // $report->text(400, 550, 'roboto русский', [
    $report->text(400, 550, '1234567890abcdefghij', [
        'fontName' => 'roboto',
        'fontSize' => 8,
        'maxWidth' => 150,
        // 'alignVert'  => $vert,
        // 'alignHoriz' => $horiz,
    ]);
    $report->cross(400, 550);
    $report->box(400, 550, 150, 50);

    // $report->text(400, 300, 'roboto русский', ['fontName' => 'roboto', 'fontSize' => 15, 'alignVert' => $vert, 'alignHoriz' => $horiz]);
    // $report->line(400, 100, 400, 400, ['width' => 2]);
    // $report->cross(200 + 400, 200);
    // $report->cross(200 + 400, 200);

    // $report->cross(200, 200);
    // $report->text(300, 300, 'comic русский', ['color','fontName'=>'comic']);

    // $report->text(10, 10, 'text1');

    $report->newPage(['orientation' => Report::LANDSCAPE]);
    $report->markup();
    $report->cross(100, 100);

    $report->newPage();
    $report->markup();
    $report->line(10, 10, 500, 10);
    $report->cross(100, 100);

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
