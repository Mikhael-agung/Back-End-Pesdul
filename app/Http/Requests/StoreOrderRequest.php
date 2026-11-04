<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\OrderType;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['order_type' => ['required', Rule::enum(OrderType::class)], 'items' => ['required','array','min:1'], 'items.*.menu_id' => ['required','exists:menus,id'], 'items.*.quantity' => ['required','integer','min:1']]; }
}
