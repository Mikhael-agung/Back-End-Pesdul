<?php

use App\Http\Controllers\Api\V1\Consumer\AuthController;
use App\Http\Controllers\Api\V1\Internal\PinAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ---------- Konsumen ----------
    Route::post('auth/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    Route::middleware(['auth:sanctum', 'role:consumer'])->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);
        // Part 3+: menus, cart, orders, payment
    });

    // ---------- Internal ----------
    Route::prefix('internal')->group(function () {
        Route::post('auth/pin', [PinAuthController::class, 'login']);

        Route::middleware(['auth:sanctum', 'role:kasir,dapur,admin'])->group(function () {
            Route::post('auth/logout', [PinAuthController::class, 'logout']);
            // Part 3+: kasir, dapur, admin
        });
    });
});