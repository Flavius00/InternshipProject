<?php

namespace Personal\CsvHandler\Functionalities;

use Personal\CsvHandler\Model\Tabel;

interface OneFileFunctionality
{
    public function modify(string $options, Tabel $tabel): void;
}
