<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalogo_tipos_ram', function (Blueprint $table) {
            $table->id(); // Esto crea el campo 'id' autoincremental
            $table->string('nombre', 50)->unique();
            // Nota: Tu respaldo no mostraba timestamps (created_at), así que no los pongo
            // para ser fiel a tu estructura original.
        });

        // Insertar los datos que tenías en el respaldo para no perderlos
        DB::table('catalogo_tipos_ram')->insert([
            ['nombre' => 'DDR3'],
            ['nombre' => 'DDR4'],
            ['nombre' => 'DDR5'],
            ['nombre' => 'ECC'],
            ['nombre' => 'LPDDR4'],
            ['nombre' => 'LPDDR5'],
            ['nombre' => 'SO-DIMM DDR4'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('catalogo_tipos_ram');
    }
};