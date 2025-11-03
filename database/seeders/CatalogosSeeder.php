<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usar truncate en lugar de delete para reiniciar los IDs autoincrementables
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('catalogo_motivosbaja')->truncate();
        DB::table('catalogo_departamentos')->truncate();
        DB::table('catalogo_ubicaciones')->truncate();
        DB::table('catalogo_condiciones')->truncate();
        DB::table('catalogo_estadosactivo')->truncate();
        DB::table('catalogo_estadosasignacion')->truncate();
        DB::table('catalogo_puestos')->truncate(); // <--- NUEVA TABLA
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        // --- Llenar Catálogos ---

        DB::table('catalogo_motivosbaja')->insert([
            ['nombre' => 'Dañado', 'comentarios_baja' => ''],
            ['nombre' => 'Extraviado', 'comentarios_baja' => ''],
            ['nombre' => 'Robado', 'comentarios_baja' => ''],
            ['nombre' => 'Obsoleto', 'comentarios_baja' => ''],
        ]);

        DB::table('catalogo_departamentos')->insert([
            ['nombre' => 'Sistemas'],
            ['nombre' => 'Operaciones'],
            ['nombre' => 'Administración'],
            ['nombre' => 'Mantenimiento'],
            ['nombre' => 'Recursos Humanos'],
        ]);

        DB::table('catalogo_ubicaciones')->insert([
            ['nombre' => 'Oficinas Centrales Villahermosa'],
            ['nombre' => 'Planta Cárdenas'],
            ['nombre' => 'Planta Comalcalco'],
            ['nombre' => 'Estación de Servicio Norte'],
            ['nombre' => 'Almacén Central'],
        ]);
        
        DB::table('catalogo_condiciones')->insert([
            ['nombre' => 'Nuevo'],
            ['nombre' => 'Usado'],
            ['nombre' => 'Semi-nuevo'],
        ]);

        DB::table('catalogo_estadosactivo')->insert([
            ['nombre' => 'Disponible'],
            ['nombre' => 'En Uso'],
            ['nombre' => 'En Mantenimiento'],
            ['nombre' => 'En Diagnóstico'],
            ['nombre' => 'Pendiente de Baja'],
            ['nombre' => 'Baja'],
        ]);

        DB::table('catalogo_estadosasignacion')->insert([
            ['nombre' => 'Funcional'],
            ['nombre' => 'Con detalles estéticos'],
            ['nombre' => 'Requiere reparación'],
            ['nombre' => 'Dañado'],
        ]);

        // <--- NUEVO SEEDER --->
        DB::table('catalogo_puestos')->insert([
            ['nombre' => 'Gerente de Sistemas'],
            ['nombre' => 'Analista de Sistemas'],
            ['nombre' => 'Coordinador de Operaciones'],
            ['nombre' => 'Técnico de Mantenimiento'],
            ['nombre' => 'Contador'],
            ['nombre' => 'Analista Administrativo'],
            ['nombre' => 'Ingeniero de TICS'],
        ]);
    }
}
