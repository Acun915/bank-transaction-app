<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImportUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_csv_import_with_all_valid_records(): void
    {
        $csv = implode("\n", [
            'transaction_id,account_number,transaction_date,amount,currency',
            '550e8400-e29b-41d4-a716-446655440001,PL61109010140000071219812874,2025-10-14,150000,PLN',
            '550e8400-e29b-41d4-a716-446655440002,GB29NWBK60161331926819,2025-10-13,20050,USD',
        ]);

        $file = UploadedFile::fake()->createWithContent('transactions.csv', $csv);

        $response = $this->postJson('/api/imports', ['file' => $file]);

        $response->assertStatus(201)
            ->assertJsonPath('data.status', 'success')
            ->assertJsonPath('data.total_records', 2)
            ->assertJsonPath('data.successful_records', 2)
            ->assertJsonPath('data.failed_records', 0);

        $this->assertDatabaseCount('transactions', 2);
        $this->assertDatabaseCount('import_logs', 0);
    }

    public function test_json_import_with_all_valid_records(): void
    {
        $json = json_encode([
            [
                'transaction_id' => '660e8400-e29b-41d4-a716-446655440001',
                'account_number' => 'PL61109010140000071219812874',
                'transaction_date' => '2025-10-14',
                'amount' => 150000,
                'currency' => 'PLN',
            ],
            [
                'transaction_id' => '660e8400-e29b-41d4-a716-446655440002',
                'account_number' => 'GB29NWBK60161331926819',
                'transaction_date' => '2025-10-13',
                'amount' => 20050,
                'currency' => 'USD',
            ],
        ]);

        $file = UploadedFile::fake()->createWithContent('transactions.json', $json);

        $response = $this->postJson('/api/imports', ['file' => $file]);

        $response->assertStatus(201)
            ->assertJsonPath('data.status', 'success')
            ->assertJsonPath('data.total_records', 2)
            ->assertJsonPath('data.successful_records', 2)
            ->assertJsonPath('data.failed_records', 0);

        $this->assertDatabaseCount('transactions', 2);
        $this->assertDatabaseCount('import_logs', 0);
    }

    public function test_xml_import_with_all_valid_records(): void
    {
        $xml = <<<'XML'
        <transactions>
            <transaction>
                <transaction_id>770e8400-e29b-41d4-a716-446655440001</transaction_id>
                <account_number>PL61109010140000071219812874</account_number>
                <transaction_date>2025-10-14</transaction_date>
                <amount>150000</amount>
                <currency>PLN</currency>
            </transaction>
            <transaction>
                <transaction_id>770e8400-e29b-41d4-a716-446655440002</transaction_id>
                <account_number>GB29NWBK60161331926819</account_number>
                <transaction_date>2025-10-13</transaction_date>
                <amount>20050</amount>
                <currency>USD</currency>
            </transaction>
        </transactions>
        XML;

        $file = UploadedFile::fake()->createWithContent('transactions.xml', $xml);

        $response = $this->postJson('/api/imports', ['file' => $file]);

        $response->assertStatus(201)
            ->assertJsonPath('data.status', 'success')
            ->assertJsonPath('data.total_records', 2)
            ->assertJsonPath('data.successful_records', 2)
            ->assertJsonPath('data.failed_records', 0);

        $this->assertDatabaseCount('transactions', 2);
        $this->assertDatabaseCount('import_logs', 0);
    }

    public function test_import_with_mixed_valid_and_invalid_records(): void
    {
        $csv = implode("\n", [
            'transaction_id,account_number,transaction_date,amount,currency',
            '880e8400-e29b-41d4-a716-446655440001,PL61109010140000071219812874,2025-10-14,150000,PLN',
            '880e8400-e29b-41d4-a716-446655440002,INVALID-IBAN,2025-10-13,-50,pln',
        ]);

        $file = UploadedFile::fake()->createWithContent('mixed.csv', $csv);

        $response = $this->postJson('/api/imports', ['file' => $file]);

        $response->assertStatus(201)
            ->assertJsonPath('data.status', 'partial')
            ->assertJsonPath('data.total_records', 2)
            ->assertJsonPath('data.successful_records', 1)
            ->assertJsonPath('data.failed_records', 1);

        $this->assertDatabaseCount('transactions', 1);
        $this->assertDatabaseCount('import_logs', 1);
    }

    public function test_import_with_all_invalid_records(): void
    {
        $csv = implode("\n", [
            'transaction_id,account_number,transaction_date,amount,currency',
            ',INVALID-IBAN,not-a-date,-100,xx',
            '',
        ]);

        $file = UploadedFile::fake()->createWithContent('invalid.csv', $csv);

        $response = $this->postJson('/api/imports', ['file' => $file]);

        $response->assertStatus(201)
            ->assertJsonPath('data.status', 'failed');

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_import_logs_are_created_for_invalid_records(): void
    {
        $csv = implode("\n", [
            'transaction_id,account_number,transaction_date,amount,currency',
            '990e8400-e29b-41d4-a716-446655440001,INVALID-IBAN,2025-10-14,100,PLN',
        ]);

        $file = UploadedFile::fake()->createWithContent('errors.csv', $csv);

        $this->postJson('/api/imports', ['file' => $file]);

        $this->assertDatabaseCount('import_logs', 1);
        $this->assertDatabaseHas('import_logs', [
            'transaction_id' => '990e8400-e29b-41d4-a716-446655440001',
        ]);
    }

    public function test_invalid_file_type_returns_422(): void
    {
        $file = UploadedFile::fake()->create('document.pdf', 10, 'application/pdf');

        $response = $this->postJson('/api/imports', ['file' => $file]);

        $response->assertStatus(422);
    }

    public function test_missing_file_returns_422(): void
    {
        $response = $this->postJson('/api/imports', []);

        $response->assertStatus(422);
    }
}
