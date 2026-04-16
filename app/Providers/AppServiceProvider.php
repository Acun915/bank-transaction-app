<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Contracts\ImportLogRepositoryInterface;
use App\Repositories\Contracts\ImportRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Repositories\ImportLogRepository;
use App\Repositories\ImportRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
        $this->app->bind(ImportRepositoryInterface::class, ImportRepository::class);
        $this->app->bind(ImportLogRepositoryInterface::class, ImportLogRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
