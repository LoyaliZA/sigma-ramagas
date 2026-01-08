<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activo extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'activo';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = [
        'numero_serie', 'codigo_interno', 'modelo', 'especificaciones', 'costo', 
        'fecha_adquisicion', 'garantia_hasta', 'observaciones',
        'tipo_id', 'marca_id', 'estado_id', 'ubicacion_id', 
        'condicion_id', 'motivo_baja_id', 'fecha_baja', 'user_id'
    ];

    protected $casts = [
        'especificaciones' => 'array',
        'fecha_adquisicion' => 'date',
        'garantia_hasta' => 'date',
        'fecha_baja' => 'datetime'
    ];

    // Relaciones Simples
    public function tipo() { return $this->belongsTo(CatalogoTipoActivo::class, 'tipo_id'); }
    public function marca() { return $this->belongsTo(CatalogoMarca::class, 'marca_id'); }
    public function estado() { return $this->belongsTo(CatalogoEstadoActivo::class, 'estado_id'); }
    public function ubicacion() { return $this->belongsTo(CatalogoUbicacion::class, 'ubicacion_id'); }
    public function condicion() { return $this->belongsTo(CatalogoCondicion::class, 'condicion_id'); }
    public function motivoBaja() { return $this->belongsTo(CatalogoMotivoBaja::class, 'motivo_baja_id'); }

    // --- LA RELACIÓN QUE FALTABA (CORRECCIÓN) ---
    // Busca al empleado actual a través de la asignación activa (sin fecha de devolución)
    public function empleado() {
        return $this->hasOneThrough(
            Empleado::class,
            Asignacion::class,
            'activo_id', 'id', 'id', 'empleado_id'
        )->whereNull('asignacion.fecha_devolucion');
    }
}