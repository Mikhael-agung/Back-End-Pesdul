<?php

namespace Database\Seeders;

use App\Enums\StaffRole;
use App\Models\StaffAccess;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffAccessSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(['phone' => '081234567890'], ['name' => 'Administrator', 'password' => 'password']);
        StaffAccess::updateOrCreate(['user_id' => $user->id], ['role' => StaffRole::Admin, 'pin_hash' => Hash::make('123456'), 'is_active' => true]);
    }
}
