<?php

namespace Personal\CsvHandler\Convertors;

use Personal\CsvHandler\Model\Tabel;
use Personal\CsvHandler\Model\Row;

interface ConvertorFromTableToFile
{
    public static function convert(Tabel $input): void;
}
