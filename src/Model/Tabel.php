<?php

namespace Personal\CsvHandler\Model;

use Ds\Vector;

class Tabel
{
    private Vector $vector;

    public function __construct()
    {
        $this->vector = new Vector();
    }

    public function addRow(Row $row): void
    {
        $this->vector->push($row);
    }

    public function addHeader(Row $row): void
    {
        $this->vector->unshift($row);
    }

    public function addIndexedRow(): void
    {
        $startIndex = 0;
        foreach ($this->vector as $row) {
            if ($startIndex == 0) {
                $row->addIndex("Index");
                $startIndex++;
            } else {
                $row->addIndex($startIndex);
                $startIndex++;
            }
        }
    }

    public function reorderByHeader(Row $row): void
    {
        $newIndexOrder = new Vector([]);
        $tableHeaders = $this->vector->first();

        $newIndexOrder = $row->getOrderFrom($tableHeaders);

        if ($newIndexOrder->count() !== $tableHeaders->count()) {
            throw new \InvalidArgumentException("The number of headers does not match the number of cells in the row.");
        }

        foreach ($this->vector as $row) {
            $row->reorderCells($newIndexOrder);
        }
    }

    public function removeColumn(int | string $header): void
    {
        $index = is_int($header) ? $header : $this->vector->first()->headerToIndex($header);
        foreach ($this->vector as $row) {
            $row->removeCell($index);
        }
    }

    public function truncateColumn(int | string $header, int $length): void
    {
        $index = is_int($header) ? $header : $this->vector->first()->headerToIndex($header);
        foreach ($this->vector as $row) {
            $row->truncateCell($index, $length);
        }
    }

    public function reformatDate(int | string $header, string $format): void
    {
        $index = is_int($header) ? $header : $this->vector->first()->headerToIndex($header);
        foreach ($this->vector as $row) {
            $row->reformatDate($index, $format);
        }
    }

    public function mergeTwoTabels(Tabel $tabel): void
    {
        if (!$this->vector->first()->equals($tabel->vector->first())) {
            throw new \InvalidArgumentException("Headers do not match.");
        }

        foreach ($tabel->vector as $row) {
            if ($this->vector->first()->equals($row)) {
                continue; // Skip the header row
            }
            $this->addRow($row);
        }
    }

    public function encryptColumn(int | string $header, string $publicKey): void
    {
        $index = is_int($header) ? $header : $this->vector->first()->headerToIndex($header);
        foreach ($this->vector as $row) {
            if ($this->vector->first() !== $row) {
                $row->encryptCell($index, $publicKey);
            }
        }
    }

    public function decryptColumn(int | string $header, string $privateKey): void
    {
        $index = is_int($header) ? $header : $this->vector->first()->headerToIndex($header);
        foreach ($this->vector as $row) {
            if ($this->vector->first() !== $row) {
                $row->decryptCell($index, $privateKey);
            }
        }
    }

    public function signColumn(int | string $header, string $privateKey): void
    {
        $index = is_int($header) ? $header : $this->vector->first()->headerToIndex($header);
        foreach ($this->vector as $row) {

            if ($this->vector->first() === $row) {
                $row->addCell("Signature");
                continue;
            }

            $row->signCell($index, $privateKey);
        }
    }

    public function verifyColumn(int | string $header, string $publicKey): bool
    {
        $index = is_int($header) ? $header : $this->vector->first()->headerToIndex($header);
        foreach ($this->vector as $row) {
            if ($this->vector->first() !== $row && !$row->verifyCell($index, $publicKey)) {
                return false;
            }
        }
        return true;
    }

    public function print(): void
    {
        foreach ($this->vector as $row) {
            echo $row . PHP_EOL;
        }
    }
}
