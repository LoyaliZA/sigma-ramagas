<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empleado_contactos', function (Blueprint $table) {
            $table->id();
            $table->char('empleado_id', 36);
            $table->string('tipo'); // 'Telefono', 'Correo', 'Celular Empresa', etc.
            $table->string('valor'); // El número o el email
            $table->string('descripcion')->nullable(); // Ej: 'Personal', 'Emergencia'
            $table->timestamps();

            $table->foreign('empleado_id')->references('id')->on('empleado')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empleado_contactos');
    }
};