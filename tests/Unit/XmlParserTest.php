<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\Parsers\XmlTransactionParser;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class XmlParserTest extends TestCase
{
    private XmlTransactionParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new XmlTransactionParser;
    }

    public function test_parses_valid_xml(): void
    {
        $xml = <<<'XML'
        <transactions>
            <transaction>
                <transaction_id>550e8400-e29b-41d4-a716-446655440001</transaction_id>
                <account_number>PL61109010140000071219812874</account_number>
                <transaction_date>2025-10-14</transaction_date>
                <amount>150000</amount>
                <currency>PLN</currency>
            </transaction>
        </transactions>
        XML;

        $file = $this->createTempFile($xml);
        $records = $this->parser->parse($file);
        unlink($file);

        $this->assertCount(1, $records);
        $this->assertSame('550e8400-e29b-41d4-a716-446655440001', $records[0]['transaction_id']);
        $this->assertSame('PL61109010140000071219812874', $records[0]['account_number']);
        $this->assertSame('2025-10-14', $records[0]['transaction_date']);
        $this->assertSame('150000', $records[0]['amount']);
        $this->assertSame('PLN', $records[0]['currency']);
    }

    public function test_returns_empty_array_for_xml_with_no_transactions(): void
    {
        $xml = '<transactions></transactions>';

        $file = $this->createTempFile($xml);
        $records = $this->parser->parse($file);
        unlink($file);

        $this->assertCount(0, $records);
    }

    public function test_throws_on_invalid_xml(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/Invalid XML/');

        $file = $this->createTempFile('<transactions><unclosed>');
        try {
            $this->parser->parse($file);
        } finally {
            unlink($file);
        }
    }

    public function test_parses_multiple_transactions(): void
    {
        $xml = <<<'XML'
        <transactions>
            <transaction>
                <transaction_id>aaa</transaction_id>
                <account_number>PL61109010140000071219812874</account_number>
                <transaction_date>2025-10-14</transaction_date>
                <amount>100</amount>
                <currency>PLN</currency>
            </transaction>
            <transaction>
                <transaction_id>bbb</transaction_id>
                <account_number>GB29NWBK60161331926819</account_number>
                <transaction_date>2025-10-15</transaction_date>
                <amount>200</amount>
                <currency>USD</currency>
            </transaction>
        </transactions>
        XML;

        $file = $this->createTempFile($xml);
        $records = $this->parser->parse($file);
        unlink($file);

        $this->assertCount(2, $records);
        $this->assertSame('aaa', $records[0]['transaction_id']);
        $this->assertSame('bbb', $records[1]['transaction_id']);
    }

    private function createTempFile(string $content): string
    {
        $path = tempnam(sys_get_temp_dir(), 'xml_test_');
        file_put_contents($path, $content);

        return $path;
    }
}
