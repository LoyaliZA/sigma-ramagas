<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'empleado';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = [
        'numero_empleado',
        'nombre',
        'apellido_paterno', // <-- CAMBIO
        'apellido_materno', // <-- CAMBIO
        'correo',
        'estatus',
        'fecha_ingreso',
        'departamento_id',
        'planta_id',
        'puesto_id', // <-- CAMBIO
    ];

    /**
     * Obtiene el nombre completo del empleado.
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}";
    }

    // --- RELACIONES ---

    public function departamento()
    {
        return $this->belongsTo(CatalogoDepartamento::class, 'departamento_id');
    }

    public function ubicacion()
    {
        return $this->belongsTo(CatalogoUbicacion::class, 'planta_id');
    }

    public function puesto()
    {
        return $this->belongsTo(CatalogoPuesto::class, 'puesto_id'); // <-- NUEVA RELACIÓN
    }
}
