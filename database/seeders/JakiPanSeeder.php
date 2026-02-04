<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class JakiPanSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $acRole = Role::firstOrCreate(['name' => 'Atención al Cliente']); // AC
        $cashierRole = Role::firstOrCreate(['name' => 'Cajera']);

        // 2. Create Users
        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@jakipan.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole($adminRole);

        // AC (2 users)
        $ac1 = User::firstOrCreate(
            ['email' => 'ac1@jakipan.com'],
            [
                'name' => 'Atención Cliente 1',
                'password' => Hash::make('password'),
            ]
        );
        $ac1->assignRole($acRole);

        $ac2 = User::firstOrCreate(
            ['email' => 'ac2@jakipan.com'],
            [
                'name' => 'Atención Cliente 2',
                'password' => Hash::make('password'),
            ]
        );
        $ac2->assignRole($acRole);

        // Cashiers (2 users)
        $cashier1 = User::firstOrCreate(
            ['email' => 'cajera1@jakipan.com'],
            [
                'name' => 'Cajera 1',
                'password' => Hash::make('password'),
            ]
        );
        $cashier1->assignRole($cashierRole);

        $cashier2 = User::firstOrCreate(
            ['email' => 'cajera2@jakipan.com'],
            [
                'name' => 'Cajera 2',
                'password' => Hash::make('password'),
            ]
        );
        $cashier2->assignRole($cashierRole);

        // 3. Create Products (20 items)
        $categories = ['Pan', 'Pastelería', 'Bebidas'];
        
        $products = [
            ['name' => 'Pan Francés', 'price' => 0.50, 'cat' => 'Pan'],
            ['name' => 'Baguette', 'price' => 1.50, 'cat' => 'Pan'],
            ['name' => 'Pan de Yema', 'price' => 0.80, 'cat' => 'Pan'],
            ['name' => 'Croissant', 'price' => 2.00, 'cat' => 'Pastelería'],
            ['name' => 'Donas', 'price' => 1.20, 'cat' => 'Pastelería'],
            ['name' => 'Pastel de Chocolate', 'price' => 3.50, 'cat' => 'Pastelería'],
            ['name' => 'Cheesecake', 'price' => 4.00, 'cat' => 'Pastelería'],
            ['name' => 'Café Americano', 'price' => 2.50, 'cat' => 'Bebidas'],
            ['name' => 'Capuchino', 'price' => 3.00, 'cat' => 'Bebidas'],
            ['name' => 'Jugo de Naranja', 'price' => 2.00, 'cat' => 'Bebidas'],
            ['name' => 'Pan Integral', 'price' => 1.80, 'cat' => 'Pan'],
            ['name' => 'Empanada de Carne', 'price' => 2.50, 'cat' => 'Pastelería'],
            ['name' => 'Empanada de Pollo', 'price' => 2.50, 'cat' => 'Pastelería'],
            ['name' => 'Gaseosa Cola', 'price' => 1.50, 'cat' => 'Bebidas'],
            ['name' => 'Agua Mineral', 'price' => 1.00, 'cat' => 'Bebidas'],
            ['name' => 'Tarta de Manzana', 'price' => 3.80, 'cat' => 'Pastelería'],
            ['name' => 'Brownie', 'price' => 2.00, 'cat' => 'Pastelería'],
            ['name' => 'Pan Chapla', 'price' => 0.60, 'cat' => 'Pan'],
            ['name' => 'Pan Ciabatta', 'price' => 0.70, 'cat' => 'Pan'],
            ['name' => 'Leche Chocolatada', 'price' => 2.20, 'cat' => 'Bebidas'],
        ];

        foreach ($products as $p) {
            Product::create([
                'name' => $p['name'],
                'price' => $p['price'],
                'stock' => rand(20, 100),
                'provisional_image' => null, // Placeholder
            ]);
        }
    }
}
