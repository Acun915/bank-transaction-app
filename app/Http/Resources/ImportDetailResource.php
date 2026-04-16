<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;

class ImportDetailResource extends ImportResource
{
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'logs' => ImportLogResource::collection($this->whenLoaded('logs')),
        ]);
    }
}
