<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Order */
class OrderSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->code,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'order_type' => $this->order_type->value,
            'total' => $this->total,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}