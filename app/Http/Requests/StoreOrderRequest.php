<?php

namespace App\Http\Requests;

use App\Enums\OrderType;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends CartValidateRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'order_type' => ['required', Rule::enum(OrderType::class)],
            'items.*.menu_id' => ['required', 'integer', 'exists:menus,id'],
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'order_type.required' => 'Tipe pesanan wajib dipilih.',
            'order_type.enum' => 'Tipe pesanan tidak valid.',
            'items.*.menu_id.exists' => 'Menu tidak ditemukan.',
        ]);
    }
}