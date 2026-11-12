<?php

namespace App\Console\Commands;

use App\Services\OrderService;
use Illuminate\Console\Command;

class ExpireUnpaidOrders extends Command
{
    protected $signature = 'orders:expire-unpaid';

    protected $description = 'Kedaluwarsakan pesanan yang belum dibayar melewati batas waktu';

    public function handle(OrderService $orders): int
    {
        $count = $orders->expireOverdue();

        $this->info("{$count} pesanan dikedaluwarsakan.");

        return self::SUCCESS;
    }
}