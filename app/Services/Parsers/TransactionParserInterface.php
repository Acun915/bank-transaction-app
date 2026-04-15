<?php

declare(strict_types=1);

namespace App\Services\Parsers;

interface TransactionParserInterface
{
    public function parse(string $filePath): array;
}
