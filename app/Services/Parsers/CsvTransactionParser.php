<?php

declare(strict_types=1);

namespace App\Services\Parsers;

class CsvTransactionParser implements TransactionParserInterface
{
    public function parse(string $filePath): array
    {
        $records = [];

        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            return [];
        }

        $headers = fgetcsv($handle);

        if ($headers === false || $headers === null) {
            fclose($handle);
            return [];
        }

        $headers = array_map('trim', $headers);

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($headers)) {
                continue;
            }

            $records[] = array_combine($headers, $row);
        }

        fclose($handle);

        return $records;
    }
}
