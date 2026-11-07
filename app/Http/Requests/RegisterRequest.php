<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:100'],
            'identifier' => [
                'required', 'string', 'max:100',
                function ($attribute, $value, $fail) {
                    if (! filter_var($value, FILTER_VALIDATE_EMAIL) && ! preg_match('/^08\d{8,12}$/', $value)) {
                        $fail('Isi dengan email atau nomor HP yang valid.');
                    }
                },
                'unique:users,identifier',
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'Nama', 'identifier' => 'Email/nomor HP', 'password' => 'Kata sandi'];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'identifier.unique' => 'Email/nomor HP sudah terdaftar.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ];
    }
}