<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // <-- ¡Asegúrate de importar esto!

class CatalogosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Aquí pones tus inserts
        DB::table('catalogo_motivosbaja')->insert([
            ['id' => 1, 'nombre' => 'Dañado', 'comentarios_baja' => ''],
            ['id' => 2, 'nombre' => 'Extraviado', 'comentarios_baja' => ''],
            ['id' => 3, 'nombre' => 'Robado', 'comentarios_baja' => ''],
            ['id' => 4, 'nombre' => 'Obsoleto', 'comentarios_baja' => ''],
        ]);

        // ¡Agrega aquí los inserts para tus otros catálogos!
        // (catalogo_condiciones, catalogo_departamentos, etc.)
    }
}