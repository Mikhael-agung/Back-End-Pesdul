<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenuFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** FormData mengirim boolean sebagai "true"/"false", jadi dinormalisasi dulu. */
    protected function prepareForValidation(): void
    {
        foreach (['is_active', 'has_spice_level', 'remove_photo'] as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->input($field), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                ]);
            }
        }
    }

    public function rules(): array
    {
        $req = $this->isMethod('POST') ? 'required' : 'sometimes';

        return [
            'name' => [$req, 'string', 'max:100'],
            'price' => [$req, 'integer', 'min:0', 'max:10000000'],
            'category_id' => [$req, 'integer', 'exists:menu_categories,id'],
            'is_active' => ['sometimes', 'boolean'],
            'has_spice_level' => ['sometimes', 'boolean'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_photo' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'category_id.exists' => 'Kategori tidak ditemukan.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.mimes' => 'Foto harus jpg, png, atau webp.',
            'photo.max' => 'Foto maksimal 2 MB.',
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'Nama', 'price' => 'Harga', 'category_id' => 'Kategori'];
    }
}