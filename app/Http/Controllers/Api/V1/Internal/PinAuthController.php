<?php

namespace App\Http\Controllers\Api\V1\Internal;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\PinLoginRequest;
use App\Models\StaffAccess;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class PinAuthController extends Controller
{
    private const MAX_ATTEMPTS = 5;
    private const DECAY_SECONDS = 60;

    public function login(PinLoginRequest $request, ActivityLogger $logger): JsonResponse
    {
        $key = 'pin-login:'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $wait = RateLimiter::availableIn($key);

            throw new BusinessException(
                "Terlalu banyak percobaan. Coba lagi dalam {$wait} detik.",
                'TOO_MANY_ATTEMPTS',
                429,
                retryAfter: $wait,
            );
        }

        $pin = $request->validated('pin');
        $access = StaffAccess::all()->first(fn (StaffAccess $a) => Hash::check($pin, $a->pin_hash));

        if (! $access) {
            RateLimiter::hit($key, self::DECAY_SECONDS);

            throw new BusinessException('PIN salah.', 'PIN_INVALID', 401);
        }

        RateLimiter::clear($key);

        $token = $access->createToken('staff-'.$access->role->value, ['*'], now()->addHours(12))->plainTextToken;

        $logger->log($access->role, 'login_pin');

        return response()->json(['data' => ['role' => $access->role->value, 'token' => $token]]);
    }

    public function logout(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}