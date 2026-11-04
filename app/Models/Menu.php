<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $fillable = ['menu_category_id', 'name', 'slug', 'description', 'price', 'image_path', 'is_active'];
    protected $casts = ['price' => 'integer', 'is_active' => 'boolean'];

    public function category(): BelongsTo { return $this->belongsTo(MenuCategory::class, 'menu_category_id'); }
    public function stocks(): HasMany { return $this->hasMany(DailyStock::class); }
    public function orderItems(): HasMany { return $this->hasMany(OrderItem::class); }
}
