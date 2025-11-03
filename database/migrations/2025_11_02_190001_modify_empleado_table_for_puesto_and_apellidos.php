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
        Schema::table('empleado', function (Blueprint $table) {
            // 1. Eliminar la columna 'apellidos'
            $table->dropColumn('apellidos');
            
            // 2. Eliminar la columna 'puesto'
            $table->dropColumn('puesto');
            
            // 3. Agregar las nuevas columnas de apellidos (después de 'nombre')
            $table->string('apellido_paterno', 100)->after('nombre');
            $table->string('apellido_materno', 100)->nullable()->after('apellido_paterno');
            
            // 4. Agregar la nueva llave foránea para 'puesto_id' (después de 'departamento_id')
            $table->unsignedBigInteger('puesto_id')->nullable()->after('departamento_id');

            // 5. Definir la llave foránea
            $table->foreign('puesto_id')
                  ->references('id')
                  ->on('catalogo_puestos')
                  ->onDelete('set null'); // Si se borra un puesto, el empleado no se borra
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleado', function (Blueprint $table) {
            // Revertir los cambios en orden inverso
            $table->dropForeign(['puesto_id']);
            $table->dropColumn('puesto_id');
            $table->dropColumn('apellido_materno');
            $table->dropColumn('apellido_paterno');

            $table->string('apellidos', 100)->after('nombre');
            $table->string('puesto', 100)->nullable()->after('apellidos');
        });
    }
};
