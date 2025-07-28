<?php

require_once __DIR__ . '/vendor/autoload.php';

use Ds\Vector;
use Personal\CsvHandler\Model\Tabel;
use Personal\CsvHandler\Convertors\FileToTableImpl\CsvToTabel;
use Personal\CsvHandler\Convertors\TableToFileImpl\TableToCsv;
use Personal\CsvHandler\Functionalities\AddHeaderFunctionality;
use Personal\CsvHandler\Functionalities\ReorderFileFunctionality;
use Personal\CsvHandler\Functionalities\EncryptFunctionality;
use Personal\CsvHandler\Functionalities\DecryptFunctionality;
use Personal\CsvHandler\Functionalities\SignFunctionality;
use Personal\CsvHandler\Functionalities\VerifySignatureFunctionality;



$options = [];
$filePaths = new Vector();

try {

    if ($argc < 3) {
        throw new Exception("Insufficient arguments provided.");
    }

    foreach ($argv as $index => $arg) {
        if ($index === 0) {
            continue; // Skip the script name
        }
        if (strpos($arg, '--') === 0) {

            [$option, $value] = explode('=', substr($arg, 2), 2) + [null, null];
            $options[$option] = $value ?? "";
        } else {
            $filePaths->push($arg);
        }
    }

    $streams = new Vector();

    foreach ($filePaths as $filePath) {
        if (!file_exists($filePath)) {
            throw new Exception("File not found: $filePath");
        } else {
            $streams->push(new SplFileObject($filePath, 'r+'));
        }
    }

    $tabel1 = new Tabel();
    $tabel1 = CsvToTabel::convert($streams->get(0));

    if ($streams->count() < 2) {
        $tabel2 = null;
    } else {
        $tabel2 = new Tabel();
        $tabel2 = CsvToTabel::convert($streams->get(1));
    }


    $stream = null; // Close the file stream after reading

    $availableOptions = [
        'add-header',
        'add-indexing',
        'reorder-file',
        'remove-column',
        'truncate-column',
        'reformat-date-time',
        'encrypt-column',
        'decrypt-column',
        'sign-column',
        'verify-column',
        'merge-tables'
    ];

    $selectedOption = null;

    foreach ($availableOptions as $option) {
        if (isset($options[$option])) {
            $selectedOption = $option;
            break;
        }
    }

    switch ($selectedOption) {
        case 'add-header':
            $func = new AddHeaderFunctionality();
            $func->modify($options['add-header'], $tabel1);
            break;

        case 'add-indexing':
            $tabel1->addIndexedRow();
            break;

        case 'reorder-file':
            $func = new ReorderFileFunctionality();
            $func->modify($options['reorder-file'], $tabel1);
            break;

        case 'remove-column':
            $tabel1->removeColumn($options['remove-column']);
            break;

        case 'truncate-column':
            $tabel1->truncateColumn($options['truncate-column'], (int)$options['length']);
            break;

        case 'reformat-date-time':
            $tabel1->reformatDate($options['reformat-date-time'], $options['format']);
            break;

        case 'encrypt-column':
            $func = new EncryptFunctionality();
            $func->modify($options['encrypt-column'], $tabel1);
            break;

        case 'decrypt-column':
            $func = new DecryptFunctionality();
            $func->modify($options['decrypt-column'], $tabel1);
            break;

        case 'sign-column':
            $func = new SignFunctionality();
            $func->modify($options['sign-column'], $tabel1);
            break;

        case 'verify-column':
            $func = new VerifySignatureFunctionality();
            $func->modify($options['verify-column'], $tabel1);
            break;

        case 'merge-tables':
            if ($tabel2 === null) {
                throw new Exception("Second table is required for merging.");
            }
            $tabel1->appendFromTable($tabel2);
            break;

        default:
            throw new Exception("No valid functionality option provided.");
    }

    TableToCsv::convert($tabel1);
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
} finally {
    echo "Operation completed.\n";
}
