<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array { return ['id' => $this->id, 'order_number' => $this->order_number, 'status' => $this->status?->value, 'order_type' => $this->order_type?->value, 'subtotal' => $this->subtotal, 'total' => $this->total, 'expires_at' => $this->expires_at, 'items' => OrderItemResource::collection($this->whenLoaded('items')), 'payment' => new PaymentResource($this->whenLoaded('payment'))]; }
}
