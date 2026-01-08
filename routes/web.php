<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\ActivoController;
use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\SeguimientoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ProfileController; // Controlador de perfil de Breeze

Route::get('/', function () {
    return redirect()->route('login');
});

// --- RUTAS PROTEGIDAS (Requieren Login) ---
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil de usuario (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // --- GRUPO 1: SOLO LECTURA (Empleado, Admin, Super Admin) ---
    // Todos pueden ver seguimiento
    Route::get('seguimiento', [SeguimientoController::class, 'index'])->name('seguimiento.index');
    Route::get('seguimiento/{id}', [SeguimientoController::class, 'show'])->name('seguimiento.show');


    // --- GRUPO 2: GESTIÓN INTERMEDIA (Admin y Super Admin) ---
    // Empleado NO entra aquí
    Route::middleware(['role:Admin,Super Admin'])->group(function () {
        
        // Reportes
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/inventario', [ReporteController::class, 'generarInventario'])->name('reportes.inventario');
        Route::get('/reportes/bajas', [ReporteController::class, 'generarBajas'])->name('reportes.bajas');
        Route::get('/reportes/inventario-csv', [ReporteController::class, 'generarInventarioCSV'])->name('reportes.inventario_csv');

        // Asignaciones
        Route::get('asignaciones', [AsignacionController::class, 'index'])->name('asignaciones.index');
        Route::post('asignaciones', [AsignacionController::class, 'store'])->name('asignaciones.store');
        Route::post('asignaciones/{id}/devolver', [AsignacionController::class, 'devolver'])->name('asignaciones.devolver');
        Route::get('asignaciones/{id}/carta-responsiva', [AsignacionController::class, 'imprimirCarta'])->name('asignaciones.carta');
        Route::get('asignaciones/{id}/carta-devolucion', [AsignacionController::class, 'imprimirCartaDevolucion'])->name('asignaciones.carta_devolucion');
        Route::get('/asignaciones/carta-lote/{loteId}', [AsignacionController::class, 'imprimirCartaPorLote'])->name('asignaciones.carta_lote');
        Route::post('/asignaciones/subir-documento', [AsignacionController::class, 'subirDocumento'])->name('asignaciones.subir_documento');
        Route::get('/asignaciones/{id}/historial-documentos', [AsignacionController::class, 'obtenerHistorial']);

        // Almacén
        Route::get('almacen', [AlmacenController::class, 'index'])->name('almacen.index');
        Route::post('almacen/{id}/cambiar-estado', [AlmacenController::class, 'cambiarEstado'])->name('almacen.cambiar_estado');

        // Empleados y Activos (Solo métodos de lectura/creación, no eliminar si eres Admin)
        // Nota: Si Admin NO debe editar, habría que restringir 'edit' y 'update' también.
        // Por simplicidad, aquí dejamos el resource, pero protegemos la ruta 'destroy' abajo.
        Route::resource('empleados', EmpleadoController::class)->except(['destroy']);
        
        
        Route::get('empleados/{id}/historial-pdf', [EmpleadoController::class, 'generarHistorialPdf'])->name('empleados.historial_pdf');
        Route::get('/activos/bajas', [ActivoController::class, 'bajas'])->name('activos.bajas');
        Route::post('activos/quick-add-catalogo', [ActivoController::class, 'storeCatalogo'])->name('activos.quick_add');
        Route::resource('activos', ActivoController::class)->except(['destroy', 'darBaja']);
    });


    // --- GRUPO 3: SUPER ADMIN (Poder Absoluto) ---
    Route::middleware(['role:Super Admin'])->group(function () {
        
        // Acciones destructivas o críticas
        Route::delete('empleados/{empleado}', [EmpleadoController::class, 'destroy'])->name('empleados.destroy');
        Route::delete('activos/{activo}', [ActivoController::class, 'destroy'])->name('activos.destroy');
        
        // Dar de baja un activo (Considerado crítico)
        Route::post('/activos/{id}/baja', [ActivoController::class, 'darBaja'])->name('activos.dar_baja');
    });

});

require __DIR__.'/auth.php'; // Rutas generadas por Breeze