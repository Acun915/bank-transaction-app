<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\UnsupportedFileTypeException;
use App\Http\Requests\StoreImportRequest;
use App\Http\Resources\ImportDetailResource;
use App\Http\Resources\ImportResource;
use App\Services\ImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ImportController extends Controller
{
    public function __construct(
        private ImportService $importService,
    ) {}

    public function store(StoreImportRequest $request): JsonResponse
    {
        try {
            $import = $this->importService->import($request->file('file'));
        } catch (UnsupportedFileTypeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new ImportResource($import))
            ->response()
            ->setStatusCode(201);
    }

    public function index(): AnonymousResourceCollection
    {
        return ImportResource::collection($this->importService->getAll());
    }

    public function show(int $id): JsonResponse
    {
        $import = $this->importService->getById($id);

        if ($import === null) {
            return response()->json(['message' => 'Import not found.'], 404);
        }

        return (new ImportDetailResource($import))->response();
    }
}
