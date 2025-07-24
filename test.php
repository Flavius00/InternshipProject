<?php

require_once __DIR__ . '/vendor/autoload.php';
use Personal\CsvHandler\Functionalities\AddHeaderFunctionality;
use Personal\CsvHandler\Functionalities\AddIndexingFunctionality;
use Personal\CsvHandler\Functionalities\ReorderFileFunctionality;
use Personal\CsvHandler\Functionalities\RemoveColumnFunctionality;
use Personal\CsvHandler\Functionalities\TruncateColumnFunctionality;
use Personal\CsvHandler\Functionalities\ReformatDateTimeFunctionality;

$options = [];
$filePath = "";

try{

    if ($argc < 3) {
        throw new Exception("Insufficient arguments provided.");
    }

    foreach ($argv as $index => $arg) {
        if( $index === 0) {
            continue; // Skip the script name
        }
        if($index === $argc-1){
            $filePath = $arg;
            continue; // Last argument is the file path
        }
        if (strpos($arg, '--') === 0) {
            
            [$option, $value] = explode('=', substr($arg, 2), 2) + [null, null];
            $options[$option] = $value ?? "";

        }
    }

    if (!file_exists($filePath)) {
        throw new Exception("File does not exist: " . $filePath);
    }

    $stream = new SplFileObject($filePath, 'r+');

    if (isset($options['add-header'])) {

        $functionality = new AddHeaderFunctionality();
        $functionality->modify($options['add-header'], $stream);

    }elseif (isset($options['add-indexing'])) {

        $functionality = new AddIndexingFunctionality();
        $functionality->modify($options['add-indexing'], $stream);

    }elseif (isset($options['reorder-file'])) {

        $functionality = new ReorderFileFunctionality();
        $functionality->modify($options['reorder-file'], $stream);

    } elseif (isset($options['remove-column'])) {

        $functionality = new RemoveColumnFunctionality();
        $functionality->modify($options['remove-column'], $stream);

    } elseif (isset($options['truncate-column'])) {

        $functionality = new TruncateColumnFunctionality();
        $functionality->modify($options['truncate-column'] . "," . $options["truncate-length"], $stream);
        
    } elseif(isset($options['reformat-date-time'])) {

        $functionality = new ReformatDateTimeFunctionality();
        $functionality->modify($options['reformat-date-time'] . "," . $options["reformat-date-time-format"], $stream);

    } else {

        throw new Exception("No valid functionality option provided.");
        
    }
}catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}finally{
    $stream = null; // Close the file stream
    echo "Operation completed.\n";
}
