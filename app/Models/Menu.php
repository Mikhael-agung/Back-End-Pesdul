<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Menu extends Model
{
    public const SPICE_LEVELS = [1, 2, 3];

    protected $fillable = ['category_id', 'name', 'price', 'photo', 'has_spice_level', 'is_active'];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'has_spice_level' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class, 'category_id');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(DailyStock::class);
    }

    public function todayStock(): HasOne
    {
        return $this->hasOne(DailyStock::class)->whereDate('date', today());
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->photo
            ? asset('storage/'.$this->photo)
            : asset('images/default-menu.svg'));
    }
}