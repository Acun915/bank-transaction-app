<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Import;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImportFactory extends Factory
{
    protected $model = Import::class;

    public function definition(): array
    {
        return [
            'file_name'          => $this->faker->word() . '.csv',
            'total_records'      => 10,
            'successful_records' => 10,
            'failed_records'     => 0,
            'status'             => 'success',
        ];
    }
}
