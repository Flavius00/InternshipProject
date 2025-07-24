<?php

namespace Personal\CsvHandler\Functionalities;
use SplFileObject;

class EncryptionFunctionality implements Functionality
{
    public function modify(string $input, SplFileObject $stream): void
    {
        // Implement encryption logic here
        try{

            if ($stream->getSize() === 0) {
                throw new \Exception("Stream is empty, cannot encrypt.");
            }

            $input = trim($input);

            $publicKey = file_get_contents(__DIR__ . '/../../keys/public.pem');

            $header = explode(",", rtrim($stream->fgets()));
            $tempStream = new SplFileObject('php://temp', 'r+');

            while (!$stream->eof()) {
                $line = explode(",", rtrim($stream->fgets()));

                if (count($line) === count($header)) {
                    $assocoateLineKey = array_combine($header, $line);

                    if (!is_numeric($input)) {
                        if (array_key_exists($input, $assocoateLineKey)) {
                            
                            foreach ($assocoateLineKey as $key => $value) {
                                if ($key !== $input) {
                                    $tempStream->fwrite($value . ",");
                                } else {
                                    if (is_string($value)) {
                                        if (!openssl_public_encrypt($value, $encryptedValue, $publicKey)) {
                                            throw new \Exception("Encryption failed for value: " . openssl_error_string());
                                        }
                                        $tempStream->fwrite(base64_encode($encryptedValue) . ",");
                                    } else {
                                        throw new \Exception("Value for column '" . $input . "' is not a string.");
                                    }
                                }
                            }

                            $tempStream->fseek($tempStream->ftell() - 1); // Remove the last comma
                            $tempStream->fwrite("\n");

                        } else {
                            throw new \Exception("Column name '$input' does not exist in the line.");
                        }
                    } else {

                        if ((int)$input <= count($header)) {
                        
                            foreach ($assocoateLineKey as $key => $value) {
                                if (array_search($key, $header) != (int)$input) {
                                    $tempStream->fwrite($value . ",");
                                } else {
                                    if (is_string($value)) {
                                        if (!openssl_public_encrypt($value, $encryptedValue, $publicKey)) {
                                            throw new \Exception("Encryption failed for value: " . openssl_error_string());
                                        }
                                        $tempStream->fwrite(base64_encode($encryptedValue) . ",");
                                    } else {
                                        throw new \Exception("Value for column '" . $header[(int)$input] . "' is not a string.");
                                    }
                                }
                            }

                            $tempStream->fseek($tempStream->ftell() - 1); // Remove the last comma
                            $tempStream->fwrite("\n");

                        } else {
                            throw new \Exception("Column index '$input' is out of bounds.");
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
            $publicKey = null; // Clear the public key from memory
            
        } catch (\Exception $e) {
            throw new \Exception("Encryption failed: " . $e->getMessage());
        }
    }
}