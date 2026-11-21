<?php // app/Http/Resources/StaffOrderResource.php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Order */
class StaffOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->code,
            'customer_name' => $this->user?->name,
            'order_type' => $this->order_type->value,
            'status' => $this->status->value,
            'scanned_at' => $this->scanned_at?->toIso8601String(),
            'done_at' => $this->done_at?->toIso8601String(),
            'total' => $this->when($this->relationLoaded('items'), fn () => $this->total),
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($item) => [
                'menu_id' => $item->menu_id,
                'name' => $item->menu?->name,
                'qty' => $item->qty,
                'unit_price' => $item->unit_price,
                'spice_level' => $item->spice_level,
                'note' => $item->note,
            ])->values()),
        ];
    }
}