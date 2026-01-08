<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpleadoDocumento extends Model
{
    use HasFactory;

    protected $table = 'empleado_documentos';

    protected $fillable = [
        'empleado_id',
        'nombre',          // Nombre legible del archivo
        'ruta_archivo',    // Ruta en el storage
        'tipo_documento',  // Ej: 'INE', 'Contrato', 'Carta Responsiva'
        'subido_por'       // ID del usuario que lo subió
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function subidoPor()
    {
        return $this->belongsTo(User::class, 'subido_por');
    }
}