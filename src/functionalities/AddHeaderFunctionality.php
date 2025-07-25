<?php

namespace Personal\CsvHandler\Functionalities;

use Personal\CsvHandler\Model\Tabel;
use Personal\CsvHandler\Model\Row;

class AddHeaderFunctionality implements OneFileFunctionality
{
    public function modify(string $header, Tabel $tabel): void
    {
        // Split the header string into an array of column names
        $columns = explode(',', rtrim($header));

        // Add the header row to the table
        $headerRow = new Row();
        foreach ($columns as $column) {
            $headerRow->addCell(trim($column));
        }

        $tabel->addHeader($headerRow);
    }
}
