<?php

namespace App\Http\Controllers\Api\V1\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\PinLoginRequest;
use App\Models\StaffAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class PinAuthController extends Controller
{
    public function login(PinLoginRequest $request): JsonResponse
    {
        $access = StaffAccess::whereHas('user', fn ($q) => $q->where('phone', $request->string('phone')))->where('is_active', true)->first();
        abort_unless($access && Hash::check($request->string('pin'), $access->pin_hash), 422, 'PIN tidak valid.');
        return response()->json(['data' => ['user' => $access->user, 'role' => $access->role->value]]);
    }
}
