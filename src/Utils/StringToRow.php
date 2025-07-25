<?php

namespace Personal\CsvHandler\Utils;

use Personal\CsvHandler\Model\Row;

class StringToRow
{
    public static function convert(string $input): Row
    {
        $row = new Row();
        $cells = explode(',', rtrim($input));

        foreach ($cells as $cell) {
            $row->addCell(trim($cell));
        }

        return $row;
    }
}
