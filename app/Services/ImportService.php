<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Import;
use App\Repositories\Contracts\ImportLogRepositoryInterface;
use App\Repositories\Contracts\ImportRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Services\Parsers\TransactionParserFactory;
use App\Services\Validation\TransactionValidator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ImportService
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepository,
        private ImportRepositoryInterface $importRepository,
        private ImportLogRepositoryInterface $importLogRepository,
        private TransactionValidator $validator,
    ) {}

    public function import(UploadedFile $file): Import
    {
        $parser = TransactionParserFactory::make($file->getMimeType());
        $records = $parser->parse($file->getRealPath());

        $totalRecords = count($records);
        $successfulRecords = 0;
        $failedRecords = 0;

        return DB::transaction(function () use ($file, $records, $totalRecords, &$successfulRecords, &$failedRecords) {
            $import = $this->importRepository->create([
                'file_name' => $file->getClientOriginalName(),
                'total_records' => $totalRecords,
                'successful_records' => 0,
                'failed_records' => 0,
                'status' => 'partial',
            ]);

            foreach ($records as $record) {
                $errors = $this->validator->validate($record);

                if (empty($errors) && $this->transactionRepository->existsByTransactionId((string) $record['transaction_id'])) {
                    $errors[] = 'transaction_id already exists.';
                }

                if (! empty($errors)) {
                    $this->importLogRepository->create([
                        'import_id' => $import->id,
                        'transaction_id' => $record['transaction_id'] ?? null,
                        'error_message' => implode(' ', $errors),
                    ]);
                    $failedRecords++;

                    continue;
                }

                $this->transactionRepository->create([
                    'transaction_id' => $record['transaction_id'],
                    'account_number' => $record['account_number'],
                    'transaction_date' => $record['transaction_date'],
                    'amount' => $record['amount'],
                    'currency' => $record['currency'],
                ]);
                $successfulRecords++;
            }

            $import->successful_records = $successfulRecords;
            $import->failed_records = $failedRecords;
            $import->status = $this->resolveStatus($successfulRecords, $failedRecords, $totalRecords);
            $import->save();

            return $import;
        });
    }

    public function getAll(): Collection
    {
        return $this->importRepository->findAll();
    }

    public function getById(int $id): ?Import
    {
        return $this->importRepository->findById($id);
    }

    private function resolveStatus(int $successful, int $failed, int $total): string
    {
        if ($total === 0 || $failed === $total) {
            return 'failed';
        }

        if ($successful === $total) {
            return 'success';
        }

        return 'partial';
    }
}
