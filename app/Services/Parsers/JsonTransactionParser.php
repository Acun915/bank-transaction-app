<?php

declare(strict_types=1);

namespace App\Services\Parsers;

use RuntimeException;

class JsonTransactionParser implements TransactionParserInterface
{
    public function parse(string $filePath): array
    {
        $content = file_get_contents($filePath);

        if ($content === false) {
            return [];
        }

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Invalid JSON file: ' . json_last_error_msg());
        }

        if (!is_array($data)) {
            throw new RuntimeException('JSON file must contain an array of transactions.');
        }

        return $data;
    }
}
