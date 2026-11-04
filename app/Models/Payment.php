<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = ['order_id', 'provider', 'transaction_id', 'status', 'gross_amount', 'paid_at'];
    protected $casts = ['gross_amount' => 'integer', 'paid_at' => 'datetime'];
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
}
