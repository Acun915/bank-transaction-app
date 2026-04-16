<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'max:10240',
                'mimetypes:text/csv,application/json,text/xml,application/xml',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'A file is required.',
            'file.file' => 'The uploaded value must be a file.',
            'file.max' => 'The file must not exceed 10MB.',
            'file.mimetypes' => 'The file must be a CSV, JSON or XML file.',
        ];
    }
}
