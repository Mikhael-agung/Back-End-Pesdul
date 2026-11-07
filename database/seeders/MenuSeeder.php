<?php

namespace Database\Seeders;

use App\Models\DailyStock;
use App\Models\Menu;
use App\Models\MenuCategory;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // kategori => [[nama, harga, pakai level pedas]]
        $data = [
            'Makanan' => [
                ['Nasi Goreng Spesial', 25000, true],
                ['Mie Goreng', 22000, true],
                ['Ayam Geprek', 20000, true],
                ['Nasi Ayam Bakar', 27000, false],
            ],
            'Minuman' => [
                ['Es Teh Manis', 6000, false],
                ['Es Jeruk', 8000, false],
                ['Kopi Susu', 15000, false],
            ],
            'Camilan' => [
                ['Kentang Goreng', 12000, false],
            ],
        ];

        foreach ($data as $categoryName => $items) {
            $category = MenuCategory::firstOrCreate(['name' => $categoryName]);

            foreach ($items as [$name, $price, $spice]) {
                $menu = Menu::firstOrCreate(
                    ['name' => $name],
                    ['category_id' => $category->id, 'price' => $price, 'has_spice_level' => $spice, 'is_active' => true],
                );

                DailyStock::firstOrCreate(
                    ['menu_id' => $menu->id, 'date' => today()->toDateString()],
                    ['stock' => 20, 'sold' => 0],
                );
            }
        }
    }
}