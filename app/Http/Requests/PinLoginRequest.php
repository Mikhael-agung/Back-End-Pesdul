<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PinLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pin' => ['required', 'string', 'regex:/^\d{4,8}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'pin.required' => 'PIN wajib diisi.',
            'pin.regex' => 'PIN harus 4 sampai 8 digit angka.',
        ];
    }
}