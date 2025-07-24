<?php

namespace Personal\CsvHandler\Functionalities;
use SplFileObject;
use Carbon\Carbon;

class ReformatDateTimeFunctionality implements Functionality
{
    private function formatDateTime(string $dateTime, string $format): string
    {
        try {
            return Carbon::parse($dateTime)->format($format);
        } catch (\Exception $e) {
            throw new \Exception("Invalid date time format: " . $e->getMessage());
        }
    }

    public function modify(string $input, SplFileObject $stream): void
    {
       try{
            
        if ($stream->getSize() === 0) {
            throw new \Exception("Stream is empty, cannot reformat date time.");
        }

        [$dateTimeColumn, $format] = explode(",", $input);
        $header = explode(",", rtrim($stream->fgets()));

        $tempStream = new SplFileObject('php://temp', 'r+');

        while (!$stream->eof()) {
            $line = explode(",", rtrim($stream->fgets()));

            if (count($line) === count($header)) {
                $assocoateLineKey = array_combine($header, $line);
                
                if (!is_numeric($dateTimeColumn)) {
                    if (array_key_exists($dateTimeColumn, $assocoateLineKey)) {

                         foreach ($assocoateLineKey as $key => $value) {
                            if ($key !== $dateTimeColumn) {
                                $tempStream->fwrite($value . ",");
                            } else {
                                if (is_string($value)) {
                                    try {
                                        $formattedDate = $this->formatDateTime($value, $format);
                                        $tempStream->fwrite($formattedDate . ",");
                                    } catch (\Exception $e) {
                                        throw new \Exception("Failed to format date in column '$dateTimeColumn': " . $e->getMessage());
                                    }

                                } else {
                                    throw new \Exception("Value for column '" . $dateTimeColumn . "' is not a string.");
                                }
                            }
                        }

                        $tempStream->fseek($tempStream->ftell() - 1); // Remove the last comma
                        $tempStream->fwrite("\n");
                        
                    } else {
                        throw new \Exception("Column '$dateTimeColumn' does not exist in the header.");
                    }
                    
                } else {

                     if ((int)$dateTimeColumn < count($header)) {

                        foreach ($assocoateLineKey as $key => $value) {
                            if (array_search($key, $header) != $dateTimeColumn) {
                                $tempStream->fwrite($value . ",");
                            } else {
                                if (is_string($value)) {
                                    try {
                                        $formattedDate = $this->formatDateTime($value, $format);
                                        $tempStream->fwrite($formattedDate . ",");
                                    } catch (\Exception $e) {
                                        throw new \Exception("Failed to format date in column '$dateTimeColumn': " . $e->getMessage());
                                    }
                                } else {
                                    throw new \Exception("Value for column '" . $dateTimeColumn . "' is not a string.");
                                }
                            }
                        }

                        $tempStream->fseek($tempStream->ftell() - 1); // Remove the last comma
                        $tempStream->fwrite("\n");

                    }else{
                        throw new \Exception("Column index '$dateTimeColumn' is out of bounds.");
                    }
                    
                }
                    
            }
        }

        $tempStream->rewind();
            $stream->rewind(); // Reset the original stream position
            $stream->ftruncate(0); // Clear the original stream

            $stream->fwrite(implode(",", $header) . "\n"); // Write the new header
            while (!$tempStream->eof()) {
                $stream->fwrite($tempStream->fgets());
            }

            $tempStream = null; // Close the temporary stream


       }catch(\Exception $e){
           error_log("Failed to format date: " . $e->getMessage());
       }
    }
}