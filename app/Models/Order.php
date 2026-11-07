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
    protected $fillable = [
        'user_id', 'code', 'order_type', 'qr_token', 'status', 'total',
        'expires_at', 'paid_at', 'scanned_at', 'done_at', 'picked_up_at',
    ];

    protected function casts(): array
    {
        return [
            'order_type' => OrderType::class,
            'status' => OrderStatus::class,
            'total' => 'integer',
            'expires_at' => 'datetime',
            'paid_at' => 'datetime',
            'scanned_at' => 'datetime',
            'done_at' => 'datetime',
            'picked_up_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function typeLogs(): HasMany
    {
        return $this->hasMany(OrderTypeLog::class)->orderBy('changed_at');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }
}