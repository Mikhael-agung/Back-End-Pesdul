<?php

namespace App\Http\Controllers\Api\V1\Consumer;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create($request->safe()->only(['name', 'email', 'phone']) + ['password' => $request->string('password')->toString()]);
        return response()->json(['data' => $user], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->string('email'))->first();
        abort_unless($user && Hash::check($request->string('password'), $user->password), 422, 'Email atau password salah.');
        return response()->json(['data' => $user]);
    }

    public function me(): JsonResponse { return response()->json(['data' => request()->user()]); }
}
