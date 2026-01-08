<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empleado_documentos', function (Blueprint $table) {
            $table->id();
            $table->char('empleado_id', 36);
            $table->string('nombre'); // Nombre legible del documento (ej. INE)
            $table->string('ruta_archivo'); // Ruta en storage
            $table->string('tipo_documento')->nullable(); // Para clasificar si es necesario
            $table->unsignedBigInteger('subido_por')->nullable(); // ID del usuario admin que lo subió
            $table->timestamps();

            $table->foreign('empleado_id')->references('id')->on('empleado')->onDelete('cascade');
            $table->foreign('subido_por')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empleado_documentos');
    }
};