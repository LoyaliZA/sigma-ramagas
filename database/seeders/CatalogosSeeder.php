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
        // 1. Roles
        // Se busca por 'id'. Si existe, actualiza nombre/descripcion. Si no, inserta.
        DB::table('roles')->upsert([
            ['id' => 1, 'nombre' => 'Super Admin', 'descripcion' => 'Usuario con acceso total al sistema'],
            ['id' => 2, 'nombre' => 'Admin',       'descripcion' => 'Administrador'],
            ['id' => 3, 'nombre' => 'Empleado',    'descripcion' => 'Solo lectura'],
        ], ['id'], ['nombre', 'descripcion']);


        // --- Llenar Catálogos (Usando upsert por 'nombre') ---

        DB::table('catalogo_motivosbaja')->upsert([
            ['nombre' => 'Dañado', 'comentarios_baja' => ''],
            ['nombre' => 'Extraviado', 'comentarios_baja' => ''],
            ['nombre' => 'Robado', 'comentarios_baja' => ''],
            ['nombre' => 'Obsoleto', 'comentarios_baja' => ''],
        ], ['nombre'], ['comentarios_baja']);

        DB::table('catalogo_departamentos')->upsert([
            ['nombre' => 'Sistemas'],
            ['nombre' => 'Operaciones'],
            ['nombre' => 'Administración'],
            ['nombre' => 'Mantenimiento'],
            ['nombre' => 'Recursos Humanos'],
        ], ['nombre'], ['nombre']);

        DB::table('catalogo_ubicaciones')->upsert([
            ['nombre' => 'Oficinas Centrales Villahermosa'],
            ['nombre' => 'Planta Cárdenas'],
            ['nombre' => 'Planta Comalcalco'],
            ['nombre' => 'Estación de Servicio Norte'],
            ['nombre' => 'Almacén Central'],
        ], ['nombre'], ['nombre']);
        
        DB::table('catalogo_condiciones')->upsert([
            ['nombre' => 'Nuevo'],
            ['nombre' => 'Usado'],
            ['nombre' => 'Semi-nuevo'],
        ], ['nombre'], ['nombre']);

        DB::table('catalogo_estadosactivo')->upsert([
            ['nombre' => 'Disponible'],
            ['nombre' => 'En Uso'],
            ['nombre' => 'En Mantenimiento'],
            ['nombre' => 'En Diagnóstico'],
            ['nombre' => 'Pendiente de Baja'],
            ['nombre' => 'Baja'],
        ], ['nombre'], ['nombre']);

        DB::table('catalogo_estadosasignacion')->upsert([
            ['nombre' => 'Funcional'],
            ['nombre' => 'Con detalles estéticos'],
            ['nombre' => 'Requiere reparación'],
            ['nombre' => 'Dañado'],
        ], ['nombre'], ['nombre']);

        // <--- NUEVO SEEDER --->
        DB::table('catalogo_puestos')->upsert([
            ['nombre' => 'Gerente de Sistemas'],
            ['nombre' => 'Analista de Sistemas'],
            ['nombre' => 'Coordinador de Operaciones'],
            ['nombre' => 'Técnico de Mantenimiento'],
            ['nombre' => 'Contador'],
            ['nombre' => 'Analista Administrativo'],
            ['nombre' => 'Ingeniero de TICS'],
        ], ['nombre'], ['nombre']);
    }
}