<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ImportLog;
use App\Repositories\Contracts\ImportLogRepositoryInterface;

class ImportLogRepository implements ImportLogRepositoryInterface
{
    public function create(array $data): ImportLog
    {
        return ImportLog::create($data);
    }
}
