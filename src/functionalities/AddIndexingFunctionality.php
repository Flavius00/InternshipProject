<?php

namespace Personal\CsvHandler\Functionalities;

use SplFileObject;

class AddIndexingFunctionality implements Functionality
{
    public function modify(string $input, SplFileObject $stream): void
    {
        // Implementation for adding indexing functionality
        try{

            $tempStream = new SplFileObject('php://temp', 'r+');

            if($stream->getSize() === 0) {
                throw new \Exception("Stream is empty, cannot add indexing.");
            }

            $i = 0;

            while(!$stream->eof()) {
                
                $line = $stream->fgets();

                if ($i === 0){
                    $tempStream->fwrite("Indexing," . $line);
                }else{
                    $tempStream->fwrite($i . "," . $line);
                }

                $i++;
            }

            $tempStream->rewind();
            $stream->rewind(); // Reset the original stream position
            $stream->ftruncate(0); // Clear the original stream

            while (!$tempStream->eof()) {
                $stream->fwrite($tempStream->fgets());
            }

            $tempStream = null; // Close the temporary stream

            $stream->fflush();
        }catch (\Exception $e) {

            error_log("Error modifying stream: " . $e->getMessage());

        }
    }
}
