<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\Parsers\JsonTransactionParser;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class JsonParserTest extends TestCase
{
    private JsonTransactionParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new JsonTransactionParser();
    }

    public function test_parses_valid_json(): void
    {
        $json = json_encode([
            [
                'transaction_id'   => '550e8400-e29b-41d4-a716-446655440001',
                'account_number'   => 'PL61109010140000071219812874',
                'transaction_date' => '2025-10-14',
                'amount'           => 150000,
                'currency'         => 'PLN',
            ],
        ]);

        $file = $this->createTempFile($json);
        $records = $this->parser->parse($file);
        unlink($file);

        $this->assertCount(1, $records);
        $this->assertSame('550e8400-e29b-41d4-a716-446655440001', $records[0]['transaction_id']);
        $this->assertSame('PL61109010140000071219812874', $records[0]['account_number']);
        $this->assertSame(150000, $records[0]['amount']);
    }

    public function test_returns_empty_array_for_empty_json_array(): void
    {
        $file = $this->createTempFile('[]');
        $records = $this->parser->parse($file);
        unlink($file);

        $this->assertCount(0, $records);
    }

    public function test_throws_on_invalid_json(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/Invalid JSON/');

        $file = $this->createTempFile('not valid json {{');
        try {
            $this->parser->parse($file);
        } finally {
            unlink($file);
        }
    }

    public function test_throws_when_json_is_not_an_array(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/must contain an array/');

        $file = $this->createTempFile(json_encode('just a string'));
        try {
            $this->parser->parse($file);
        } finally {
            unlink($file);
        }
    }

    private function createTempFile(string $content): string
    {
        $path = tempnam(sys_get_temp_dir(), 'json_test_');
        file_put_contents($path, $content);
        return $path;
    }
}
