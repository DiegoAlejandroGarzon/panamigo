<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Brand;

class CategoryAndBrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['Bebidas', 'Abarrotes', 'Pan', 'Lácteos', 'Limpieza'];
        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat]);
        }

        $brands = ['Alpina', 'Postobon', 'Cocacola', 'Jaki-Pan', 'Colgate', 'Diana'];
        foreach ($brands as $brand) {
            Brand::firstOrCreate(['name' => $brand]);
        }
    }
}
