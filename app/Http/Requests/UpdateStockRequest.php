<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'integer', 'distinct', 'exists:menus,id'],
            'items.*.stock' => ['required', 'integer', 'min:0', 'max:9999'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.*.menu_id.exists' => 'Menu tidak ditemukan.',
            'items.*.menu_id.distinct' => 'Menu tidak boleh dobel.',
            'items.*.stock.min' => 'Stok tidak boleh negatif.',
            'items.*.stock.max' => 'Stok maksimal 9999.',
        ];
    }
}