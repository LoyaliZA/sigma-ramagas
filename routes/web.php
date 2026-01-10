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
use App\Http\Controllers\ConfiguracionController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- GRUPO 1: SOLO LECTURA ---
    Route::get('seguimiento', [SeguimientoController::class, 'index'])->name('seguimiento.index');
    Route::get('seguimiento/{id}', [SeguimientoController::class, 'show'])->name('seguimiento.show');


    // --- GRUPO 2: GESTIÓN INTERMEDIA (Admin | Super Admin) ---
    Route::middleware(['role:Admin|Super Admin'])->group(function () {
        
        // --- 1. REPORTES (Especificas) ---
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/inventario-pdf', [ReporteController::class, 'generarInventario'])->name('reportes.inventario');
        Route::get('/reportes/bajas-pdf', [ReporteController::class, 'bajasPdf'])->name('reportes.bajas_pdf');
        Route::get('/reportes/exportar-excel', [ReporteController::class, 'generarInventarioCSV'])->name('reportes.inventario_csv');

        // --- 2. ASIGNACIONES (Especificas primero) ---
        Route::post('asignaciones/subir-documento', [AsignacionController::class, 'subirDocumento'])->name('asignaciones.subir_documento');
        Route::get('asignaciones/{id}/historial-documentos', [AsignacionController::class, 'obtenerHistorial'])->name('asignaciones.historial_documentos');
        Route::post('asignaciones/{id}/devolver', [AsignacionController::class, 'devolver'])->name('asignaciones.devolver');
        Route::get('asignaciones/carta/{id}', [AsignacionController::class, 'imprimirCarta'])->name('asignaciones.carta');
        Route::get('asignaciones/carta-lote/{loteId}', [AsignacionController::class, 'imprimirCartaPorLote'])->name('asignaciones.carta_lote');
        Route::get('asignaciones/carta-devolucion/{id}', [AsignacionController::class, 'imprimirCartaDevolucion'])->name('asignaciones.carta_devolucion');
        // Recurso general (al final)
        Route::resource('asignaciones', AsignacionController::class);

        // --- 3. ALMACÉN (Especificas primero) ---
        Route::post('almacen/{id}/cambiar-estado', [AlmacenController::class, 'cambiarEstado'])->name('almacen.cambiar_estado');
        Route::post('almacen/{id}/confirmar-baja', [AlmacenController::class, 'confirmarBajaDefinitiva'])->name('almacen.confirmar_baja');
        // Recurso general
        Route::resource('almacen', AlmacenController::class)->only(['index']);

        // --- 4. EMPLEADOS (Especificas primero) ---
        Route::post('/empleados/{id}/documentos', [EmpleadoController::class, 'subirDocumento'])->name('empleados.documentos.store');
        Route::delete('/empleados/documentos/{id}', [EmpleadoController::class, 'eliminarDocumento'])->name('empleados.documentos.destroy');
        Route::get('empleados/{id}/historial-pdf', [EmpleadoController::class, 'generarHistorialPdf'])->name('empleados.historial_pdf');
        // Recurso general
        Route::resource('empleados', EmpleadoController::class)->except(['destroy']);

        // --- 5. ACTIVOS (Especificas primero - AQUÍ ESTABA TU ERROR) ---
        // Estas rutas deben ir ANTES del resource 'activos' para que 'bajas' no se tome como un {id}
        Route::get('/activos/bajas', [ActivoController::class, 'bajas'])->name('activos.bajas');
        Route::post('activos/quick-add-catalogo', [ActivoController::class, 'storeCatalogo'])->name('activos.quick_add');
        // Recurso general
        Route::resource('activos', ActivoController::class)->except(['destroy', 'darBaja']);
    });


    // --- GRUPO 3: SUPER ADMIN (Operaciones destructivas y CONFIGURACIÓN) ---
    Route::middleware(['role:Super Admin'])->group(function () {
        
        // Operaciones destructivas (Especificas)
        Route::delete('empleados/{empleado}', [EmpleadoController::class, 'destroy'])->name('empleados.destroy');
        Route::post('/activos/{id}/baja', [ActivoController::class, 'darBaja'])->name('activos.baja');
        Route::delete('activos/{activo}', [ActivoController::class, 'destroy'])->name('activos.destroy');

        // --- NUEVO MODULO DE CONFIGURACIÓN ---
        Route::prefix('configuracion')->name('configuracion.')->group(function() {
            // Dashboard de configuración (Menú principal)
            Route::get('/', [ConfiguracionController::class, 'index'])->name('index');
            
            // 1. Gestión de Usuarios
            Route::get('/usuarios', [ConfiguracionController::class, 'usuarios'])->name('usuarios');
            Route::post('/usuarios', [ConfiguracionController::class, 'storeUsuario'])->name('usuarios.store');
            Route::put('/usuarios/{id}', [ConfiguracionController::class, 'updateUsuario'])->name('usuarios.update');
            Route::delete('/usuarios/{id}', [ConfiguracionController::class, 'destroyUsuario'])->name('usuarios.destroy');

            // 2. Bitácora / Logs
            Route::get('/bitacora', [ConfiguracionController::class, 'bitacora'])->name('bitacora');
            
            // 3. Catálogos (Dinámicos)
            // Ruta para Reset de Fábrica (Especifica antes de la dinámica)
            Route::post('/catalogos-reset', [ConfiguracionController::class, 'resetCatalogos'])->name('catalogos.reset');
            
            // Rutas CRUD Dinámicas
            // {cat?} es opcional para que /catalogos lleve al default
            Route::get('/catalogos/{cat?}', [ConfiguracionController::class, 'catalogos'])->name('catalogos');
            Route::post('/catalogos/{cat}', [ConfiguracionController::class, 'storeCatalogo'])->name('catalogos.store');
            Route::put('/catalogos/{cat}/{id}', [ConfiguracionController::class, 'updateCatalogo'])->name('catalogos.update');
            Route::delete('/catalogos/{cat}/{id}', [ConfiguracionController::class, 'destroyCatalogo'])->name('catalogos.destroy');
        });
    });

});

require __DIR__.'/auth.php';