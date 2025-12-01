# report 0.1

Обертка для генерации отчетов на страницах формата A4. 

### Примеры использования
#### Быстрый страрт
```php

use fmihel\report\driver\ImagickDriver;
use fmihel\report\Report;
use fmihel\report\ReportFonts;

$report = new Report();

$report->newPage(['orientation' => Report::PORTRAIT]);
$report->box(200, 200, 400, 50, ['color' => '#ff0000', 'bg' => '#00ff0099']);
$report->text(200, 200, "Hello World",['color'=>'#00ff00']);

$report->out(new ImagickDriver());

```
---
# class Report

## Methods
Render report and out to target device
```php
out( $driver, $outPage = 'all', string $target = 'echo', string $filename = '') 
```


|name|type|notes|
|---|---|---|
|$driver|ReportDriver| output driver
|$outPage|string \| int|"all" or number of page for output|
|$target|string|"echo" \| "file" output device|
|$filename|string|name of file for $target='file'|

---
Create new page of report 
```php
newPage(array $param = [])
```


|name|type|notes|
|---|---|---|
|$param|array|param for created page ex: [ Report::ORIENTATION => Report::LANDSCAPE ] |

---
Draw line
```php
line( $x1, $y1, $x2, $y2, array $param=[])
```
 
|name|type|notes|
|---|---|---|
|$x1|int \| string| x start coord as int or percent|
|$y1|int \| string| y start coord as int or percent|
|$x2|int \| string| x end coord as int or percent|
|$y2|int \| string| y start coord as int or percent|
|$param|array| ```width``` - fat of line 1,2, ... <br> ```color``` - color ex:'#00ff00'|

---
Draw image
```php
image( $x, $y, $width, $height, $filename, array $param=[])
```

 
|name|type|notes|
|---|---|---|
|$x|int \| string| left top coord as int or percent|
|$y|int \| string| left top coord as int or percent|
|$width|int \| string| width as int or percent|
|$height|int \| string| height as int or percent|
|$filename|string| path to local file|
|$param|array| ```scale``` - "h" or "w" or "inscribe" - scaling method of image<br> ```border``` - color of border (default none) ex:"#00ff00"|





