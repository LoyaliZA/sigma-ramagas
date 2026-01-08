<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asignacion extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'asignacion';
    
    // Tu tabla no usa timestamps estándar (created_at, updated_at)
    public $timestamps = false;

    protected $fillable = [
        'lote_id',
        'fecha_asignacion',
        'fecha_devolucion',
        'observaciones_entrega',
        'carta_responsiva_url',
        'observaciones_devolucion',
        'carta_devolucion_url',
        'activo_id',
        'empleado_id',
        'estado_entrega_id',
        'estado_devolucion_id'
    ];

    // --- CORRECCIÓN PARA QUE LA HORA SE VEA BIEN ---
    protected $casts = [
        'fecha_asignacion' => 'datetime',
        'fecha_devolucion' => 'datetime',
    ];

    // --- RELACIONES CORRECTAS ---
    
    public function activo() {
        return $this->belongsTo(Activo::class, 'activo_id');
    }

    public function empleado() {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    // Aquí respetamos que usas CatalogoEstadoAsignacion
    public function estadoEntrega() {
        return $this->belongsTo(CatalogoEstadoAsignacion::class, 'estado_entrega_id');
    }

    public function estadoDevolucion() {
        return $this->belongsTo(CatalogoEstadoAsignacion::class, 'estado_devolucion_id');
    }
}