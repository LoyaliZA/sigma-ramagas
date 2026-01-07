<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignacion_documento_historial', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Relación con la asignación (FK)
            $table->char('asignacion_id', 36);
            $table->foreign('asignacion_id')->references('id')->on('asignacion')->onDelete('cascade');

            // Guardamos lote_id también para agrupar consultas rápidas
            $table->uuid('lote_id')->nullable()->index();

            $table->string('url_archivo', 500);
            $table->string('nombre_archivo_original');
            
            // ID del usuario que subió el archivo (asumiendo que uses auth)
            $table->uuid('subido_por_id')->nullable(); 

            // Fecha de subida automática
            $table->timestamp('fecha_subida')->useCurrent();
            
            $table->text('comentarios')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignacion_documento_historial');
    }
};