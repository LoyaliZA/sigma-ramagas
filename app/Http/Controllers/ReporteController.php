<?php

namespace App\Http\Controllers;

use App\Models\Activo;
use App\Models\Empleado;
use App\Models\CatalogoUbicacion;
use App\Models\CatalogoEstadoActivo;
use App\Models\CatalogoTipoActivo;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    public function index()
    {
        // 1. Cargamos catálogos para los filtros del buscador (si los mantienes)
        $ubicaciones = CatalogoUbicacion::orderBy('nombre')->get();
        $estados = CatalogoEstadoActivo::orderBy('nombre')->get();
        $tipos = CatalogoTipoActivo::orderBy('nombre')->get();
        $empleados = Empleado::where('estatus', 'Activo')->orderBy('nombre')->get();

        // 2. CALCULAMOS LOS CONTEOS PARA LAS TARJETAS
        // Ajusta los IDs (1, 2, 3...) según tus seeders reales
        $totalActivos = Activo::count();
        
        // Asumiendo: 2 = Asignado/En Uso
        $totalAsignados = Activo::where('estado_id', 2)->count(); 
        
        // Asumiendo: 1 = Disponible
        $totalDisponibles = Activo::where('estado_id', 1)->count();
        
        // Asumiendo: 3 y 4 = Diagnóstico y Mantenimiento
        $totalMantenimiento = Activo::whereIn('estado_id', [3, 4])->count();
        
        // Asumiendo: 5 y 6 = Pendiente de Baja y Baja Definitiva
        $totalBajas = Activo::whereIn('estado_id', [5, 6])->count();

        return view('reportes.index', compact(
            'ubicaciones', 'estados', 'tipos', 'empleados',
            'totalActivos', 'totalAsignados', 'totalDisponibles', 
            'totalMantenimiento', 'totalBajas'
        ));
    }

    // ... Mantén las funciones generarInventario y generarBajas IGUAL que antes ...
    public function generarInventario(Request $request)
    {
        $query = Activo::with(['tipo', 'marca', 'ubicacion', 'estado']); // Sin 'modelo'

        if ($request->ubicacion_id) $query->where('ubicacion_id', $request->ubicacion_id);
        if ($request->estado_id) $query->where('estado_id', $request->estado_id);
        if ($request->tipo_id) $query->where('tipo_id', $request->tipo_id);

        $activos = $query->orderBy('ubicacion_id')->orderBy('tipo_id')->get();
        
        $filtros = [
            'ubicacion' => $request->ubicacion_id ? CatalogoUbicacion::find($request->ubicacion_id)->nombre : 'Todas',
            'estado' => $request->estado_id ? CatalogoEstadoActivo::find($request->estado_id)->nombre : 'Todos',
            'tipo' => $request->tipo_id ? CatalogoTipoActivo::find($request->tipo_id)->nombre : 'Todos',
        ];

        $pdf = Pdf::loadView('reportes.pdf_inventario', compact('activos', 'filtros'))
                  ->setPaper('a4', 'landscape');

        return $pdf->stream('Inventario_' . date('Ymd') . '.pdf');
    }

    public function generarBajas()
    {
        $activos = Activo::with(['tipo', 'marca', 'motivoBaja'])
                    ->whereIn('estado_id', [5, 6])
                    ->orderBy('updated_date', 'desc')
                    ->get();

        $pdf = Pdf::loadView('reportes.pdf_bajas', compact('activos'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('Bajas_' . date('Ymd') . '.pdf');
    }

    public function generarInventarioCSV(Request $request)
    {
        // 1. Reutilizamos la misma lógica de filtrado
        $query = Activo::with(['tipo', 'marca', 'ubicacion', 'estado']);

        if ($request->ubicacion_id) $query->where('ubicacion_id', $request->ubicacion_id);
        if ($request->estado_id) $query->where('estado_id', $request->estado_id);
        if ($request->tipo_id) $query->where('tipo_id', $request->tipo_id);

        $activos = $query->orderBy('ubicacion_id')->orderBy('tipo_id')->get();
        $filename = 'Inventario_' . date('Ymd_His') . '.csv';

        // 2. Creamos un StreamedResponse para descargar sin guardar en disco
        return response()->streamDownload(function () use ($activos) {
            $handle = fopen('php://output', 'w');
            
            // Para que Excel reconozca caracteres latinos (tildes, ñ)
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Encabezados del CSV
            fputcsv($handle, [
                'Numero de Serie', 
                'Tipo', 
                'Marca', 
                'Modelo', 
                'Estado', 
                'Condicion', 
                'Ubicacion', 
                'Costo'
            ]);

            // Filas de datos
            foreach ($activos as $activo) {
                fputcsv($handle, [
                    $activo->numero_serie,
                    optional($activo->tipo)->nombre,
                    optional($activo->marca)->nombre,
                    $activo->modelo,
                    optional($activo->estado)->nombre,
                    optional($activo->condicion)->nombre,
                    optional($activo->ubicacion)->nombre,
                    $activo->costo
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
