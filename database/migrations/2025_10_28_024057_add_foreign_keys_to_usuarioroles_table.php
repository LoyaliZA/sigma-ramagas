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
        Schema::table('usuarioroles', function (Blueprint $table) {
            $table->foreign(['usuario_id'], 'usuarioroles_ibfk_1')->references(['id'])->on('usuarios')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['rol_id'], 'usuarioroles_ibfk_2')->references(['id'])->on('roles')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarioroles', function (Blueprint $table) {
            $table->dropForeign('usuarioroles_ibfk_1');
            $table->dropForeign('usuarioroles_ibfk_2');
        });
    }
};
