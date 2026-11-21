<?php // app/Http/Requests/ScanRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'qr_token' => ['required_without:code', 'nullable', 'string', 'max:100'],
            'code' => ['required_without:qr_token', 'nullable', 'string', 'max:30'],
        ];
    }
}