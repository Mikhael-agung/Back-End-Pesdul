<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyStock extends Model
{
    protected $fillable = ['menu_id', 'date', 'stock', 'sold'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'stock' => 'integer',
            'sold' => 'integer',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    protected function remaining(): Attribute
    {
        return Attribute::get(fn () => max(0, $this->stock - $this->sold));
    }
}