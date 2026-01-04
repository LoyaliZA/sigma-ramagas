<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Activo;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activo', function (Blueprint $table) {
            // 1. Creamos la columna permitiendo nulos temporalmente
            $table->string('codigo_interno', 20)->nullable()->after('id');
        });

        // 2. Generamos códigos para los activos que YA existen (Backfilling)
        $activos = Activo::orderBy('created_date')->get();
        $consecutivo = 1;
        
        foreach ($activos as $activo) {
            // Formato: RMA-ACT-001, RMA-ACT-002...
            $activo->codigo_interno = 'RMA-ACT-' . str_pad($consecutivo, 3, '0', STR_PAD_LEFT);
            $activo->save();
            $consecutivo++;
        }

        // 3. Ahora sí, la hacemos única y no nula
        Schema::table('activo', function (Blueprint $table) {
            $table->string('codigo_interno', 20)->nullable(false)->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('activo', function (Blueprint $table) {
            $table->dropColumn('codigo_interno');
        });
    }
};