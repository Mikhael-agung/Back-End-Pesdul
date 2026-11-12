<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Exceptions\BusinessException;
use App\Models\DailyStock;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderService
{
    private const PAYMENT_WINDOW_MINUTES = 15;

    public function __construct(
        private readonly CartService $cart,
        private readonly MidtransService $midtrans,
    ) {}

    /** Buat pesanan pending_payment lalu tagihan QRIS. Midtrans dipanggil SETELAH commit supaya lock stok tidak tertahan. */
    public function create(User $user, OrderType $type, array $items): Order
    {
        if ($type === OrderType::DineIn && Setting::dineInFull()) {
            throw new BusinessException('Restoran sedang penuh, makan di tempat tidak tersedia.', 'DINE_IN_FULL');
        }

        $order = DB::transaction(function () use ($user, $type, $items) {
            $cart = $this->cart->evaluate($items, lock: true);

            if (! $cart['valid']) {
                throw new BusinessException(
                    'Stok tidak cukup atau menu tidak tersedia.',
                    'STOCK_INSUFFICIENT',
                    409,
                    $this->stockErrors($cart['lines']),
                );
            }

            $order = Order::create([
                'user_id' => $user->id,
                'code' => $this->nextCode(),
                'order_type' => $type,
                'status' => OrderStatus::PendingPayment,
                'total' => $cart['total'],
                'expires_at' => now()->addMinutes(self::PAYMENT_WINDOW_MINUTES),
            ]);

            $order->items()->createMany(array_map(fn (array $line) => [
                'menu_id' => $line['menu_id'],
                'qty' => $line['qty'],
                'unit_price' => $line['unit_price'],
                'spice_level' => $line['spice_level'],
                'note' => $line['note'],
            ], $cart['lines']));

            return $order;
        }, 3);

        try {
            $this->createPayment($order, 1);
        } catch (BusinessException $e) {
            $order->update(['status' => OrderStatus::Expired]);

            throw $e;
        }

        return $order;
    }

    /**
     * Satu-satunya jalan menuju status paid. Dipakai webhook dan Cek status, idempoten:
     * notifikasi dobel tidak menggandakan stok atau QR.
     *
     * @param  string  $status  hasil MidtransService::mapStatus()
     */
    public function applyPaymentResult(Payment $payment, string $status, ?string $transactionId = null): Order
    {
        return DB::transaction(function () use ($payment, $status, $transactionId) {
            $order = Order::whereKey($payment->order_id)->lockForUpdate()->firstOrFail();
            $payment = Payment::whereKey($payment->id)->firstOrFail();

            if ($status === 'ignored') {
                return $order;
            }

            if ($transactionId !== null && $payment->transaction_id === null) {
                $payment->transaction_id = $transactionId;
            }

            if ($status === 'paid') {
                if ($payment->status === 'paid') {
                    return $order;
                }

                $payment->fill(['status' => 'paid', 'paid_at' => now()])->save();

                if ($order->status === OrderStatus::PendingPayment) {
                    $this->markPaid($order);
                } else {
                    Log::warning('Pembayaran masuk untuk pesanan yang tidak lagi menunggu pembayaran', [
                        'order' => $order->code,
                        'payment' => $payment->gateway_order_id,
                        'order_status' => $order->status->value,
                    ]);
                }

                return $order->refresh();
            }

            if ($payment->status === 'pending' && in_array($status, ['failed', 'expired'], true)) {
                $payment->status = $status;
            }

            $payment->save();

            return $order;
        });
    }

    /** Cek tagihan terakhir ke Midtrans. Pesanan yang lewat batas bayar dan belum dibayar ikut dikedaluwarsakan. */
    public function syncPayment(Order $order): Order
    {
        if ($order->status !== OrderStatus::PendingPayment) {
            return $order;
        }

        $payment = $order->payment;

        if ($payment?->status === 'pending') {
            $result = $this->midtrans->status($payment->gateway_order_id);
            $order = $this->applyPaymentResult($payment, $result['status'], $result['transaction_id']);
        }

        if ($order->status === OrderStatus::PendingPayment && $order->expires_at->isPast()) {
            $this->expireOne($order->id);
            $order->refresh();
        }

        return $order;
    }

    /** Tombol coba lagi: cek tagihan lama dulu, jangan sampai konsumen yang sudah bayar disuruh bayar lagi. */
    public function retryPayment(Order $order): Order
    {
        $wasPending = $order->status === OrderStatus::PendingPayment;
        $order = $this->syncPayment($order);

        if ($order->status === OrderStatus::Expired) {
            throw new BusinessException('Pesanan sudah kedaluwarsa. Buat pesanan baru.', 'ORDER_EXPIRED');
        }

        if ($order->status !== OrderStatus::PendingPayment) {
            if ($wasPending && $order->status === OrderStatus::Paid) {
                return $order;
            }

            throw new BusinessException('Pesanan ini tidak bisa dibayar lagi.', 'ORDER_NOT_PAYABLE');
        }

        if ($order->payment?->status === 'pending') {
            // Batalkan tagihan lama dulu supaya tidak ada dua QRIS aktif untuk satu pesanan.
            $this->midtrans->cancel($order->payment->gateway_order_id);
            $order = $this->syncPayment($order);

            if ($order->status === OrderStatus::Paid) {
                return $order;
            }

            if ($order->status !== OrderStatus::PendingPayment) {
                throw new BusinessException('Pesanan sudah kedaluwarsa. Buat pesanan baru.', 'ORDER_EXPIRED');
            }

            if ($order->payment?->status === 'pending') {
                throw new BusinessException('Tagihan sebelumnya belum bisa dibatalkan, coba lagi sebentar.', 'PAYMENT_GATEWAY_ERROR', 502);
            }
        }

        $this->createPayment($order, $order->payments()->count() + 1);

        return $order;
    }

    /** Dipanggil scheduler tiap menit. Return: jumlah pesanan yang dikedaluwarsakan. */
    public function expireOverdue(): int
    {
        $expired = 0;

        $overdue = Order::query()
            ->where('status', OrderStatus::PendingPayment->value)
            ->where('expires_at', '<', now())
            ->orderBy('id')
            ->limit(200)
            ->get();

        foreach ($overdue as $order) {
            try {
                $order = $this->syncPayment($order);
            } catch (BusinessException) {
                // Gateway belum terjangkau: coba lagi menit depan, jangan kedaluwarsakan pesanan yang mungkin sudah dibayar.
                continue;
            }

            if ($order->status === OrderStatus::Expired) {
                $expired++;
            }
        }

        return $expired;
    }

    private function createPayment(Order $order, int $attempt): Payment
    {
        $gatewayOrderId = $order->code.'-'.$attempt;
        $charge = $this->midtrans->chargeQris($gatewayOrderId, $order->total, $order->expires_at);

        return $order->payments()->create([
            'gateway_order_id' => $gatewayOrderId,
            'transaction_id' => $charge['transaction_id'],
            'status' => 'pending',
            'gross_amount' => $order->total,
            'qr_string' => $charge['qr_string'],
            'qr_url' => $charge['qr_url'],
            'expires_at' => $order->expires_at,
        ]);
    }

    /** Dipanggil di dalam transaksi applyPaymentResult, baris order sudah dikunci. */
    private function markPaid(Order $order): void
    {
        $order->update([
            'status' => OrderStatus::Paid,
            'paid_at' => now(),
            'qr_token' => Str::random(40),
        ]);

        $today = today()->toDateString();

        $order->items()
            ->selectRaw('menu_id, SUM(qty) as total_qty')
            ->groupBy('menu_id')
            ->get()
            ->each(function ($row) use ($today) {
                $stock = DailyStock::query()
                    ->whereDate('date', $today)
                    ->where('menu_id', $row->menu_id)
                    ->lockForUpdate()
                    ->first()
                    ?? DailyStock::create(['menu_id' => $row->menu_id, 'date' => $today, 'stock' => 0, 'sold' => 0]);

                $stock->increment('sold', (int) $row->total_qty);
            });
    }

    private function expireOne(int $orderId): void
    {
        $cancelIds = [];

        DB::transaction(function () use ($orderId, &$cancelIds) {
            $order = Order::whereKey($orderId)->lockForUpdate()->first();

            if (! $order || $order->status !== OrderStatus::PendingPayment || $order->expires_at->isFuture()) {
                return;
            }

            $order->update(['status' => OrderStatus::Expired]);

            $cancelIds = $order->payments()->where('status', 'pending')->pluck('gateway_order_id')->all();
            $order->payments()->where('status', 'pending')->update(['status' => 'expired']);
        });

        foreach ($cancelIds as $gatewayOrderId) {
            $this->midtrans->cancel($gatewayOrderId);
        }
    }

    /** Kode PSD-yymmdd-NNNN. Baca terakhir dengan lock supaya dua checkout bersamaan tidak mendapat nomor sama. */
    private function nextCode(): string
    {
        $prefix = 'PSD-'.now()->format('ymd').'-';

        $last = Order::where('code', 'like', $prefix.'%')
            ->orderByDesc('code')
            ->lockForUpdate()
            ->value('code');

        $next = ($last ? (int) substr($last, -4) : 0) + 1;

        return $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    private function stockErrors(array $lines): array
    {
        $errors = [];

        foreach ($lines as $i => $line) {
            if ($line['available']) {
                continue;
            }

            $menu = $line['menu'];

            $errors["items.$i.qty"] = [match (true) {
                $menu === null => 'Menu tidak ditemukan.',
                ! $menu->is_active => "{$menu->name} tidak tersedia.",
                default => "Stok {$menu->name} tersisa {$line['stock_remaining']}.",
            }];
        }

        return $errors;
    }
}