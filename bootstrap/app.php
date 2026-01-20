<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Aquí capturamos el error cuando el middleware 'role' rechaza el acceso
        $exceptions->render(function (HttpException $e, Request $request) {
            
            // Verificamos si el error es un 403 (Prohibido)
            if ($e->getStatusCode() === 403) {

                // CASO 1: Petición AJAX / Axios (Ej: Intentar eliminar algo sin permiso)
                // Devolvemos JSON para que tu JS muestre el SweetAlert de error sin recargar
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'ACCESO DENEGADO: No tienes permisos suficientes para realizar esta acción.'
                    ], 403);
                }

                // CASO 2: Navegación Normal (Ej: Intentar entrar a /seguimiento por URL)
                // Redirigimos al dashboard con el mensaje flash para el SweetAlert Visual
                return redirect()->route('dashboard')
                    ->with('error_permisos', 'No tienes autorización para acceder a esa vista.');
            }
        });
    })->create();