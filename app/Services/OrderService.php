<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Exceptions\BusinessException;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(private readonly StockService $stocks) {}

    public function create(array $data, ?int $userId = null): Order
    {
        return DB::transaction(function () use ($data, $userId) {
            $order = Order::create(['order_number' => now()->format('YmdHis').'-'.Str::upper(Str::random(4)), 'user_id' => $userId, 'order_type' => $data['order_type'], 'status' => OrderStatus::PendingPayment, 'expires_at' => now()->addMinutes(config('midtrans.expiry_minutes'))]);
            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $menu = \App\Models\Menu::findOrFail($item['menu_id']);
                $this->stocks->reserve($menu, (int) $item['quantity']);
                $line = $menu->price * (int) $item['quantity'];
                $order->items()->create(['menu_id' => $menu->id, 'quantity' => $item['quantity'], 'unit_price' => $menu->price, 'subtotal' => $line]);
                $subtotal += $line;
            }
            $order->update(['subtotal' => $subtotal, 'total' => $subtotal]);
            return $order->load('items.menu', 'payment');
        });
    }

    public function changeType(Order $order, string $type, ?int $userId = null): Order
    {
        if ($order->status !== OrderStatus::PendingPayment) throw new BusinessException('Jenis pesanan hanya dapat diubah sebelum pembayaran.');
        $old = $order->order_type->value;
        $order->update(['order_type' => $type]);
        $order->typeLogs()->create(['from_type' => $old, 'to_type' => $type, 'changed_by' => $userId]);
        return $order->refresh();
    }

    public function expireUnpaid(): int
    {
        return Order::where('status', OrderStatus::PendingPayment)->whereNotNull('expires_at')->where('expires_at', '<', now())->update(['status' => OrderStatus::Expired]);
    }
}
