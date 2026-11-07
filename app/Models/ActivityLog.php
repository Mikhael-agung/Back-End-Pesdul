<?php

namespace App\Models;

use App\Enums\StaffRole;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['role', 'action', 'target', 'detail'];

    protected function casts(): array
    {
        return [
            'role' => StaffRole::class,
            'detail' => 'array',
            'created_at' => 'datetime',
        ];
    }
}