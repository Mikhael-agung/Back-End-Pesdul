<?php

namespace App\Http\Controllers\Api\V1\Consumer;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\PaymentResource;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /** Tombol Cek status dan polling 5 dtk. Memeriksa ulang ke Midtrans. */
    public function status(Request $request, string $code, OrderService $orders): JsonResponse
    {
        $order = $request->user()->orders()->where('code', $code)->firstOrFail();
        $order = $orders->syncPayment($order)->load('payment');

        return response()->json([
            'data' => [
                'order_status' => $order->status->value,
                'payment' => $order->payment ? new PaymentResource($order->payment) : null,
            ],
        ]);
    }

    public function retry(Request $request, string $code, OrderService $orders): OrderResource
    {
        $order = $request->user()->orders()->where('code', $code)->firstOrFail();
        $order = $orders->retryPayment($order);

        return new OrderResource($order->load(['items.menu', 'typeLogs', 'payment']));
    }
}