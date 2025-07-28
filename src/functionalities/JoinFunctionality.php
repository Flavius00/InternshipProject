<?php

namespace Personal\CsvHandler\Functionalities;

use Personal\CsvHandler\Model\Tabel;

class JoinFunctionality implements TwoTablesFunctionality
{
    public function modify(string $options, Tabel $firstTable, Tabel $secondTable): Tabel
    {
        // Implement the join logic here
        [$myColumn, $otherColumn] = explode('-', $options);
        $myIndex = is_numeric($myColumn) ? (int)$myColumn : $firstTable->getRows()->first()->headerToIndex($myColumn);
        $otherIndex = is_numeric($otherColumn) ? (int)$otherColumn : $secondTable->getRows()->first()->headerToIndex($otherColumn);

        if ($myIndex === -1 || $otherIndex === -1) {
            throw new \InvalidArgumentException("Column not found in one of the tables.");
        }

        $firstTable->getRows()->first()->appendWithout($secondTable->getRows()->first(), $otherIndex);

        $newTable = new Tabel();
        $newTable->addRow($firstTable->getRows()->first()->clone()); // Add header row

        foreach ($firstTable->getRows() as $myRow) {
            if ($myRow->equals($firstTable->getRows()->first())) {
                continue; // Skip the header row
            }
            foreach ($secondTable->getRows() as $otherRow) {
                if ($otherRow->equals($secondTable->getRows()->first())) {
                    continue; // Skip the header row
                }
                $newRow = $myRow->joinIfCompatible($otherRow, $myIndex, $otherIndex);
                if ($newRow !== null) {
                    $newTable->addRow($newRow);
                }
            }
        }
        return $newTable;
    }
}
