<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockRowResource extends JsonResource
{
    public function toArray(Request $request): array { return ['id' => $this->id, 'menu_id' => $this->menu_id, 'menu_name' => $this->menu?->name, 'stock_date' => $this->stock_date?->toDateString(), 'quantity' => $this->quantity, 'sold_quantity' => $this->sold_quantity, 'remaining' => $this->remaining]; }
}
