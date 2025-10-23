<?php
namespace fmihel\report;

use fmihel\report\_utils\hexToRgb;
use fmihel\report\_utils\hexToRgba;
use fmihel\report\_utils\hexToRgbw;
use fmihel\report\_utils\imgSize;
use fmihel\report\_utils\isHexColor;
use fmihel\report\_utils\randomString;
use fmihel\report\_utils\translate;

class ReportUtils
{
    use hexToRgb;
    use hexToRgba;
    use hexToRgbw;
    use imgSize;
    use isHexColor;
    use randomString;
    use translate;

}
