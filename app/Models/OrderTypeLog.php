<?php

namespace App\Models;

use App\Enums\OrderType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTypeLog extends Model
{
    public const CREATED_AT = 'changed_at';
    public const UPDATED_AT = null;

    protected $fillable = ['order_id', 'old_type', 'new_type'];

    protected function casts(): array
    {
        return [
            'old_type' => OrderType::class,
            'new_type' => OrderType::class,
            'changed_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}