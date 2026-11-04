<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\DailyStock;
use App\Models\Menu;
use Illuminate\Support\Carbon;

class StockService
{
    public function available(Menu $menu, int $quantity, ?Carbon $date = null): bool
    {
        $stock = DailyStock::where('menu_id', $menu->id)->whereDate('stock_date', $date ?? now())->first();
        return $stock !== null && $stock->remaining >= $quantity;
    }

    public function reserve(Menu $menu, int $quantity): void
    {
        $stock = DailyStock::where('menu_id', $menu->id)->whereDate('stock_date', today())->lockForUpdate()->first();
        if (! $stock || $stock->remaining < $quantity) {
            throw new BusinessException("Stok {$menu->name} tidak mencukupi.");
        }
        $stock->increment('sold_quantity', $quantity);
    }
}
