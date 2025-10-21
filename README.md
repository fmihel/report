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

