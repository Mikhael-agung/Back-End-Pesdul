<?php

namespace App\Services;

use App\Models\Order;

class QueueEstimator
{
    public function estimate(Order $order): int
    {
        return Order::whereIn('status', ['paid', 'processing'])->where('id', '<=', $order->id)->count();
    }
}
