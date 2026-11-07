<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $timestamps = false;
    public $incrementing = false;

    protected $primaryKey = 'key';
    protected $keyType = 'string';
    protected $fillable = ['key', 'value'];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        return static::query()->whereKey($key)->value('value') ?? $default;
    }

    public static function putValue(string $key, string $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function dineInFull(): bool
    {
        return static::getValue('dine_in_full', '0') === '1';
    }
}