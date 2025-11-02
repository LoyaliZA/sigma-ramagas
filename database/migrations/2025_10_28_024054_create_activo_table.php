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
        Schema::create('activo', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('numero_serie', 100)->unique('numero_serie_unique');
            $table->string('modelo', 100)->nullable();
            $table->json('especificaciones')->nullable();
            $table->decimal('costo', 10)->nullable();
            $table->date('fecha_adquisicion')->nullable();
            $table->date('garantia_hasta')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('updated_date')->useCurrentOnUpdate()->useCurrent();
            $table->integer('tipo_id')->index('tipo_id');
            $table->integer('marca_id')->index('marca_id');
            $table->integer('estado_id')->index('estado_id');
            $table->integer('ubicacion_id')->index('ubicacion_id');
            $table->integer('condicion_id')->index('fk_activo_condicion');
            $table->integer('motivo_baja_id')->nullable()->index('fk_motivo_baja');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activo');
    }
};
