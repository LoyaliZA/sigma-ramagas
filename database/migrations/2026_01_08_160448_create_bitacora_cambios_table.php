<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bitacora_cambios', function (Blueprint $table) {
            $table->id();
            $table->string('accion'); // Crear, Actualizar, Eliminar
            $table->string('tabla'); // Nombre de la tabla afectada
            $table->string('registro_id'); // ID del registro afectado (String para soportar UUID)
            $table->json('valores_anteriores')->nullable();
            $table->json('valores_nuevos')->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); // Quién hizo el cambio
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            // Relación con usuarios (opcional, si se borra el usuario queda el log)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacora_cambios');
    }
};