<?php

namespace Personal\CsvHandler\Functionalities;
use SplFileObject;

class ReorderFileFunctionality implements Functionality
{
    public function modify(string $input, SplFileObject $stream): void
    {
        // Implementation for reordering the CSV file
        try{

            if($stream->getSize() === 0) {
                throw new \Exception("Stream is empty, cannot reorder.");
            }

            $input = explode(",", $input);

            $header = explode(",", rtrim($stream->fgets()));

            // echo "=====================================================================\n";
            // echo "Header: " . implode(", ", $header) . "\n";
            // echo "\n";

            if(var_dump($input == $header) === true) {
                return; // No reordering needed if input matches header
            }

            $tempStream = new SplFileObject('php://temp', 'r+');

            while(!$stream->eof()) {
                
                $line = explode(",", rtrim($stream->fgets()));
                // echo "Line: " . implode(", ", $line) . "\n";
                // echo "\n";
                // echo "=====================================================================\n";
                if (count($line) === count($header)) {
                    
                    $assocoateLineKey = array_combine($header, $line);

                    foreach ($input as $key){
                        if(array_key_exists($key, $assocoateLineKey)) {
                            $tempStream->fwrite($assocoateLineKey[$key] . ",");
                        } else {
                            throw new \Exception("Key '$key' does not exist in the header.");
                        }
                    }
                    
                    $tempStream->fseek($tempStream->ftell() - 1); // Remove the last comma
                    $tempStream->fwrite("\n");
                }
                
            }

            $tempStream->rewind();
            $stream->rewind(); // Reset the original stream position
            $stream->ftruncate(0); // Clear the original stream

            $stream->fwrite(implode(",", $input) . "\n"); // Write the new header
            while (!$tempStream->eof()) {
                $stream->fwrite($tempStream->fgets());
            }

            $tempStream = null; // Close the temporary stream
           
        }catch (\Exception $e) {

            error_log("Error modifying stream: " . $e->getMessage());

        }
    }
}
