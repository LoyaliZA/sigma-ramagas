<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 0. Si no está logueado, al login
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        // 1. El Super Admin siempre pasa (Acceso total)
        // Esto evita tener que poner "Super Admin" en cada ruta explícitamente si no quieres
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // 2. Recorrer los roles definidos en la ruta
        // El operador ...$roles recibe los parámetros. 
        // Ejemplo en web.php: middleware('role:Admin|Super Admin') -> $roles llega como ["Admin|Super Admin"]
        foreach ($roles as $roleGroup) {
            
            // Separamos por '|' para soportar múltiples roles en un solo parámetro (ej: "Admin|Empleado")
            $posiblesRoles = explode('|', $roleGroup);

            foreach ($posiblesRoles as $rol) {
                if ($user->hasRole($rol)) {
                    return $next($request);
                }
            }
        }

        // 3. Si terminó el ciclo y no encontró coincidencia, error 403
        return redirect()->back()->with('error', '⛔ No tienes permisos para realizar esta acción.');
    }
}