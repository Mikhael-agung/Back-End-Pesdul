<?php

use App\Http\Controllers\Api\V1\Consumer\AuthController;
use App\Http\Controllers\Api\V1\Consumer\CartController;
use App\Http\Controllers\Api\V1\Consumer\MenuController;
use App\Http\Controllers\Api\V1\Internal\CategoryAdminController;
use App\Http\Controllers\Api\V1\Internal\MenuAdminController;
use App\Http\Controllers\Api\V1\Internal\PinAuthController;
use App\Http\Controllers\Api\V1\Internal\SettingController;
use App\Http\Controllers\Api\V1\Internal\StockController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ---------- Konsumen ----------
    Route::post('auth/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    Route::middleware(['auth:sanctum', 'role:consumer'])->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);

        Route::get('categories', [MenuController::class, 'categories']);
        Route::get('menus', [MenuController::class, 'index']);
        Route::get('settings/public', [MenuController::class, 'publicSettings']);
        Route::post('cart/validate', [CartController::class, 'check']);
        // Part 4: orders, payment
    });

    // ---------- Internal ----------
    Route::prefix('internal')->group(function () {
        Route::post('auth/pin', [PinAuthController::class, 'login']);

        Route::middleware(['auth:sanctum', 'role:kasir,dapur,admin'])->group(function () {
            Route::post('auth/logout', [PinAuthController::class, 'logout']);
            // Part 5: kasir, dapur
        });

        Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
            Route::get('categories', [CategoryAdminController::class, 'index']);
            Route::post('categories', [CategoryAdminController::class, 'store']);
            Route::patch('categories/{category}', [CategoryAdminController::class, 'update']);
            Route::delete('categories/{category}', [CategoryAdminController::class, 'destroy']);

            Route::get('menus', [MenuAdminController::class, 'index']);
            Route::post('menus', [MenuAdminController::class, 'store']);
            // Multipart: kirim POST + field _method=PATCH (Laravel menganggapnya PATCH)
            Route::patch('menus/{menu}', [MenuAdminController::class, 'update']);

            Route::get('stocks', [StockController::class, 'index']);
            Route::put('stocks', [StockController::class, 'update']);

            Route::get('settings/dine-in-full', [SettingController::class, 'showDineInFull']);
            Route::put('settings/dine-in-full', [SettingController::class, 'updateDineInFull']);
            // Part 6: reports, activity-logs
        });
    });
});