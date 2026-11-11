<?php

namespace App\Http\Resources;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Order */
class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->code,
            'order_type' => $this->order_type->value,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'total' => $this->total,
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($item) => [
                'menu_id' => $item->menu_id,
                'name' => $item->menu?->name,
                'qty' => $item->qty,
                'unit_price' => $item->unit_price,
                'spice_level' => $item->spice_level,
                'note' => $item->note,
            ])->values()),
            // QR hanya tampil saat menunggu di-scan. Setelah itu tidak ditampilkan lagi.
            'qr_token' => $this->status === OrderStatus::Paid ? $this->qr_token : null,
            'estimate_minutes' => null, // diisi QueueEstimator di Part 5
            'can_change_type' => $this->status === OrderStatus::Paid,
            'type_logs' => $this->whenLoaded('typeLogs', fn () => $this->typeLogs->map(fn ($log) => [
                'old_type' => $log->old_type->value,
                'new_type' => $log->new_type->value,
                'changed_at' => $log->changed_at->toIso8601String(),
            ])->values()),
            'payment' => $this->whenLoaded('payment', fn () => $this->payment ? new PaymentResource($this->payment) : null),
            'created_at' => $this->created_at->toIso8601String(),
            'expires_at' => $this->expires_at->toIso8601String(),
        ];
    }
}