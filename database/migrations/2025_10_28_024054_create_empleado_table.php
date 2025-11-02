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
        Schema::create('empleado', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('numero_empleado', 50)->unique('numero_empleado_unique');
            $table->string('nombre', 100);
            $table->string('apellidos', 100);
            $table->string('puesto', 100)->nullable();
            $table->string('correo')->nullable()->unique('correo_unique');
            $table->string('estatus', 20)->default('Activo');
            $table->date('fecha_ingreso')->nullable();
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('updated_date')->useCurrentOnUpdate()->useCurrent();
            $table->integer('departamento_id')->index('departamento_id');
            $table->integer('planta_id')->index('planta_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleado');
    }
};
