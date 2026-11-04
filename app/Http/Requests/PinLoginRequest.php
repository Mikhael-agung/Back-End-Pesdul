<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PinLoginRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['phone' => ['required','string'], 'pin' => ['required','digits:6']]; }
}
