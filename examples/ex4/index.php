<?php
// http://work/fmihel/report/report/examples/ex4
error_reporting(E_ALL);

use fmihel\console;
use fmihel\report\utils\Str;
use ImalH\PDFLib\PDFLib;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../autoload.php';
$pdf = 'D:\work\fmihel\report\report\examples\media\doc1.pdf';
$out = __DIR__ . '/local.jpg';

function gsSaveAsJpg(string $fromPdfFile, string $toJpgFile, $pageNum = 0, array $params = [])
{

    $params = array_merge([
        'compressionQuality' => 100,
        'resolution'         => 200,
    ], $params);

    $tmp_pref = 'tmp-' . Str::random(5) . '-';

    $pdflib = new PDFLib();
    $pdflib->setPdfPath($fromPdfFile);
    $pdflib->setOutputPath(__DIR__);
    $pdflib->setImageFormat(PDFLib::$IMAGE_FORMAT_JPEG);
    $pdflib->setDPI($params['resolution']);
    $pdflib->setPageRange($pageNum + 1, $pageNum + 1);
    $pdflib->setFilePrefix($tmp_pref);
    $pdflib->convert();

}

try {

    gsSaveAsJpg($pdf, '');

} catch (\Exception $e) {
    console::error($e);
}
