<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Import;
use App\Models\ImportLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImportLogFactory extends Factory
{
    protected $model = ImportLog::class;

    public function definition(): array
    {
        return [
            'import_id' => Import::factory(),
            'transaction_id' => $this->faker->uuid(),
            'error_message' => 'account_number must be a valid IBAN.',
        ];
    }
}
