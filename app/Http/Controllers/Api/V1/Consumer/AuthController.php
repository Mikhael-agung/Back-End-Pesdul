<?php

namespace App\Http\Controllers\Api\V1\Consumer;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        return $this->tokenResponse($user, 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('identifier', $request->validated('identifier'))->first();

        if (! $user || ! Hash::check($request->validated('password'), $user->password)) {
            throw new BusinessException('Email/nomor HP atau kata sandi salah.', 'UNAUTHENTICATED', 401);
        }

        return $this->tokenResponse($user);
    }

    public function logout(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['data' => $request->user()->only(['id', 'name', 'identifier'])]);
    }

    private function tokenResponse(User $user, int $status = 200): JsonResponse
    {
        return response()->json([
            'data' => [
                'user' => $user->only(['id', 'name', 'identifier']),
                'token' => $user->createToken('consumer', ['*'], now()->addDays(30))->plainTextToken,
            ],
        ], $status);
    }
}