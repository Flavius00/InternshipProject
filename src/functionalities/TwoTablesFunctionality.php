<?php

namespace Personal\CsvHandler\Functionalities;

use Personal\CsvHandler\Model\Tabel;

interface TwoTablesFunctionality
{
    /**
     * Merges two tables based on the specified options.
     *
     * @param string $options Options for merging the tables.
     * @param Tabel $firstTable The first table to merge.
     * @param Tabel $secondTable The second table to merge.
     * @return Tabel The merged table.
     */
    public function modify(string $options, Tabel $firstTable, Tabel $secondTable): Tabel;
}
