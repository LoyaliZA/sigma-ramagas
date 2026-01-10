<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BitacoraCambio extends Model
{
    use HasFactory;

    protected $table = 'bitacora_cambios';

    // Deshabilitamos updated_at ya que una bitácora es histórica y no se edita
    public $timestamps = false;

    // Solo habilitamos created_at para saber cuándo ocurrió
    const CREATED_AT = 'created_at';

    protected $fillable = [
        'accion',
        'tabla',
        'registro_id',
        'valores_anteriores',
        'valores_nuevos',
        'user_id',
        'ip_address',
        'user_agent'
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     * Aquí solucionamos el error del format() y el manejo de JSON.
     */
    protected $casts = [
        'valores_anteriores' => 'array',
        'valores_nuevos' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Relación con el usuario que hizo la acción
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}