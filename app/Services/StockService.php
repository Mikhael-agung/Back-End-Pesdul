<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\DailyStock;
use App\Models\Menu;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StockService
{
    /** Stok hari ini untuk semua menu aktif. Menu yang belum diset admin dianggap stok 0 (Habis). */
    public function todayRows(): Collection
    {
        $stocks = DailyStock::whereDate('date', today())->get()->keyBy('menu_id');

        return Menu::active()->orderBy('name')->get(['id', 'name'])->map(function (Menu $menu) use ($stocks) {
            $row = $stocks->get($menu->id);
            $stock = $row?->stock ?? 0;
            $sold = $row?->sold ?? 0;

            return [
                'menu_id' => $menu->id,
                'name' => $menu->name,
                'stock' => $stock,
                'sold' => $sold,
                'remaining' => max(0, $stock - $sold),
            ];
        })->values();
    }

    /**
     * Simpan stok hari ini. Stok baru tidak boleh kurang dari jumlah terjual (FR-21).
     *
     * @return array daftar perubahan [{menu_id, name, from, to}] (kosong kalau tidak ada yang berubah)
     */
    public function updateToday(array $items): array
    {
        $items = collect($items)->map(fn ($i) => [
            'menu_id' => (int) $i['menu_id'],
            'stock' => (int) $i['stock'],
        ])->all();

        $ids = array_column($items, 'menu_id');
        $today = today()->toDateString();

        return DB::transaction(function () use ($items, $ids, $today) {
            $existing = DailyStock::whereDate('date', $today)
                ->whereIn('menu_id', $ids)
                ->lockForUpdate()
                ->get()
                ->keyBy('menu_id');

            $names = Menu::whereIn('id', $ids)->pluck('name', 'id');

            $errors = [];
            foreach ($items as $item) {
                $sold = $existing->get($item['menu_id'])?->sold ?? 0;

                if ($item['stock'] < $sold) {
                    $errors['menu_'.$item['menu_id']] = [
                        "Stok {$names[$item['menu_id']]} tidak boleh kurang dari jumlah terjual ({$sold}).",
                    ];
                }
            }

            if ($errors) {
                throw new BusinessException(
                    'Stok baru kurang dari jumlah yang sudah terjual hari ini.',
                    'STOCK_BELOW_SOLD',
                    422,
                    $errors,
                );
            }

            $changes = [];
            foreach ($items as $item) {
                $row = $existing->get($item['menu_id']);
                $old = $row?->stock ?? 0;

                if ($row) {
                    $row->update(['stock' => $item['stock']]);
                } else {
                    DailyStock::create([
                        'menu_id' => $item['menu_id'],
                        'date' => $today,
                        'stock' => $item['stock'],
                        'sold' => 0,
                    ]);
                }

                if ($old !== $item['stock']) {
                    $changes[] = [
                        'menu_id' => $item['menu_id'],
                        'name' => $names[$item['menu_id']],
                        'from' => $old,
                        'to' => $item['stock'],
                    ];
                }
            }

            return $changes;
        });
    }
}