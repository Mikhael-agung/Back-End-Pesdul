<?php

namespace App\Http\Resources;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Menu */
class MenuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $remaining = $this->todayStock?->remaining ?? 0;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'image_url' => $this->image_url,
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ]),
            'has_spice_level' => $this->has_spice_level,
            'spice_levels' => $this->has_spice_level ? Menu::SPICE_LEVELS : [],
            'stock_remaining' => $remaining,
            'is_available' => $remaining > 0,
        ];
    }
}