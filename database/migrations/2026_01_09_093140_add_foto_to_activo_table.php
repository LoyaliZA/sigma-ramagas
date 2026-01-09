<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activo', function (Blueprint $table) {
            // Agregamos la columna 'foto' (nullable, porque no todos tendrán foto)
            // La ponemos después de 'costo' para mantener orden
            $table->string('foto', 255)->nullable()->after('costo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activo', function (Blueprint $table) {
            $table->dropColumn('foto');
        });
    }
};