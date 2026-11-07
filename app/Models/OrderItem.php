<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'menu_id', 'qty', 'unit_price', 'spice_level', 'note'];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'unit_price' => 'integer',
            'spice_level' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}