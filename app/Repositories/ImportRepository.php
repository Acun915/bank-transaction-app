<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Import;
use App\Repositories\Contracts\ImportRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ImportRepository implements ImportRepositoryInterface
{
    public function create(array $data): Import
    {
        return Import::create($data);
    }

    public function findAll(): Collection
    {
        return Import::latest()->get();
    }

    public function findById(int $id): ?Import
    {
        return Import::with('logs')->find($id);
    }
}
