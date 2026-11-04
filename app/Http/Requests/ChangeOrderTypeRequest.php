<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\OrderType;

class ChangeOrderTypeRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['order_type' => ['required', Rule::enum(OrderType::class)]]; }
}
