<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpleadoContacto extends Model
{
    use HasFactory;

    protected $table = 'empleado_contactos';

    protected $fillable = [
        'empleado_id',
        'tipo',        // Ej: 'Telefono', 'Email Personal', 'Celular Trabajo'
        'valor',       // Ej: '9933123456', 'juan@gmail.com'
        'descripcion'  // Ej: 'Solo whatsapp', 'Contacto de emergencia'
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }
}