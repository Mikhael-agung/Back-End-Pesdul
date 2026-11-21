<?php // app/Http/Resources/KitchenCardResource.php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Order */
class KitchenCardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->code,
            'order_type' => $this->order_type->value,
            'scanned_at' => $this->scanned_at?->toIso8601String(),
            'items' => $this->items->map(fn ($item) => [
                'menu_id' => $item->menu_id,
                'name' => $item->menu?->name,
                'qty' => $item->qty,
                'spice_level' => $item->spice_level,
                'note' => $item->note,
            ])->values(),
        ];
    }
}