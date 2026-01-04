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
        'apellido_paterno',
        'apellido_materno',
        'correo',
        'estatus',
        'fecha_ingreso',
        'fecha_baja',      // <-- Nuevo
        'motivo_baja',     // <-- Nuevo
        'foto_url',        // <-- Nuevo
        'departamento_id',
        'planta_id',
        'puesto_id',
    ];

    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}";
    }

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
        return $this->belongsTo(CatalogoPuesto::class, 'puesto_id');
    }

    // Relación para ver activos asignados (Historial completo)
    public function asignaciones()
    {
        return $this->hasMany(Asignacion::class, 'empleado_id')->orderBy('fecha_asignacion', 'desc');
    }
    
    // Relación auxiliar para ver SOLO los activos actuales (no devueltos)
    public function asignacionesActivas()
    {
        return $this->hasMany(Asignacion::class, 'empleado_id')->whereNull('fecha_devolucion');
    }
}