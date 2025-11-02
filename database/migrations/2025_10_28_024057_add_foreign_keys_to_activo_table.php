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
        Schema::table('activo', function (Blueprint $table) {
            $table->foreign(['tipo_id'], 'activo_ibfk_1')->references(['id'])->on('catalogo_tiposactivo')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['marca_id'], 'activo_ibfk_2')->references(['id'])->on('catalogo_marcas')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['estado_id'], 'activo_ibfk_3')->references(['id'])->on('catalogo_estadosactivo')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['ubicacion_id'], 'activo_ibfk_4')->references(['id'])->on('catalogo_ubicaciones')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['condicion_id'], 'fk_activo_condicion')->references(['id'])->on('catalogo_condiciones')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['motivo_baja_id'], 'fk_motivo_baja')->references(['id'])->on('catalogo_motivosbaja')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activo', function (Blueprint $table) {
            $table->dropForeign('activo_ibfk_1');
            $table->dropForeign('activo_ibfk_2');
            $table->dropForeign('activo_ibfk_3');
            $table->dropForeign('activo_ibfk_4');
            $table->dropForeign('fk_activo_condicion');
            $table->dropForeign('fk_motivo_baja');
        });
    }
};
