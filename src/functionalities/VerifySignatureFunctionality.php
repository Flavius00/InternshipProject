<?php

namespace Personal\CsvHandler\Functionalities;

use Personal\CsvHandler\Model\Tabel;
use Personal\CsvHandler\Model\Row;

class VerifySignatureFunctionality implements OneFileFunctionality
{
    public function modify(string $column, Tabel $tabel): void
    {
        $publicKey = file_get_contents(__DIR__ . '/../../keys/public.pem');

        if ($publicKey === false) {
            throw new \RuntimeException("Could not read public key from path: /../../keys/public.pem");
        }

        // Verify the signature for the specified column
        if (!$tabel->verifyColumn($column, $publicKey)) {
            throw new \RuntimeException("Signature verification failed for column: $column");
        }

        echo "============================================================\n";
        echo "Signature verification successful for column: $column\n";
        echo "============================================================\n";

        $publicKey = null; // Clear the variable to free memory
    }
}
