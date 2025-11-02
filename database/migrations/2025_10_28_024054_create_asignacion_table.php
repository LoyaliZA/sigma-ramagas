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
        Schema::create('asignacion', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->dateTime('fecha_asignacion');
            $table->dateTime('fecha_devolucion')->nullable();
            $table->text('observaciones_entrega')->nullable();
            $table->string('carta_responsiva_url', 500)->nullable();
            $table->text('observaciones_devolucion')->nullable();
            $table->string('carta_devolucion_url', 500)->nullable();
            $table->char('activo_id', 36)->index('activo_id');
            $table->char('empleado_id', 36)->index('empleado_id');
            $table->integer('estado_entrega_id')->nullable()->index('fk_asignacion_estado_entrega');
            $table->integer('estado_devolucion_id')->nullable()->index('fk_asignacion_estado_devolucion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacion');
    }
};
