<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'file_name'           => $this->file_name,
            'total_records'       => $this->total_records,
            'successful_records'  => $this->successful_records,
            'failed_records'      => $this->failed_records,
            'status'              => $this->status,
            'created_at'          => $this->created_at->toIso8601String(),
        ];
    }
}
