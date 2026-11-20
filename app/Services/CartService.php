<?php

namespace App\Services;

use App\Models\Menu;
use Illuminate\Validation\ValidationException;

class CartService
{
    /**
     * Hitung isi keranjang terhadap harga dan stok hari ini.
     *
     * @param  array  $items  [{menu_id, qty, spice_level?, note?}]
     * @param  bool  $lock  true kalau dipanggil di dalam transaksi checkout (Part 4), mengunci baris stok
     * @return array{valid: bool, total: int, lines: array}
     */
    public function evaluate(array $items, bool $lock = false): array
    {
        $items = array_values($items);
        $ids = collect($items)->pluck('menu_id')->map(fn ($id) => (int) $id)->unique()->all();

        $menus = Menu::query()
            ->with(['todayStock' => fn ($q) => $lock ? $q->lockForUpdate() : $q])
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        // Total qty per menu (menu sama boleh muncul dua baris dengan level pedas beda)
        $qtyByMenu = [];
        foreach ($items as $item) {
            $id = (int) $item['menu_id'];
            $qtyByMenu[$id] = ($qtyByMenu[$id] ?? 0) + (int) $item['qty'];
        }

        $lines = [];
        $errors = [];
        $total = 0;
        $valid = true;

        foreach ($items as $i => $item) {
            $menuId = (int) $item['menu_id'];
            $qty = (int) $item['qty'];
            $spice = isset($item['spice_level']) ? (int) $item['spice_level'] : null;
            $note = isset($item['note']) && trim($item['note']) !== '' ? trim($item['note']) : null;
            $menu = $menus->get($menuId);

            if ($menu) {
                if ($menu->has_spice_level && $spice === null) {
                    $errors["items.$i.spice_level"] = ['Level pedas wajib dipilih.'];
                } elseif ($menu->has_spice_level && ! in_array($spice, Menu::SPICE_LEVELS, true)) {
                    $errors["items.$i.spice_level"] = ['Level pedas tidak valid.'];
                } elseif (! $menu->has_spice_level && $spice !== null) {
                    $errors["items.$i.spice_level"] = ['Menu ini tidak punya level pedas.'];
                }
            }

            $remaining = $menu?->todayStock?->remaining ?? 0;
            $available = $menu !== null && $menu->is_active && $qtyByMenu[$menuId] <= $remaining;
            $unitPrice = $menu?->price ?? 0;

            $valid = $valid && $available;
            $total += $unitPrice * $qty;

            $lines[] = [
                'menu' => $menu,
                'menu_id' => $menuId,
                'qty' => $qty,
                'unit_price' => $unitPrice,
                'spice_level' => $spice,
                'note' => $note,
                'available' => $available,
                'stock_remaining' => $remaining,
            ];
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        return ['valid' => $valid && $lines !== [], 'total' => $total, 'lines' => $lines];
    }
}