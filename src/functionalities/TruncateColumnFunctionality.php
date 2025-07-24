<?php

namespace Personal\CsvHandler\Functionalities;
use SplFileObject;

class TruncateColumnFunctionality implements Functionality
{

    public function modify(string $input, SplFileObject $stream): void
	{
		// TODO: Implement the modify logic here.

        try{

            if ($stream->getSize() === 0) {
                throw new \Exception("Stream is empty, cannot truncate column.");
            }

            [$truncateColumn, $truncateLength] = explode(",", $input);

            // echo "=====================================================================\n";
            // echo "Truncate Column: $truncateColumn\n";
            // echo "Truncate Length: $truncateLength\n";
            // echo "=====================================================================\n";

            $header = explode(",", rtrim($stream->fgets()));
            $tempStream = new SplFileObject('php://temp', 'r+');

            while (!$stream->eof()) {

                $line = explode(",", rtrim($stream->fgets()));

                // echo "=====================================================================\n";
                // echo "Header: " . implode(", ", $header) . "\n";
                // echo "Line: " . implode(", ", $line) . "\n";
                // echo "=====================================================================\n";

                if (count($line) === count($header)) {

                    $assocoateLineKey = array_combine($header, $line);

                    if (!is_numeric($truncateColumn)) {
                        if (array_key_exists($truncateColumn, $assocoateLineKey)) {

                            foreach ($assocoateLineKey as $key => $value) {
                                if ($key !== $truncateColumn) {
                                    $tempStream->fwrite($value . ",");
                                }else{
                                    if(is_string($value)) {
                                        $tempStream->fwrite(mb_substr($value, 0, $truncateLength) . ",");
                                    }else{
                                        throw new \Exception("Value for column '" . $truncateColumn . "' is not a string.");
                                    }
                                }
                            }

                            $tempStream->fseek($tempStream->ftell() - 1); // Remove the last comma
                            $tempStream->fwrite("\n");

                        } else {
                            throw new \Exception("Column name '$input' does not exist in the line.");
                        }
                    } else {
                        if ((int)$truncateColumn < count($header)) {

                            foreach ($assocoateLineKey as $key => $value) {

                                // echo "======================================================\n";
                                // echo "Key: $key, Value: $value\n";
                                // echo "Header Key: " . array_search($key, $header) . "\n";
                                // echo "======================================================\n";

                                if (array_search($key, $header) != $truncateColumn) {
                                    $tempStream->fwrite($value . ",");
                                }else{
                                    if(is_string($value)) {
                                        $tempStream->fwrite(mb_substr($value, 0, $truncateLength) . ",");
                                    }else{
                                        throw new \Exception("Value for column '" . $truncateColumn . "' is not a string.");
                                    }
                                }
                            }
                            
                            $tempStream->fseek($tempStream->ftell() - 1); // Remove the last comma
                            $tempStream->fwrite("\n");

                        } else {
                            throw new \Exception("Column index '" . $truncateColumn . "' is out of bounds.");
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

        }catch (\Exception $e) {

            error_log("Error modifying stream: " . $e->getMessage());

        }
	}

}