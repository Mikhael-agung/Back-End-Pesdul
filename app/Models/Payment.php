<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id', 'gateway_order_id', 'transaction_id', 'status',
        'gross_amount', 'qr_string', 'qr_url', 'expires_at', 'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'gross_amount' => 'integer',
            'expires_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}