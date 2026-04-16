<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\Parsers\CsvTransactionParser;
use PHPUnit\Framework\TestCase;

class CsvParserTest extends TestCase
{
    private CsvTransactionParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new CsvTransactionParser;
    }

    public function test_parses_valid_csv(): void
    {
        $csv = implode("\n", [
            'transaction_id,account_number,transaction_date,amount,currency',
            '550e8400-e29b-41d4-a716-446655440001,PL61109010140000071219812874,2025-10-14,150000,PLN',
            '550e8400-e29b-41d4-a716-446655440002,GB29NWBK60161331926819,2025-10-13,20050,USD',
        ]);

        $file = $this->createTempFile($csv);
        $records = $this->parser->parse($file);
        unlink($file);

        $this->assertCount(2, $records);
        $this->assertSame('550e8400-e29b-41d4-a716-446655440001', $records[0]['transaction_id']);
        $this->assertSame('PL61109010140000071219812874', $records[0]['account_number']);
        $this->assertSame('2025-10-14', $records[0]['transaction_date']);
        $this->assertSame('150000', $records[0]['amount']);
        $this->assertSame('PLN', $records[0]['currency']);
    }

    public function test_returns_empty_array_for_header_only_csv(): void
    {
        $csv = 'transaction_id,account_number,transaction_date,amount,currency';

        $file = $this->createTempFile($csv);
        $records = $this->parser->parse($file);
        unlink($file);

        $this->assertCount(0, $records);
    }

    public function test_skips_rows_with_mismatched_column_count(): void
    {
        $csv = implode("\n", [
            'transaction_id,account_number,transaction_date,amount,currency',
            '550e8400-e29b-41d4-a716-446655440001,PL61109010140000071219812874,2025-10-14,150000',
            '550e8400-e29b-41d4-a716-446655440002,GB29NWBK60161331926819,2025-10-13,20050,USD',
        ]);

        $file = $this->createTempFile($csv);
        $records = $this->parser->parse($file);
        unlink($file);

        $this->assertCount(1, $records);
        $this->assertSame('550e8400-e29b-41d4-a716-446655440002', $records[0]['transaction_id']);
    }

    private function createTempFile(string $content): string
    {
        $path = tempnam(sys_get_temp_dir(), 'csv_test_');
        file_put_contents($path, $content);

        return $path;
    }
}
