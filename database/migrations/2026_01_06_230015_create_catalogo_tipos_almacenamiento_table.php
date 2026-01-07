<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalogo_tipos_almacenamiento', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->unique();
        });

        // Insertamos los datos recuperados de tu respaldo
        DB::table('catalogo_tipos_almacenamiento')->insert([
            ['nombre' => 'HDD'],
            ['nombre' => 'SSD SATA'],
            ['nombre' => 'SSD M.2'],
            ['nombre' => 'SSD NVMe'],
            ['nombre' => 'SAS'],
            ['nombre' => 'MicroSD'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('catalogo_tipos_almacenamiento');
    }
};