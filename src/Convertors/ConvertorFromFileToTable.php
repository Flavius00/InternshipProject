<?php

namespace Personal\CsvHandler\Convertors;

use Personal\CsvHandler\Model\Tabel;
use Personal\CsvHandler\Model\Row;
use SplFileObject;

interface ConvertorFromFileToTable
{
    public static function convert(SplFileObject $input): Tabel;
}
