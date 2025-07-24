<?php

namespace Personal\CsvHandler\Functionalities;

use SplFileObject;

interface Functionality
{
    public function modify(string $input, SplFileObject $stream): void;
}
