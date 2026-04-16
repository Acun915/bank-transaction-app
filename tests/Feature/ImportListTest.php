<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Import;
use App\Models\ImportLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportListTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_returns_all_imports(): void
    {
        Import::factory()->count(3)->create();

        $response = $this->getJson('/api/imports');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_list_returns_empty_array_when_no_imports(): void
    {
        $response = $this->getJson('/api/imports');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    public function test_list_returns_correct_fields(): void
    {
        Import::factory()->create([
            'file_name'          => 'test.csv',
            'total_records'      => 5,
            'successful_records' => 4,
            'failed_records'     => 1,
            'status'             => 'partial',
        ]);

        $response = $this->getJson('/api/imports');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.file_name', 'test.csv')
            ->assertJsonPath('data.0.total_records', 5)
            ->assertJsonPath('data.0.successful_records', 4)
            ->assertJsonPath('data.0.failed_records', 1)
            ->assertJsonPath('data.0.status', 'partial');
    }

    public function test_show_returns_import_with_logs(): void
    {
        $import = Import::factory()->create([
            'status' => 'partial',
        ]);

        ImportLog::factory()->create([
            'import_id'      => $import->id,
            'transaction_id' => 'abc-123',
            'error_message'  => 'account_number must be a valid IBAN.',
        ]);

        $response = $this->getJson("/api/imports/{$import->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $import->id)
            ->assertJsonPath('data.status', 'partial')
            ->assertJsonCount(1, 'data.logs')
            ->assertJsonPath('data.logs.0.transaction_id', 'abc-123')
            ->assertJsonPath('data.logs.0.error_message', 'account_number must be a valid IBAN.');
    }

    public function test_show_returns_404_for_nonexistent_import(): void
    {
        $response = $this->getJson('/api/imports/9999');

        $response->assertStatus(404);
    }

    public function test_show_returns_empty_logs_array_when_no_errors(): void
    {
        $import = Import::factory()->create(['status' => 'success']);

        $response = $this->getJson("/api/imports/{$import->id}");

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data.logs');
    }
}
