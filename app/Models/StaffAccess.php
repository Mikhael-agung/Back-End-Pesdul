<?php

namespace App\Models;

use App\Enums\StaffRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffAccess extends Model
{
    protected $fillable = ['user_id', 'role', 'pin_hash', 'is_active'];
    protected $casts = ['role' => StaffRole::class, 'is_active' => 'boolean'];
    protected $hidden = ['pin_hash'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
