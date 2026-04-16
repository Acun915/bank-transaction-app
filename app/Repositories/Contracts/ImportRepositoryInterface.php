<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Import;
use Illuminate\Database\Eloquent\Collection;

interface ImportRepositoryInterface
{
    public function create(array $data): Import;

    public function findAll(): Collection;

    public function findById(int $id): ?Import;
}
