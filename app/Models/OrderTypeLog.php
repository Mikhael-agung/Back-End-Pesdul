<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTypeLog extends Model
{
    protected $fillable = ['order_id', 'from_type', 'to_type', 'changed_by'];
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class, 'changed_by'); }
}
