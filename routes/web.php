<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\ActivoController;
use App\Http\Controllers\AsignacionController;

Route::redirect('/', '/dashboard');

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

Route::resource('empleados',EmpleadoController::class);
// Dentro del grupo de rutas o donde tienes las de empleados
Route::get('empleados/{id}/historial-pdf', [EmpleadoController::class, 'generarHistorialPdf'])->name('empleados.historial_pdf');

Route::get('/activos/bajas', [ActivoController::class, 'bajas'])->name('activos.bajas'); // <--- ESTA VA PRIMERO
Route::post('activos/quick-add-catalogo', [ActivoController::class, 'storeCatalogo'])->name('activos.quick_add');
Route::post('/activos/{id}/baja', [ActivoController::class, 'darBaja'])->name('activos.dar_baja');

// 2. DESPUÉS: La ruta genérica (resource)
// Como esta ruta "atrapa todo" ({id}), debe ir al final para no comerse a las anteriores
Route::resource('activos', ActivoController::class);

Route::get('asignaciones', [AsignacionController::class, 'index'])->name('asignaciones.index');
Route::post('asignaciones', [AsignacionController::class, 'store'])->name('asignaciones.store');
Route::post('asignaciones/{id}/devolver', [AsignacionController::class, 'devolver'])->name('asignaciones.devolver');
Route::get('asignaciones/{id}/carta-responsiva', [AsignacionController::class, 'imprimirCarta'])->name('asignaciones.carta');
Route::get('asignaciones/{id}/carta-devolucion', [AsignacionController::class, 'imprimirCartaDevolucion'])->name('asignaciones.carta_devolucion');

// Almacén Routes
Route::get('almacen', [App\Http\Controllers\AlmacenController::class, 'index'])->name('almacen.index');
Route::post('almacen/{id}/cambiar-estado', [App\Http\Controllers\AlmacenController::class, 'cambiarEstado'])->name('almacen.cambiar_estado');

// Seguimiento Routes
Route::get('seguimiento', [App\Http\Controllers\SeguimientoController::class, 'index'])->name('seguimiento.index');
Route::get('seguimiento/{id}', [App\Http\Controllers\SeguimientoController::class, 'show'])->name('seguimiento.show');

// Módulo de Reportes
Route::get('/reportes', [App\Http\Controllers\ReporteController::class, 'index'])->name('reportes.index');
Route::get('/reportes/inventario', [App\Http\Controllers\ReporteController::class, 'generarInventario'])->name('reportes.inventario');
Route::get('/reportes/bajas', [App\Http\Controllers\ReporteController::class, 'generarBajas'])->name('reportes.bajas');
Route::get('/reportes/inventario-csv', [App\Http\Controllers\ReporteController::class, 'generarInventarioCSV'])->name('reportes.inventario_csv');