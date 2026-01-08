<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Convertimos las columnas a DATETIME para que guarden la hora
        DB::statement("ALTER TABLE asignacion MODIFY fecha_asignacion DATETIME NULL");
        DB::statement("ALTER TABLE asignacion MODIFY fecha_devolucion DATETIME NULL");
    }

    public function down(): void
    {
        // Revertir a DATE si fuera necesario
        DB::statement("ALTER TABLE asignacion MODIFY fecha_asignacion DATE NULL");
        DB::statement("ALTER TABLE asignacion MODIFY fecha_devolucion DATE NULL");
    }
};