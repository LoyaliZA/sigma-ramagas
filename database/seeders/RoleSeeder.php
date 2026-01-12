<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Resetear cache de roles y permisos para evitar errores
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Crear Roles (usa firstOrCreate para evitar duplicados)
        // El guard_name suele ser 'web' por defecto en Laravel
        
        $role1 = Role::firstOrCreate(['name' => 'Super Admin']);
        $role2 = Role::firstOrCreate(['name' => 'Admin']);
        $role3 = Role::firstOrCreate(['name' => 'Empleado']); // Opcional, si lo usas
    }
}