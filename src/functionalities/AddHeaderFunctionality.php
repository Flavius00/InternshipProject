<?php

namespace Personal\CsvHandler\Functionalities;
use SplFileObject;

class AddHeaderFunctionality implements Functionality
{
    public function modify(string $input, SplFileObject $stream): void
    {
        // Implementation for adding a header
        try{

            if($stream->getSize() === 0) {
                throw new \Exception("Stream is empty, cannot add header.");
            }
            
            if(strlen($input) === 0) {
                throw new \Exception("Input string cannot be empty.");
            }

            $originalContent = $stream->fread($stream->getSize());
            $stream->fseek(0);
            $stream->fwrite($input. "\n". $originalContent);
            $stream->fflush();

        }catch (\Exception $e) {

            error_log("Error modifying stream: " . $e->getMessage());
        }
        
    }
}
