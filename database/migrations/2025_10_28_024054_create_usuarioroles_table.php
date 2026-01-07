<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarioroles', function (Blueprint $table) {
            // Relación con la nueva tabla de usuarios (Breeze)
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            
            // Relación con la tabla de roles
            $table->integer('rol_id');
            $table->foreign('rol_id')->references('id')->on('roles')->onDelete('cascade');

            // Llave primaria compuesta
            $table->primary(['usuario_id', 'rol_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarioroles');
    }
};