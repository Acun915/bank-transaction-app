<?php

declare(strict_types=1);

namespace App\Services\Parsers;

use App\Exceptions\UnsupportedFileTypeException;

class TransactionParserFactory
{
    public static function make(string $mimeType): TransactionParserInterface
    {
        return match ($mimeType) {
            'text/csv'                      => new CsvTransactionParser(),
            'application/json'              => new JsonTransactionParser(),
            'text/xml', 'application/xml'   => new XmlTransactionParser(),
            default                         => throw new UnsupportedFileTypeException(
                "Unsupported file type: {$mimeType}"
            ),
        };
    }
}
