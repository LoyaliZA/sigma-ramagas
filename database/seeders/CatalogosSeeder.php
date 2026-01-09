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
        // 1. Limpiar tablas (reiniciar IDs)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('catalogo_motivosbaja')->truncate();
        DB::table('catalogo_departamentos')->truncate();
        DB::table('catalogo_ubicaciones')->truncate();
        DB::table('catalogo_condiciones')->truncate();
        DB::table('catalogo_estadosactivo')->truncate();
        DB::table('catalogo_estadosasignacion')->truncate();
        DB::table('catalogo_puestos')->truncate();
        
        // Tablas nuevas o críticas para tu lógica
        DB::table('catalogo_tiposactivo')->truncate(); 
        DB::table('catalogo_marcas')->truncate();
        DB::table('catalogo_tipos_ram')->truncate();
        DB::table('catalogo_tipos_almacenamiento')->truncate();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        // --- 2. TIPOS DE ACTIVO (CRÍTICO: Los nombres activan los campos en el formulario) ---
        // El JS busca: laptop, pc, desktop, cpu, servidor, monitor, pantalla, celular, tablet
        DB::table('catalogo_tiposactivo')->insert([
            ['nombre' => 'Laptop'],           // Activa campos: CPU, RAM, Disco
            ['nombre' => 'Desktop PC'],       // Activa campos: CPU, RAM, Disco
            ['nombre' => 'Servidor'],         // Activa campos: CPU, RAM, Disco
            ['nombre' => 'Monitor'],          // Activa campos: Pulgadas, Conectividad
            ['nombre' => 'Celular'],          // Activa campos: IMEI, Pantalla
            ['nombre' => 'Tablet'],           // Activa campos: IMEI, Pantalla
            ['nombre' => 'Impresora'],        // Genérico
            ['nombre' => 'Proyector'],        // Genérico
            ['nombre' => 'Accesorio'],        // Genérico
            ['nombre' => 'Licencia Software'],// Genérico
            ['nombre' => 'Vehículo'],         // Genérico
        ]);

        // --- 3. Marcas ---
        DB::table('catalogo_marcas')->insert([
            ['nombre' => 'HP'],
            ['nombre' => 'Dell'],
            ['nombre' => 'Lenovo'],
            ['nombre' => 'Apple'],
            ['nombre' => 'Samsung'],
            ['nombre' => 'Logitech'],
            ['nombre' => 'Epson'],
            ['nombre' => 'Cisco'],
            ['nombre' => 'Sin Marca'],
        ]);

        // --- 4. Ubicaciones ---
        DB::table('catalogo_ubicaciones')->insert([
            ['nombre' => 'Oficinas Centrales Villahermosa'],
            ['nombre' => 'Planta Cárdenas'],
            ['nombre' => 'Planta Comalcalco'],
            ['nombre' => 'Estación de Servicio Norte'],
            ['nombre' => 'Almacén Central'],
            ['nombre' => 'Home Office'],
        ]);
        
        // --- 5. Condiciones ---
        DB::table('catalogo_condiciones')->insert([
            ['nombre' => 'Nuevo'],
            ['nombre' => 'Excelente'],
            ['nombre' => 'Bueno'],
            ['nombre' => 'Regular'],
            ['nombre' => 'Malo'],
            ['nombre' => 'Para Piezas'],
        ]);

        // --- 6. Estados del Activo ---
        DB::table('catalogo_estadosactivo')->insert([
            ['nombre' => 'Disponible'],       // ID 1
            ['nombre' => 'En Uso'],           // ID 2
            ['nombre' => 'En Mantenimiento'], // ID 3
            ['nombre' => 'En Diagnóstico'],   // ID 4
            ['nombre' => 'Pendiente de Baja'],// ID 5
            ['nombre' => 'Baja Definitiva'],  // ID 6
        ]);

        // --- 7. Motivos de Baja ---
        DB::table('catalogo_motivosbaja')->insert([
            ['nombre' => 'Dañado Irreparable', 'comentarios_baja' => 'Falla técnica costosa'],
            ['nombre' => 'Obsolescencia Tecnológica', 'comentarios_baja' => 'Equipo muy viejo'],
            ['nombre' => 'Robo', 'comentarios_baja' => 'Con acta del MP'],
            ['nombre' => 'Extravío', 'comentarios_baja' => 'Responsabilidad del usuario'],
            ['nombre' => 'Venta', 'comentarios_baja' => 'Vendido a terceros'],
        ]);

        // --- 8. Tipos de RAM (Nuevo) ---
        DB::table('catalogo_tipos_ram')->insert([
            ['nombre' => 'DDR3'],
            ['nombre' => 'DDR4'],
            ['nombre' => 'DDR5'],
            ['nombre' => 'LPDDR4'],
            ['nombre' => 'SODIMM DDR4'],
        ]);

        // --- 9. Tipos de Almacenamiento (Nuevo) ---
        DB::table('catalogo_tipos_almacenamiento')->insert([
            ['nombre' => 'HDD (Mecánico)'],
            ['nombre' => 'SSD SATA'],
            ['nombre' => 'SSD M.2 NVMe'],
            ['nombre' => 'eMMC'],
        ]);

        // --- 10. Departamentos ---
        DB::table('catalogo_departamentos')->insert([
            ['nombre' => 'Sistemas'],
            ['nombre' => 'Recursos Humanos'],
            ['nombre' => 'Contabilidad'],
            ['nombre' => 'Operaciones'],
            ['nombre' => 'Ventas'],
            ['nombre' => 'Dirección General'],
        ]);
    }
}