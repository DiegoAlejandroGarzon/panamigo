<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super admin
        $admin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Admin',
                'lastname' => 'Super-Admin',
                'password' => Hash::make('12345678'),
                'status' => true,
            ]
        );
        $adminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $admin->assignRole($adminRole);

        // Roles del sistema
        Role::firstOrCreate(['name' => 'Cajera']);
    }
}
