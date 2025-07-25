<?php

namespace Personal\CsvHandler\Functionalities;

use Personal\CsvHandler\Model\Tabel;
use Personal\CsvHandler\Model\Row;

class SignFunctionality implements OneFileFunctionality
{
    public function modify(string $signature, Tabel $tabel): void
    {
        $privateKey = file_get_contents(__DIR__ . '/../../keys/private.pem');

        if ($privateKey === false) {
            throw new \RuntimeException("Could not read private key from path: /../../keys/private.pem");
        }

        $tabel->signColumn($signature, $privateKey);
        $privateKey = null; // Clear the variable to free memory
    }
}
