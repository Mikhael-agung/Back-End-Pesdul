<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStockRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['menu_id' => ['required','exists:menus,id'], 'stock_date' => ['required','date'], 'quantity' => ['required','integer','min:0']]; }
}
