<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Str;

class MidtransService
{
    public function createPayment(Order $order): array
    {
        $transactionId = 'PESDULS-'.$order->order_number;
        $order->payment()->updateOrCreate([], ['provider' => 'midtrans', 'transaction_id' => $transactionId, 'status' => 'pending', 'gross_amount' => $order->total]);
        return ['transaction_id' => $transactionId, 'token' => Str::random(32), 'gross_amount' => $order->total];
    }

    public function applyNotification(array $payload): void
    {
        $payment = \App\Models\Payment::where('transaction_id', $payload['order_id'] ?? null)->first();
        if (! $payment) return;
        $payment->update(['status' => $payload['transaction_status'] ?? 'pending', 'paid_at' => ($payload['transaction_status'] ?? '') === 'settlement' ? now() : null]);
        if (($payload['transaction_status'] ?? '') === 'settlement') $payment->order()->update(['status' => 'paid']);
    }
}
