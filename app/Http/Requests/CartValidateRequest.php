<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartValidateRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['items' => ['required','array','min:1'], 'items.*.menu_id' => ['required','integer','exists:menus,id'], 'items.*.quantity' => ['required','integer','min:1']]; }
}
