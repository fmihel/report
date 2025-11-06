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
### class Report

Render report and out to target device\
 ```function out($driver, $outPage = 'all', string $target = 'echo', string $filename = '')```

|name|type|notes|
|---|---|---|
|$driver|ReportDriver| output driver
|$outPage|string \| int|all or number of page for output|
|$target|string|'echo' \| 'file' output divice|
|$filename|string|name of file for $target='file'|

---
Create new page of report \
```function newPage(array $param = [])```

|name|type|notes|
|---|---|---|
|$param|array|param for created page ex: ['orientation'=>Report::LANDSCAPE] |


