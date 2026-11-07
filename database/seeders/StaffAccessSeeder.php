<?php

namespace Database\Seeders;

use App\Enums\StaffRole;
use App\Models\StaffAccess;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffAccessSeeder extends Seeder
{
    public function run(): void
    {
        // PIN dev. Wajib diganti sebelum deploy. Tiap role harus beda PIN.
        $pins = [
            StaffRole::Kasir->value => '111111',
            StaffRole::Dapur->value => '222222',
            StaffRole::Admin->value => '333333',
        ];

        foreach ($pins as $role => $pin) {
            StaffAccess::updateOrCreate(['role' => $role], ['pin_hash' => Hash::make($pin)]);
        }
    }
}