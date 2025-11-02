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
        Schema::table('asignacion', function (Blueprint $table) {
            $table->foreign(['activo_id'], 'asignacion_ibfk_1')->references(['id'])->on('activo')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['empleado_id'], 'asignacion_ibfk_2')->references(['id'])->on('empleado')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['estado_devolucion_id'], 'fk_asignacion_estado_devolucion')->references(['id'])->on('catalogo_estadosasignacion')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['estado_entrega_id'], 'fk_asignacion_estado_entrega')->references(['id'])->on('catalogo_estadosasignacion')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asignacion', function (Blueprint $table) {
            $table->dropForeign('asignacion_ibfk_1');
            $table->dropForeign('asignacion_ibfk_2');
            $table->dropForeign('fk_asignacion_estado_devolucion');
            $table->dropForeign('fk_asignacion_estado_entrega');
        });
    }
};
