# report 

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
## Методы
Выводит сформированный отчет на устройство ( в файл или в косоль )
```php
out( $driver, $outPage = 'all', string $target = 'echo', string $filename = '') 
```

|name|type|notes|
|---|---|---|
|$driver|ReportDriver| драйвер вывода см. class ReportDriver|
|$outPage|string \| int|принимает значение либо "all" либо номер страницы к выводу|
|$target|string|"echo" \| "file", указывает на какое устройство отправить отчет|
|$filename|string|имя файла куда сохраниться отчет, если $target='file'|

---
Создает новую страницу
```php
newPage(array $param = [])
```

|name|type|notes|
|---|---|---|
|$param|array|```orientation``` => Report::LANDSCAPE \| Report::PORTRAIT  |

---
Рисует линию
```php
line( $x1, $y1, $x2, $y2, array $param=[])
```
 
|name|type|notes|
|---|---|---|
|$x1|int \| string| начальная x координата в пикселях или в процентах|
|$y1|int \| string| начальная y координата в пикселях или в процентах|
|$x2|int \| string| конечная x координата в пикселях или в процентах|
|$y2|int \| string| конечная y координата в пикселях или в процентах|
|$param|array| ```width``` => fat of line 1,2, ... <br> ```color``` - color ex:'#00ff00'|

---
Выводит в отчете готовое изображение
```php
image( $x, $y, $width, $height, $filename, array $param=[])
```

 
|name|type|notes|
|---|---|---|
|$x|int \| string| верхняя левая координата в пикс или процентах|
|$y|int \| string| верхняя левая координата в пикс или процентах|
|$width|int \| string| ширина в пикс или процентах|
|$height|int \| string| высота в пикс или процентах|
|$filename|string| путь к графическому файлу|
|$param|array| ```scale``` - "h" or "w" or "inscribe" - методы масштабирования <br> ```border``` - цвет рамки (по умолчанию без рамки) ex:"#00ff00"|





