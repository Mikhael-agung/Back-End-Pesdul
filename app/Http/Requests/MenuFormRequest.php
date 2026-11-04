<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenuFormRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['menu_category_id' => ['required','exists:menu_categories,id'], 'name' => ['required','string','max:150'], 'description' => ['nullable','string'], 'price' => ['required','integer','min:0'], 'image' => ['nullable','image','max:2048'], 'is_active' => ['sometimes','boolean']]; }
}
