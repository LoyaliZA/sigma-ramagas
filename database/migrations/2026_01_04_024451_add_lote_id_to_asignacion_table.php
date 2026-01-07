<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asignacion', function (Blueprint $table) {
            // Agregamos el campo lote_id después del id
            // Lo indexamos porque buscaremos mucho por este campo
            $table->uuid('lote_id')->nullable()->after('id')->index(); 
        });
    }

    public function down(): void
    {
        Schema::table('asignacion', function (Blueprint $table) {
            $table->dropColumn('lote_id');
        });
    }
};