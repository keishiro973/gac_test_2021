<?php

namespace App\Service;

use League\Csv\Reader;

class ImportCsv
{
    public function import(string $csvPath)
    {
        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(0);
        $csv->setDelimiter(";");

        $csvRows = $csv->getRecords();
        foreach ($csvRows as $offset => $row) {
            dump($row);
        }
    }
}