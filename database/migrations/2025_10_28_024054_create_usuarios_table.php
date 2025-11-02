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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('nombre_usuario', 50)->unique('nombre_usuario_unique');
            $table->string('correo')->unique('correo_unique');
            $table->string('contrasena_hash');
            $table->string('nombre_completo', 150)->nullable();
            $table->boolean('activo')->default(true);
            $table->dateTime('ultimo_login')->nullable();
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('updated_date')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
