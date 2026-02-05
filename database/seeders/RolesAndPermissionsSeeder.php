<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos para los modelos
        Permission::firstOrCreate(['name' => 'create users']);
        Permission::firstOrCreate(['name' => 'edit users']);
        Permission::firstOrCreate(['name' => 'delete users']);
        Permission::firstOrCreate(['name' => 'show users']);

        // Crear el rol super-admin y asignarle los permisos
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdminRole->givePermissionTo([
            'create users',
            'edit users',
            'delete users',
            'show users',
        ]);

        // Crear roles adicionales requeridos por la aplicación
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Atención al Cliente']);
        Role::firstOrCreate(['name' => 'Cajera']);
    }
}
