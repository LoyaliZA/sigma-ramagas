<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'empleado';
    
    // Nombres personalizados de timestamps definidos en tu BD
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    // Esto le dice a Laravel: "Cuando conviertas a JSON, incluye la función getNombreCompletoAttribute"
    protected $appends = ['nombre_completo']; 

    protected $fillable = [
        'numero_empleado',
        'codigo_empresa',   // <--- [NUEVO] Agregado para Fase 1
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'correo',
        'estatus',
        'fecha_ingreso',
        'fecha_baja',       
        'motivo_baja',      
        'foto_url',         
        'departamento_id',
        'planta_id',
        'puesto_id',
    ];

    /**
     * Accessor para obtener el nombre completo concatenado.
     * Uso: $empleado->nombre_completo
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
        return $this->belongsTo(CatalogoPuesto::class, 'puesto_id');
    }

    // --- NUEVAS RELACIONES AGREGADAS (Fase 2) ---

    /**
     * Obtener los contactos extra del empleado (Teléfonos, Correos alternos).
     */
    public function contactos()
    {
        return $this->hasMany(EmpleadoContacto::class, 'empleado_id');
    }

    /**
     * Obtener los documentos del expediente digital.
     */
    public function documentos()
    {
        return $this->hasMany(EmpleadoDocumento::class, 'empleado_id');
    }

    // ---------------------------------------------

    /**
     * Historial completo de asignaciones
     */
    public function asignaciones()
    {
        return $this->hasMany(Asignacion::class, 'empleado_id')->orderBy('fecha_asignacion', 'desc');
    }

    /**
     * Solo activos que tienen actualmente (sin fecha de devolución)
     */
    public function asignacionesActivas()
    {
        return $this->hasMany(Asignacion::class, 'empleado_id')->whereNull('fecha_devolucion');
    }
}