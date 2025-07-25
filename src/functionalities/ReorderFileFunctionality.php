<?php

namespace Personal\CsvHandler\Functionalities;

use Personal\CsvHandler\Model\Tabel;
use Personal\CsvHandler\Model\Row;

class ReorderFileFunctionality implements OneFileFunctionality
{
    public function modify(string $options, Tabel $tabel): void
    {
        try {
            // Split the options string into an array of column names
            $columns = explode(',', rtrim($options));

            $row = new Row();
            foreach ($columns as $column) {
                $row->addCell(trim($column));
            }

            // Reorder the table based on the specified columns
            $tabel->reorderByHeader($row);
        } catch (\Exception $e) {
            // Handle exceptions
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
}
