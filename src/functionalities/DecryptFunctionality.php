<?php

namespace Personal\CsvHandler\Functionalities;

use Personal\CsvHandler\Model\Tabel;
use Personal\CsvHandler\Model\Row;

class DecryptFunctionality implements OneFileFunctionality
{
    public function modify(string $column, Tabel $tabel): void
    {
        // Split the options string into an array of column names
        $privateKey = file_get_contents(__DIR__ . '/../../keys/private.pem');

        if ($privateKey === false) {
            throw new \RuntimeException("Could not read private key from path: /../../keys/private.pem");
        }

        $tabel->decryptColumn($column, $privateKey);
        $privateKey = null; // Clear the variable to free memory
    }
}
