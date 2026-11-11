<?php

namespace App\Http\Controllers\Api\V1\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\MidtransService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MidtransNotificationController extends Controller
{
    public function __invoke(Request $request, MidtransService $midtrans, OrderService $orders): JsonResponse
    {
        $payload = $request->all();

        if (! $midtrans->isValidSignature($payload)) {
            return response()->json(['message' => 'Signature tidak valid.'], 403);
        }

        $payment = Payment::where('gateway_order_id', (string) ($payload['order_id'] ?? ''))->first();

        if (! $payment) {
            return response()->json(['message' => 'Pembayaran tidak dikenal.'], 404);
        }

        $orders->applyPaymentResult(
            $payment,
            $midtrans->mapStatus(
                (string) ($payload['transaction_status'] ?? ''),
                isset($payload['fraud_status']) ? (string) $payload['fraud_status'] : null,
            ),
            isset($payload['transaction_id']) ? (string) $payload['transaction_id'] : null,
        );

        return response()->json(['message' => 'OK']);
    }
}