<?php

namespace Personal\CsvHandler\Model;

use Ds\Vector;
use Carbon\Carbon;

class Row
{
    private Vector $cells;

    public function __construct()
    {
        $this->cells = new Vector();
    }

    public function __toString()
    {
        return implode(',', $this->cells->toArray());
    }

    public function clone(): Row
    {
        $newRow = clone $this;
        return $newRow;
    }

    public function getCells(): Vector
    {
        return $this->cells;
    }

    public function appendWithout(Row $row, int $skipIndex): void
    {
        foreach ($row->cells as $index => $cell) {
            if ($index === $skipIndex) {
                continue; // Skip the cell at the specified index
            }
            $this->addCell($cell);
        }
    }

    public function verifyCompatibility(Row $other): bool
    {
        if ($this->count() !== $other->count()) {
            throw new \InvalidArgumentException("Rows are not compatible: different number of cells.");
        }
        return true; // Rows are compatible
    }

    public function equals(Row $other): bool
    {
        return $this->cells->toArray() === $other->cells->toArray();
    }

    public function addCell(string | int $cell): void
    {
        $this->cells->push($cell);
    }

    public function headerToIndex(string $header): int
    {
        return $this->cells->find($header) === false ? -1 : $this->cells->find($header);
    }

    public function getOrderFrom(Row $headers): Vector
    {
        $newOrder = new Vector();
        foreach ($this->cells as $cell) {
            $index = $headers->headerToIndex(trim($cell));
            if ($index === -1) {
                throw new \InvalidArgumentException("Header '$cell' not found in headers row.");
            }
            $newOrder->push($index);
        }
        return $newOrder;
    }

    public function removeCell(int $index): void
    {
        $this->cells->remove($index);
    }

    public function addIndex(int | string $index): void
    {
        $this->cells->unshift($index);
    }

    public function reorderCells(Vector $newOrder): void
    {
        $this->cells = $newOrder->map(fn($i) => $this->cells->get($i));
    }

    public function count(): int
    {
        return $this->cells->count();
    }

    public function truncateCell(int $index, int $length): void
    {
        if (!is_string($this->cells->get($index))) {
            throw new \InvalidArgumentException("Cell at index $index is not a string.");
        }

        $this->cells->set($index, substr($this->cells->get($index), 0, $length));
    }

    public function reformatDate(int $index, string $format): void
    {
        if (!is_string($this->cells->get($index))) {
            throw new \InvalidArgumentException("Cell at index $index is not a string.");
        }

        $date = Carbon::createFromFormat($format, $this->cells->get($index));

        if ($date === false) {
            throw new \InvalidArgumentException("Invalid date format for cell at index $index.");
        }

        $this->cells->set($index, $date->format($format));
    }

    public function encryptCell(int $index, string $key): void
    {
        if (!is_string($this->cells->get($index))) {
            throw new \InvalidArgumentException("Cell at index $index is not a string.");
        }

        if (! openssl_public_encrypt($this->cells->get($index), $encrypted, $key)) {
            throw new \Exception("Encryption failed for value: " . openssl_error_string());
        }

        $this->cells->set($index, base64_encode($encrypted));
    }

    public function decryptCell(int $index, string $key): void
    {
        if (!is_string($this->cells->get($index))) {
            throw new \InvalidArgumentException("Cell at index $index is not a string.");
        }

        $data = base64_decode($this->cells->get($index));
        if (! openssl_private_decrypt($data, $decrypted, $key)) {
            throw new \Exception("Decryption failed for value: " . openssl_error_string());
        }

        $this->cells->set($index, $decrypted);
    }

    public function signCell(int $index, string $privateKey): void
    {
        if (!is_string($this->cells->get($index))) {
            throw new \InvalidArgumentException("Cell at index $index is not a string.");
        }

        if (!openssl_sign($this->cells->get($index), $signature, $privateKey)) {
            throw new \Exception("Signing failed for value: " . openssl_error_string());
        }

        $this->cells->push(base64_encode($signature));
    }

    public function verifyCell(int $index, string $publicKey): bool
    {
        if (!is_string($this->cells->get($index))) {
            throw new \InvalidArgumentException("Cell at index $index is not a string.");
        }

        $data = $this->cells->get($index);
        $signature = base64_decode($this->cells->last());

        return openssl_verify($data, $signature, $publicKey) === 1;
    }

    /**
     * Joins the current row with another row if their specified columns match.
     *
     * @return Row if the rows are compatible and the join was successful
     * @return null if the rows are not compatible
     */
    public function joinIfCompatible(Row $other, int $myIndex, int $otherIndex): ?Row
    {
        $myValue = $this->cells->get($myIndex);
        $otherValue = $other->cells->get($otherIndex);

        if ($myValue === $otherValue) {
            $this->appendWithout($other, $otherIndex);
            return $this;
        }
        return null;
    }
}
