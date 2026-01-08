<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BitacoraCambio extends Model
{
    use HasFactory;

    protected $table = 'bitacora_cambios';

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

    // Convertimos automáticamente el JSON a Array de PHP
    protected $casts = [
        'valores_anteriores' => 'array',
        'valores_nuevos' => 'array',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}