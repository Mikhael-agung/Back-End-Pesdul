<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array { return ['menu_id' => $this->menu_id, 'menu_name' => $this->menu?->name, 'quantity' => $this->quantity, 'unit_price' => $this->unit_price, 'subtotal' => $this->subtotal]; }
}
