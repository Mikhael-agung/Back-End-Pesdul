<?php

use App\Http\Controllers\Api\V1\Consumer\AuthController;
use App\Http\Controllers\Api\V1\Consumer\CartController;
use App\Http\Controllers\Api\V1\Consumer\MenuController;
use App\Http\Controllers\Api\V1\Consumer\OrderController;
use App\Http\Controllers\Api\V1\Consumer\PaymentController;
use App\Http\Controllers\Api\V1\Internal\CashierController;
use App\Http\Controllers\Api\V1\Internal\KitchenController;
use App\Http\Controllers\Api\V1\Internal\PinAuthController;
use App\Http\Controllers\Api\V1\Internal\MenuAdminController;
use App\Http\Controllers\Api\V1\Internal\CategoryAdminController;
use App\Http\Controllers\Api\V1\Internal\StockController;
use App\Http\Controllers\Api\V1\Internal\SettingController;
use App\Http\Controllers\Api\V1\Internal\ReportController;
use App\Http\Controllers\Api\V1\Internal\ActivityLogController;
use App\Http\Controllers\Api\V1\Internal\ScanController;
use App\Http\Controllers\Api\V1\Webhook\MidtransNotificationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::get('menus', [MenuController::class, 'index']);
    Route::get('menus/{menu}', [MenuController::class, 'show']);
    Route::post('cart/validate', [CartController::class, 'validateCart']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{order}', [OrderController::class, 'show']);
    Route::patch('orders/{order}/type', [OrderController::class, 'changeType']);
    Route::post('orders/{order}/payment', [PaymentController::class, 'create']);
    Route::post('webhooks/midtrans', MidtransNotificationController::class);
    Route::post('internal/pin/login', [PinAuthController::class, 'login']);
    Route::get('internal/orders', [CashierController::class, 'index']);
    Route::patch('internal/orders/{order}/complete', [CashierController::class, 'complete']);
    Route::get('internal/kitchen', [KitchenController::class, 'index']);
    Route::patch('internal/kitchen/{order}/ready', [KitchenController::class, 'ready']);
    Route::post('internal/scan', ScanController::class);
    Route::apiResource('internal/menus', MenuAdminController::class)->except(['show']);
    Route::apiResource('internal/categories', CategoryAdminController::class)->only(['index', 'store']);
    Route::get('internal/stocks', [StockController::class, 'index']);
    Route::put('internal/stocks', [StockController::class, 'update']);
    Route::get('internal/settings', [SettingController::class, 'index']);
    Route::put('internal/settings/{key}', [SettingController::class, 'update']);
    Route::get('internal/reports/sales', [ReportController::class, 'sales']);
    Route::get('internal/activity-logs', [ActivityLogController::class, 'index']);
});
