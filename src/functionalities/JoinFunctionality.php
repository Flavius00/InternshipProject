<?php

namespace Personal\CsvHandler\Functionalities;

use Personal\CsvHandler\Model\Tabel;

class JoinFunctionality implements TwoTablesFunctionality
{
    public function modify(string $options, Tabel $firstTable, Tabel $secondTable): Tabel
    {
        // Implement the join logic here
        [$myColumn, $otherColumn] = explode('-', $options);
        return $firstTable->joinWithTable($secondTable, $myColumn, $otherColumn);
    }
}
