<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('identifier'))) {
            $this->merge(['identifier' => User::normalizeIdentifier($this->input('identifier'))]);
        }
    }

    public function rules(): array
    {
        return [
            'identifier' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string'],
        ];
    }

    public function attributes(): array
    {
        return ['identifier' => 'Email/nomor HP', 'password' => 'Kata sandi'];
    }

    public function messages(): array
    {
        return ['required' => ':attribute wajib diisi.'];
    }
}