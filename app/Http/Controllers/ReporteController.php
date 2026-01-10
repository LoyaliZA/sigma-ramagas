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
        $ubicaciones = CatalogoUbicacion::orderBy('nombre')->get();
        $estados = CatalogoEstadoActivo::orderBy('nombre')->get();
        $tipos = CatalogoTipoActivo::orderBy('nombre')->get();

        // CAMBIO AQUI: Agregamos ->with('documentos')
        $empleados = Empleado::with('documentos')
            ->where('estatus', 'Activo')
            ->orderBy('nombre')
            ->get();

        $totalActivos = Activo::count();
        $totalAsignados = Activo::where('estado_id', 2)->count();
        $totalDisponibles = Activo::where('estado_id', 1)->count();
        $totalMantenimiento = Activo::whereIn('estado_id', [3, 4])->count();
        $totalBajas = Activo::whereIn('estado_id', [5, 6])->count();

        // Opcional: Registrar que alguien entró al dashboard de reportes
        // $this->logAction('Acceso', 'Reportes', 0, null, ['seccion' => 'Dashboard Reportes']);

        return view('reportes.index', compact(
            'ubicaciones',
            'estados',
            'tipos',
            'empleados',
            'totalActivos',
            'totalAsignados',
            'totalDisponibles',
            'totalMantenimiento',
            'totalBajas'
        ));
    }

    public function generarInventario(Request $request)
    {
        $query = Activo::with(['tipo', 'marca', 'ubicacion', 'estado']);

        if ($request->ubicacion_id)
            $query->where('ubicacion_id', $request->ubicacion_id);
        if ($request->estado_id)
            $query->where('estado_id', $request->estado_id);
        if ($request->tipo_id)
            $query->where('tipo_id', $request->tipo_id);

        $activos = $query->orderBy('ubicacion_id')->orderBy('tipo_id')->get();

        $filtros = [
            'ubicacion' => $request->ubicacion_id ? CatalogoUbicacion::find($request->ubicacion_id)->nombre : 'Todas',
            'estado' => $request->estado_id ? CatalogoEstadoActivo::find($request->estado_id)->nombre : 'Todos',
            'tipo' => $request->tipo_id ? CatalogoTipoActivo::find($request->tipo_id)->nombre : 'Todos',
        ];

        // [LOG] Registrar Generación de PDF
        // Guardamos los filtros usados en 'valores_nuevos' para saber qué información sacaron
        $this->logAction(
            'Reporte PDF Generado', 
            'activos', 
            'REPORT', // ID Genérico
            null, 
            [
                'tipo_reporte' => 'Inventario General',
                'filtros_usados' => $filtros,
                'registros_encontrados' => $activos->count()
            ]
        );

        $pdf = Pdf::loadView('reportes.pdf_inventario', compact('activos', 'filtros'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Inventario_' . date('Ymd_His') . '.pdf');
    }

    public function bajasPdf()
    {
        $activos = Activo::with(['tipo', 'marca', 'motivoBaja'])
            ->whereIn('estado_id', [5, 6])
            ->orderBy('fecha_baja', 'desc')
            ->get();

        // [LOG] Registrar Generación de PDF Bajas
        $this->logAction(
            'Reporte PDF Generado', 
            'activos', 
            'REPORT', 
            null, 
            [
                'tipo_reporte' => 'Historial de Bajas',
                'registros_encontrados' => $activos->count()
            ]
        );

        $pdf = Pdf::loadView('reportes.pdf_bajas', compact('activos'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('Reporte_Bajas_' . date('Ymd_His') . '.pdf');
    }

    public function generarInventarioCSV(Request $request)
    {
        $query = Activo::with(['tipo', 'marca', 'ubicacion', 'estado', 'condicion']);

        if ($request->ubicacion_id)
            $query->where('ubicacion_id', $request->ubicacion_id);
        if ($request->estado_id)
            $query->where('estado_id', $request->estado_id);
        if ($request->tipo_id)
            $query->where('tipo_id', $request->tipo_id);

        $activos = $query->orderBy('ubicacion_id')->orderBy('tipo_id')->get();
        
        // [LOG] Registrar Descarga de CSV (Importante por seguridad de datos masivos)
        $this->logAction(
            'Exportación CSV', 
            'activos', 
            'EXPORT', 
            null, 
            [
                'filtros' => $request->all(),
                'total_filas' => $activos->count()
            ]
        );

        $filename = 'Inventario_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($activos) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM

            fputcsv($handle, [
                'Numero de Serie',
                'Codigo Interno',
                'Tipo',
                'Marca',
                'Modelo',
                'Estado',
                'Condicion',
                'Ubicacion',
                'Costo'
            ]);

            foreach ($activos as $activo) {
                fputcsv($handle, [
                    $activo->numero_serie,
                    $activo->codigo_interno,
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