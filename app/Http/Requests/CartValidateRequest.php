<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartValidateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.menu_id' => ['required', 'integer'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:99'],
            'items.*.spice_level' => ['nullable', 'integer', 'min:1'],
            'items.*.note' => ['nullable', 'string', 'max:200'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Keranjang kosong.',
            'items.min' => 'Keranjang kosong.',
            'items.*.qty.min' => 'Jumlah minimal 1.',
            'items.*.qty.max' => 'Jumlah maksimal 99 per item.',
            'items.*.note.max' => 'Catatan maksimal 200 karakter.',
        ];
    }
}