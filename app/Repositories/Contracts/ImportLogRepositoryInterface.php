<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\ImportLog;

interface ImportLogRepositoryInterface
{
    public function create(array $data): ImportLog;
}
