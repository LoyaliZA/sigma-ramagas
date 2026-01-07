<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids; // Asegúrate de tener esto si usas UUIDs
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asignacion extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'asignacion';
    public $timestamps = false;

    protected $fillable = [
        'lote_id', // <--- NUEVO
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

    protected $casts = [
        'fecha_asignacion' => 'datetime',
        'fecha_devolucion' => 'datetime'
    ];

    // Relaciones existentes...
    public function activo() { return $this->belongsTo(Activo::class, 'activo_id'); }
    public function empleado() { return $this->belongsTo(Empleado::class, 'empleado_id'); }
    public function estadoEntrega() { return $this->belongsTo(CatalogoEstadoAsignacion::class, 'estado_entrega_id'); }
    public function estadoDevolucion() { return $this->belongsTo(CatalogoEstadoAsignacion::class, 'estado_devolucion_id'); }

    // Relación con Historial de Documentos
    public function historialDocumentos()
    {
        return $this->hasMany(AsignacionDocumentoHistorial::class, 'lote_id', 'lote_id'); 
        // Nota: Relacionamos por lote para ver el historial de todo el grupo
    }
}