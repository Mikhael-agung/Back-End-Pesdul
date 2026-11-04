<?php

namespace App\Console\Commands;

use App\Services\OrderService;
use Illuminate\Console\Command;

class ExpireUnpaidOrders extends Command
{
    protected $signature = 'orders:expire-unpaid';
    protected $description = 'Expire unpaid orders past their payment deadline';

    public function handle(OrderService $orders): int
    {
        $this->info("Expired {$orders->expireUnpaid()} unpaid order(s).");
        return self::SUCCESS;
    }
}
