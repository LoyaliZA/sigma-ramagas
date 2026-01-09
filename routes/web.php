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


    // --- GRUPO 1: SOLO LECTURA (Empleado, Admin, Super Admin) ---
    // Todos pueden ver seguimiento
    Route::get('seguimiento', [SeguimientoController::class, 'index'])->name('seguimiento.index');
    Route::get('seguimiento/{id}', [SeguimientoController::class, 'show'])->name('seguimiento.show');


    // --- GRUPO 2: GESTIÓN INTERMEDIA (Admin y Super Admin) ---
    // Empleado NO entra aquí
    Route::middleware(['role:Admin|Super Admin'])->group(function () {

        // ==========================================
        //         MÓDULO DE ASIGNACIONES (CORREGIDO)
        // ==========================================
        
        // 1. Acciones AJAX y Formularios (Lo que faltaba o daba error)
        Route::post('asignaciones/subir-documento', [AsignacionController::class, 'subirDocumento'])->name('asignaciones.subir_documento');
        Route::get('asignaciones/{id}/historial-documentos', [AsignacionController::class, 'obtenerHistorial'])->name('asignaciones.historial_documentos');
        Route::post('asignaciones/{id}/devolver', [AsignacionController::class, 'devolver'])->name('asignaciones.devolver');

        // 2. Generación de PDFs (Nombres alineados con el Controlador y la Vista)
        Route::get('asignaciones/carta/{id}', [AsignacionController::class, 'imprimirCarta'])->name('asignaciones.carta');
        Route::get('asignaciones/carta-lote/{loteId}', [AsignacionController::class, 'imprimirCartaPorLote'])->name('asignaciones.carta_lote');
        Route::get('asignaciones/carta-devolucion/{id}', [AsignacionController::class, 'imprimirCartaDevolucion'])->name('asignaciones.carta_devolucion'); // Corregido nombre de ruta

        // 3. Resource Principal (CRUD básico)
        Route::resource('asignaciones', AsignacionController::class);


        // --- ALMACÉN ---
        Route::resource('almacen', AlmacenController::class)->only(['index', 'store']);
        Route::post('almacen/cambiar-estado', [AlmacenController::class, 'cambiarEstado'])->name('almacen.cambiar_estado');

        // --- EMPLEADOS ---
        // Rutas para Expediente Digital (Documentos SIGMA)
        Route::post('/empleados/{id}/documentos', [EmpleadoController::class, 'subirDocumento'])->name('empleados.documentos.store');
        Route::delete('/empleados/documentos/{id}', [EmpleadoController::class, 'eliminarDocumento'])->name('empleados.documentos.destroy');
        
        // Historial PDF
        Route::get('empleados/{id}/historial-pdf', [EmpleadoController::class, 'generarHistorialPdf'])->name('empleados.historial_pdf');
        
        // Resource principal (Excluyendo destroy que es solo para Super Admin)
        Route::resource('empleados', EmpleadoController::class)->except(['destroy']);
        
        
        // --- ACTIVOS ---
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
        Route::post('/activos/{id}/baja', [ActivoController::class, 'darBaja'])->name('activos.baja');
        
        // Catálogos y usuarios del sistema (Opcional, si existieran controladores)
        // Route::resource('usuarios', UserController::class);
    });

    // --- REPORTES (Admin y Super Admin) ---
    Route::middleware(['role:Admin|Super Admin'])->group(function () {
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/inventario-pdf', [ReporteController::class, 'inventarioPdf'])->name('reportes.inventario_pdf');
        Route::get('/reportes/bajas-pdf', [ReporteController::class, 'bajasPdf'])->name('reportes.bajas_pdf'); // Ajustado al nombre del método en controlador
        Route::get('/reportes/exportar-excel', [ReporteController::class, 'generarInventarioCSV'])->name('reportes.exportar_excel'); // Ajustado: método se llamaba 'generarInventarioCSV' en el controlador
    });

});

require __DIR__.'/auth.php';