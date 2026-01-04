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
        'numero_serie', 'modelo', 'especificaciones', 'costo', 
        'fecha_adquisicion', 'garantia_hasta', 'observaciones',
        'tipo_id', 'marca_id', 'estado_id', 'ubicacion_id', 
        'condicion_id', 'motivo_baja_id'
    ];

    protected $casts = [
        'especificaciones' => 'array',
    ];

    // --- RELACIONES ---
    public function tipo() {
        return $this->belongsTo(CatalogoTipoActivo::class, 'tipo_id');
    }

    public function marca() {
        return $this->belongsTo(CatalogoMarca::class, 'marca_id');
    }

    public function estado() {
        return $this->belongsTo(CatalogoEstadoActivo::class, 'estado_id');
    }

    public function ubicacion() {
        return $this->belongsTo(CatalogoUbicacion::class, 'ubicacion_id');
    }
    
    public function motivoBaja() {
        return $this->belongsTo(CatalogoMotivoBaja::class, 'motivo_baja_id');
    }

    // --- AGREGA ESTA FUNCIÓN QUE FALTABA ---
    public function condicion() {
        return $this->belongsTo(CatalogoCondicion::class, 'condicion_id');
    }

    // MAGIA: Generación automática del código
    protected static function booted()
    {
        static::creating(function ($activo) {
            // Buscamos el último código generado
            $ultimo = Activo::whereNotNull('codigo_interno')
                ->orderByRaw('LENGTH(codigo_interno) DESC') // Para ordenar bien 9, 10, 11...
                ->orderBy('codigo_interno', 'desc')
                ->first();

            if (!$ultimo) {
                $numero = 1;
            } else {
                // Extraemos el número del string "RMA-ACT-005" -> 5
                $partes = explode('-', $ultimo->codigo_interno);
                $numero = intval(end($partes)) + 1;
            }

            // Asignamos el nuevo código: RMA-ACT-006
            $activo->codigo_interno = 'RMA-ACT-' . str_pad($numero, 3, '0', STR_PAD_LEFT);
        });
    }
}