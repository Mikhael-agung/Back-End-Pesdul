<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;

class QueueEstimator
{
    public const MINUTES_PER_ORDER = 5;

    /** FR-25: pesanan cooking yang discan lebih awal x 5 menit. Dihitung tiap dibaca, tidak disimpan. */
    public function minutesFor(Order $order): ?int
    {
        return match ($order->status) {
            OrderStatus::Cooking => $this->ahead($order) * self::MINUTES_PER_ORDER,
            OrderStatus::Done, OrderStatus::PickedUp => 0,
            default => null,
        };
    }

    private function ahead(Order $order): int
    {
        return Order::where('status', OrderStatus::Cooking->value)
            ->where(fn ($q) => $q
                ->where('scanned_at', '<', $order->scanned_at)
                ->orWhere(fn ($q) => $q->where('scanned_at', $order->scanned_at)->where('id', '<', $order->id)))
            ->count();
    }
}