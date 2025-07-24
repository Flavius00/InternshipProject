<?php

namespace Personal\CsvHandler\Functionalities;
use SplFileObject;

class RemoveColumnFunctionality implements Functionality
{
    public function modify(string $input, SplFileObject $stream): void
    {
        // Implementation for removing a column from the CSV file based on the name/index provided in $input

        try{
            if($stream->getSize() === 0) {
                throw new \Exception("Stream is empty, cannot remove column.");
            }

            $input = trim($input);

            $header = explode(",", rtrim($stream->fgets()));
            $tempStream = new SplFileObject('php://temp', 'r+');

            while(!$stream->eof()) {

                $line = explode(",", rtrim($stream->fgets()));

                if (count($line) === count($header)) {
                
                    $assocoateLineKey = array_combine($header, $line);

                    if (!is_numeric($input)){
                        if (array_key_exists($input, $assocoateLineKey)) {

                            unset($assocoateLineKey[$input]);
                            
                            // echo "============================================\n";
                            // echo "Removing column: $input\n";
                            // echo "Remaining columns: " . implode(", ", array_keys($assocoateLineKey)) . "\n";
                            // echo "============================================\n";

                            foreach ($assocoateLineKey as $value) {
                                $tempStream->fwrite($value . ",");
                            }
                            $tempStream->fseek($tempStream->ftell() - 1); // Remove the last comma
                            $tempStream->fwrite("\n");

                        } else{
                            throw new \Exception("Column name '$input' does not exist in the line.");
                        }
                    }else{
                        if ((int)$input <= count($header)){

                            // echo "============================================\n";
                            // foreach ($header as $key => $value) {
                            //     echo "Key: $key, Value: $value\n";
                            // }
                            // echo "============================================\n";
                            // echo "\n" . $assocoateLineKey[$header[(int)$input]] . "\n";


                            unset($assocoateLineKey[$header[(int)$input]]);


                            foreach ($assocoateLineKey as $value) {
                                $tempStream->fwrite($value . ",");
                            }
                            
                            $tempStream->fseek($tempStream->ftell() - 1); // Remove the last comma
                            $tempStream->fwrite("\n");

                        }else{
                            throw new \Exception("Column index '$input' does not exist in the header.");
                        }
                    }
                }
            }

            $tempStream->rewind();
            $stream->rewind(); // Reset the original stream position
            $stream->ftruncate(0); // Clear the original stream
            
            if (is_numeric($input)) {
                unset($header[(int)$input]); // Remove the column from the header
            } else {
                unset($header[array_search($input, $header)]); // Remove the column from the header
            }

            $stream->fwrite(implode(",", $header) . "\n"); // Write the new header
            while (!$tempStream->eof()) {
                $stream->fwrite($tempStream->fgets());
            }

            $tempStream = null; // Close the temporary stream

        }catch (\Exception $e) {

            error_log("Error modifying stream: " . $e->getMessage());
        }
    }
}