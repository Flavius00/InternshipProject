<?php

namespace Personal\CsvHandler\Model;

use Ds\Vector;

class Tabel
{

    /**
     * @var Vector<Row>
     */
    private Vector $rows;

    public function __construct()
    {
        $this->rows = new Vector();
    }

    public function addRow(Row $row): void
    {
        $this->rows->push($row);
    }

    public function getRows(): Vector
    {
        return $this->rows;
    }

    public function addHeader(Row $row): void
    {
        $this->rows->first()->verifyCompatibility($row);
        $this->rows->unshift($row);
    }

    public function addIndexedRow(): void
    {
        $startIndex = 0;
        foreach ($this->rows as $row) {
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
        $tableHeaders = $this->rows->first();

        $newIndexOrder = $row->getOrderFrom($tableHeaders);

        if ($newIndexOrder->count() !== $tableHeaders->count()) {
            throw new \InvalidArgumentException("The number of headers does not match the number of cells in the row.");
        }

        foreach ($this->rows as $row) {
            $row->reorderCells($newIndexOrder);
        }
    }

    public function removeColumn(int | string $header): void
    {
        $index = is_numeric($header) ? (int)$header : $this->rows->first()->headerToIndex($header);
        foreach ($this->rows as $row) {
            $row->removeCell($index);
        }
    }

    public function truncateColumn(int | string $header, int $length): void
    {
        $index = is_numeric($header) ? (int)$header : $this->rows->first()->headerToIndex($header);
        foreach ($this->rows as $row) {
            $row->truncateCell($index, $length);
        }
    }

    public function reformatDate(int | string $header, string $format): void
    {
        $index = is_numeric($header) ? (int)$header : $this->rows->first()->headerToIndex($header);
        foreach ($this->rows as $row) {
            $row->reformatDate($index, $format);
        }
    }

    public function appendFromTable(Tabel $tabel): void
    {
        if (!$this->rows->first()->equals($tabel->rows->first())) {
            throw new \InvalidArgumentException("Headers do not match.");
        }

        foreach ($tabel->rows as $row) {
            if ($this->rows->first()->equals($row)) {
                continue; // Skip the header row
            }
            $this->addRow($row->clone());
        }
    }

    public function encryptColumn(int | string $header, string $publicKey): void
    {
        $index = is_numeric($header) ? (int)$header : $this->rows->first()->headerToIndex($header);
        foreach ($this->rows as $row) {
            if ($this->rows->first() !== $row) {
                $row->encryptCell($index, $publicKey);
            }
        }
    }

    public function decryptColumn(int | string $header, string $privateKey): void
    {
        $index = is_numeric($header) ? (int)$header : $this->rows->first()->headerToIndex($header);
        foreach ($this->rows as $row) {
            if ($this->rows->first() !== $row) {
                $row->decryptCell($index, $privateKey);
            }
        }
    }

    public function signColumn(int | string $header, string $privateKey): void
    {
        $index = is_numeric($header) ? (int)$header : $this->rows->first()->headerToIndex($header);
        foreach ($this->rows as $row) {

            if ($this->rows->first() === $row) {
                $row->addCell("Signature");
                continue;
            }

            $row->signCell($index, $privateKey);
        }
    }

    public function verifyColumn(int | string $header, string $publicKey): bool
    {
        $index = is_numeric($header) ? (int)$header : $this->rows->first()->headerToIndex($header);
        foreach ($this->rows as $row) {
            if ($this->rows->first() !== $row && !$row->verifyCell($index, $publicKey)) {
                return false;
            }
        }
        return true;
    }

    public function print(): void
    {
        foreach ($this->rows as $row) {
            echo $row . PHP_EOL;
        }
    }
}
