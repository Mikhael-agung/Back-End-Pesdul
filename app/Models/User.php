<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $fillable = ['name', 'identifier', 'password'];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

        /** Email jadi huruf kecil. Nomor HP jadi format 08xxxx (+62/62 diganti 0). */
    public static function normalizeIdentifier(string $value): string
    {
        $v = strtolower(trim($value));

        if (str_contains($v, '@')) {
            return $v;
        }

        $v = preg_replace('/[\s\-]/', '', $v);

        return preg_replace('/^(\+62|62)/', '0', $v);
    }
}