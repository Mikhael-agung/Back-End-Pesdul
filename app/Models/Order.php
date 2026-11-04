<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = ['order_number', 'user_id', 'status', 'order_type', 'subtotal', 'total', 'expires_at'];
    protected $casts = ['status' => OrderStatus::class, 'order_type' => OrderType::class, 'expires_at' => 'datetime'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function items(): HasMany { return $this->hasMany(OrderItem::class); }
    public function payment(): HasOne { return $this->hasOne(Payment::class); }
    public function typeLogs(): HasMany { return $this->hasMany(OrderTypeLog::class); }
}
