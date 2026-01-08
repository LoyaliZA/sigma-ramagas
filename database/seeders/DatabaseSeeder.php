<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role; // Asegúrate de importar esto
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. IMPORTANTE: Ejecutar primero los catálogos para asegurar que existan Roles, Puestos, etc.
        $this->call([
            CatalogosSeeder::class,
        ]);

        // 2. Crear usuario de forma segura (sin error si ya existe)
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'], // Busca por este campo
            [
                'name' => 'Test User',
                // Si tienes un mutador de password o usas Laravel 11, esto se encripta solo,
                // si no, usa: 'password' => bcrypt('password'),
                'password' => 'password', 
                'email_verified_at' => now(),
            ]
        );

        // 3. Asignar el Rol de Super Admin de forma segura
        $role = Role::where('nombre', 'Super Admin')->first();

        if ($role) {
            // Verifica si el usuario YA tiene ese rol para no duplicarlo
            if (! $user->roles()->where('rol_id', $role->id)->exists()) {
                $user->roles()->attach($role->id);
            }
        }
    }
}