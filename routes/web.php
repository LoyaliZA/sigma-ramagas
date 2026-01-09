<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\ActivoController;
use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\SeguimientoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ProfileController;

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


    // --- GRUPO 1: SOLO LECTURA ---
    Route::get('seguimiento', [SeguimientoController::class, 'index'])->name('seguimiento.index');
    Route::get('seguimiento/{id}', [SeguimientoController::class, 'show'])->name('seguimiento.show');


    // --- GRUPO 2: GESTIÓN INTERMEDIA ---
    Route::middleware(['role:Admin|Super Admin'])->group(function () {

        // --- ASIGNACIONES ---
        Route::post('asignaciones/subir-documento', [AsignacionController::class, 'subirDocumento'])->name('asignaciones.subir_documento');
        Route::get('asignaciones/{id}/historial-documentos', [AsignacionController::class, 'obtenerHistorial'])->name('asignaciones.historial_documentos');
        Route::post('asignaciones/{id}/devolver', [AsignacionController::class, 'devolver'])->name('asignaciones.devolver');

        Route::get('asignaciones/carta/{id}', [AsignacionController::class, 'imprimirCarta'])->name('asignaciones.carta');
        Route::get('asignaciones/carta-lote/{loteId}', [AsignacionController::class, 'imprimirCartaPorLote'])->name('asignaciones.carta_lote');
        Route::get('asignaciones/carta-devolucion/{id}', [AsignacionController::class, 'imprimirCartaDevolucion'])->name('asignaciones.carta_devolucion');

        Route::resource('asignaciones', AsignacionController::class);

        // --- ALMACÉN ---
        Route::post('almacen/{id}/cambiar-estado', [AlmacenController::class, 'cambiarEstado'])->name('almacen.cambiar_estado');
        Route::post('almacen/{id}/confirmar-baja', [AlmacenController::class, 'confirmarBajaDefinitiva'])->name('almacen.confirmar_baja');
        Route::resource('almacen', AlmacenController::class)->only(['index']);
        
        // --- EMPLEADOS ---
        Route::post('/empleados/{id}/documentos', [EmpleadoController::class, 'subirDocumento'])->name('empleados.documentos.store');
        Route::delete('/empleados/documentos/{id}', [EmpleadoController::class, 'eliminarDocumento'])->name('empleados.documentos.destroy');
        Route::get('empleados/{id}/historial-pdf', [EmpleadoController::class, 'generarHistorialPdf'])->name('empleados.historial_pdf');
        Route::resource('empleados', EmpleadoController::class)->except(['destroy']);
        
        // --- ACTIVOS ---
        Route::get('/activos/bajas', [ActivoController::class, 'bajas'])->name('activos.bajas');
        Route::post('activos/quick-add-catalogo', [ActivoController::class, 'storeCatalogo'])->name('activos.quick_add');
        Route::resource('activos', ActivoController::class)->except(['destroy', 'darBaja']);
    });


    // --- GRUPO 3: SUPER ADMIN ---
    Route::middleware(['role:Super Admin'])->group(function () {
        Route::delete('empleados/{empleado}', [EmpleadoController::class, 'destroy'])->name('empleados.destroy');
        Route::delete('activos/{activo}', [ActivoController::class, 'destroy'])->name('activos.destroy');
        Route::post('/activos/{id}/baja', [ActivoController::class, 'darBaja'])->name('activos.baja');
    });

    // --- REPORTES (Admin y Super Admin) ---
    // CORRECCIONES APLICADAS AQUÍ:
    Route::middleware(['role:Admin|Super Admin'])->group(function () {
        
        Route::get('/reportes', [ReporteController::class, 'index'])
            ->name('reportes.index');
        
        // 1. PDF Inventario: Apunta a 'generarInventario' y se llama 'reportes.inventario'
        Route::get('/reportes/inventario-pdf', [ReporteController::class, 'generarInventario'])
            ->name('reportes.inventario');
        
        // 2. PDF Bajas: Apunta a 'bajasPdf' y se llama 'reportes.bajas'
        Route::get('/reportes/bajas-pdf', [ReporteController::class, 'bajasPdf'])
            ->name('reportes.bajas_pdf');
            
        // 3. Excel/CSV: Apunta a 'generarInventarioCSV' y se llama 'reportes.inventario_csv'
        Route::get('/reportes/exportar-excel', [ReporteController::class, 'generarInventarioCSV'])
            ->name('reportes.inventario_csv');
    });

});

require __DIR__.'/auth.php';