<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $category = MenuCategory::firstOrCreate(['slug' => 'makanan'], ['name' => 'Makanan']);
        Menu::firstOrCreate(['slug' => 'nasi-goreng'], ['menu_category_id' => $category->id, 'name' => 'Nasi Goreng', 'price' => 15000, 'is_active' => true]);
    }
}
