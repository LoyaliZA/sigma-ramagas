<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empleado', function (Blueprint $table) {
            $table->string('foto_url')->nullable()->after('correo'); // Ruta de la foto
            $table->date('fecha_baja')->nullable()->after('estatus');
            $table->string('motivo_baja')->nullable()->after('fecha_baja');
        });
    }

    public function down(): void
    {
        Schema::table('empleado', function (Blueprint $table) {
            $table->dropColumn(['foto_url', 'fecha_baja', 'motivo_baja']);
        });
    }
};