<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyStock extends Model
{
    protected $fillable = ['menu_id', 'stock_date', 'quantity', 'sold_quantity'];
    protected $casts = ['stock_date' => 'date', 'quantity' => 'integer', 'sold_quantity' => 'integer'];

    public function menu(): BelongsTo { return $this->belongsTo(Menu::class); }
    public function getRemainingAttribute(): int { return max(0, $this->quantity - $this->sold_quantity); }
}
