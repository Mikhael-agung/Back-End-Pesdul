<?php

namespace App\Models;

use App\Enums\StaffRole;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class StaffAccess extends Model
{
    use HasApiTokens;

    protected $fillable = ['role', 'pin_hash'];

    protected $hidden = ['pin_hash'];

    protected function casts(): array
    {
        return ['role' => StaffRole::class];
    }
}