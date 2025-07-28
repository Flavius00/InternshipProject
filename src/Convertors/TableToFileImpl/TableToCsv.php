<?php

namespace Personal\CsvHandler\Convertors\TableToFileImpl;

use Personal\CsvHandler\Model\Tabel;
use Personal\CsvHandler\Convertors\ConvertorFromTableToFile;

class TableToCsv implements ConvertorFromTableToFile
{
    public static function convert(Tabel $input): void
    {
        $output = '';
        foreach ($input->getRows() as $row) {
            $output .= implode(',', $row->getCells()->toArray()) . PHP_EOL;
        }
        file_put_contents('test.csv', $output);
    }
}
