<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class Activo extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'activo';

    // Configuramos los nombres reales de las columnas de fecha de tu BD
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $casts = [
        'especificaciones' => 'array',
        'fecha_adquisicion' => 'date',
        'garantia_hasta' => 'date',
        'fecha_baja' => 'datetime',
        'costo' => 'decimal:2',
        'created_date' => 'datetime',
        'updated_date' => 'datetime',
    ];

    protected $fillable = [
        'numero_serie',
        'codigo_interno',
        'modelo',
        'especificaciones',
        'costo',
        'foto', // CAMBIO: En tu BD se llama 'foto', no 'imagen'
        'fecha_adquisicion',
        'garantia_hasta',
        'observaciones',
        'tipo_id',
        'marca_id',
        'estado_id',
        'ubicacion_id',
        'condicion_id',
        'motivo_baja_id',
        'fecha_baja'
        // ELIMINADO: 'user_id' no existe en tu tabla activo
    ];

    // Evento para generar Código Interno si no viene
    protected static function booted()
    {
        static::creating(function ($activo) {
            if (empty($activo->codigo_interno)) {
                // Genera un código tipo: ACT-Ymd-Aleatorio (Ej: ACT-20260108-A1B2)
                $activo->codigo_interno = 'ACT-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
            }
        });
    }

    // Relaciones
    public function tipo() { return $this->belongsTo(CatalogoTipoActivo::class, 'tipo_id'); }
    public function marca() { return $this->belongsTo(CatalogoMarca::class, 'marca_id'); }
    public function estado() { return $this->belongsTo(CatalogoEstadoActivo::class, 'estado_id'); }
    public function ubicacion() { return $this->belongsTo(CatalogoUbicacion::class, 'ubicacion_id'); }
    public function condicion() { return $this->belongsTo(CatalogoCondicion::class, 'condicion_id'); }
    public function motivoBaja() { return $this->belongsTo(CatalogoMotivoBaja::class, 'motivo_baja_id'); }
    
    // Relación con empleado a través de asignaciones activas
    public function empleado()
    {
        return $this->hasOneThrough(
            Empleado::class,
            Asignacion::class,
            'activo_id', // FK en asignacion
            'id', // PK en empleado
            'id', // PK en activo
            'empleado_id' // FK en asignacion
        )->whereNull('asignacion.fecha_devolucion'); // Solo asignación actual
    }
}