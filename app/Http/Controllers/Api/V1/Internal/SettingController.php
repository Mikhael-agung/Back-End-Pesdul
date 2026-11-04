<?php

namespace App\Http\Controllers\Api\V1\Internal;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index() { return Setting::all()->pluck('value', 'key'); }
    public function update(Request $request, string $key) { $value = $request->validate(['value' => ['nullable','string']])['value']; return Setting::updateOrCreate(['key' => $key], ['value' => $value]); }
}
