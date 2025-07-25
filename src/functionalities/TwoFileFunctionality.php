<?php

namespace Personal\CsvHandler\Functionalities;

use Personal\CsvHandler\Model\Tabel;

interface TwoFileFunctionality
{
    public function modify(Tabel $tabel1, Tabel $tabel2): void;
}
