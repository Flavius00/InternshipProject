<?php

namespace Personal\CsvHandler\Functionalities;

use Personal\CsvHandler\Model\Tabel;
use Personal\CsvHandler\Model\Row;

class EncryptFunctionality implements OneFileFunctionality
{
    public function modify(string $column, Tabel $tabel): void
    {
        // Split the options string into an array of column names
        $publicKey = file_get_contents(__DIR__ . '/../../keys/public.pem');

        if ($publicKey === false) {
            throw new \RuntimeException("Could not read public key from path: /../../keys/public.pem");
        }

        $tabel->encryptColumn($column, $publicKey);
        $publicKey = null; // Clear the variable to free memory
    }
}
