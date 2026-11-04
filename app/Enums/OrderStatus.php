<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PendingPayment = 'pending_payment';
    case Paid = 'paid';
    case Cooking = 'cooking';
    case Done = 'done';
    case PickedUp = 'picked_up';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::PendingPayment => 'Menunggu pembayaran',
            self::Paid => 'Dibayar',
            self::Cooking => 'Dimasak',
            self::Done => 'Selesai',
            self::PickedUp => 'Diambil',
            self::Expired => 'Kedaluwarsa',
        };
    }

    /** Status yang dihitung sebagai pendapatan (sudah dibayar). */
    public static function revenue(): array
    {
        return [self::Paid, self::Cooking, self::Done, self::PickedUp];
    }
}