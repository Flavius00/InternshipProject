<?php

namespace Personal\CsvHandler\Convertors\FileToTableImpl;

use Personal\CsvHandler\Model\Tabel;
use Personal\CsvHandler\Model\Row;
use Personal\CsvHandler\Convertors\ConvertorFromFileToTable;
use SplFileObject;

class CsvToTabel implements ConvertorFromFileToTable
{
    public static function convert(SplFileObject $stream): Tabel
    {
        $tabel = new Tabel();

        // Read the rest of the file
        while (!$stream->eof()) {
            $line = $stream->fgets();
            if (trim($line) === '') {
                continue; // Skip empty lines
            }
            $row = new Row();
            $cells = explode(',', rtrim($line));
            foreach ($cells as $cell) {
                $row->addCell(trim($cell));
            }
            $tabel->addRow($row);
        }

        return $tabel;
    }
}
