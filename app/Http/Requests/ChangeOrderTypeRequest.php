<?php // app/Http/Requests/ChangeOrderTypeRequest.php

namespace App\Http\Requests;

use App\Enums\OrderType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeOrderTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['order_type' => ['required', Rule::enum(OrderType::class)]];
    }
}