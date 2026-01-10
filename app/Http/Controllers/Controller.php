<?php

namespace App\Http\Controllers;

// Agregamos el modelo de Bitácora
use App\Models\BitacoraCambio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

abstract class Controller
{
    /**
     * Función global para registrar cambios en la bitácora
     * Disponible en todos los controladores que extiendan de Controller
     */
    protected function logAction($accion, $tabla, $registroId, $anterior, $nuevo)
    {
        // Solo registramos si hay un usuario autenticado (por seguridad)
        if (Auth::check()) {
            BitacoraCambio::create([
                'accion' => $accion,                // Ej: 'Creación', 'Edición', 'Eliminación'
                'tabla' => $tabla,                  // Ej: 'activos', 'empleados'
                'registro_id' => (string) $registroId, // Convertimos a string por si es UUID
                'valores_anteriores' => $anterior,  // Array o null
                'valores_nuevos' => $nuevo,         // Array o null
                'user_id' => Auth::id(),
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        }
    }
}