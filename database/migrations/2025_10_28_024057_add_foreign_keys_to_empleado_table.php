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
        Schema::table('empleado', function (Blueprint $table) {
            $table->foreign(['departamento_id'], 'empleado_ibfk_1')->references(['id'])->on('catalogo_departamentos')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['planta_id'], 'empleado_ibfk_2')->references(['id'])->on('catalogo_ubicaciones')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleado', function (Blueprint $table) {
            $table->dropForeign('empleado_ibfk_1');
            $table->dropForeign('empleado_ibfk_2');
        });
    }
};
