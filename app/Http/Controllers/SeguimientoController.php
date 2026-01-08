<?php

namespace App\Http\Controllers;

use App\Models\Activo;
use App\Models\Asignacion;
use Illuminate\Http\Request;

class SeguimientoController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $busqueda = $request->get('q');
        
        // 1. KPIs (Resumen Superior)
        $kpis = [
            'total_rastreados' => Activo::where('estado_id', '!=', 6)->count(),
            'en_uso' => Activo::where('estado_id', 2)->count(),
            'disponibles' => Activo::where('estado_id', 1)->count(),
            'mantenimiento' => Activo::whereIn('estado_id', [3, 4])->count(),
        ];

        // 2. Consulta Principal (Busca Activos)
        $query = Activo::with(['tipo', 'marca', 'ubicacion', 'estado'])
                        ->where('estado_id', '!=', 6); // Excluir bajas definitivas

        if ($busqueda) {
            $query->where(function($q) use ($busqueda) {
                $q->where('numero_serie', 'like', "%$busqueda%")
                  ->orWhere('modelo', 'like', "%$busqueda%")
                  ->orWhere('codigo_interno', 'like', "%$busqueda%") // Agregué búsqueda por código interno
                  ->orWhereHas('marca', fn($qM) => $qM->where('nombre', 'like', "%$busqueda%"))
                  ->orWhereHas('tipo', fn($qT) => $qT->where('nombre', 'like', "%$busqueda%"))
                  // Búsqueda por empleado actual
                  ->orWhereHas('empleado', fn($qE) => $qE->where('nombre', 'like', "%$busqueda%")->orWhere('apellido_paterno', 'like', "%$busqueda%"));
            });
        }

        $activos = $query->orderBy('updated_date', 'desc')
                         ->paginate($limit)
                         ->appends($request->query());

        // Redirección directa si es resultado único (UX Pro)
        if ($busqueda && $activos->total() == 1) {
            return redirect()->route('seguimiento.show', $activos->items()[0]->id);
        }

        return view('seguimiento.index', compact('activos', 'busqueda', 'limit', 'kpis'));
    }

    public function show($id)
    {
        // Carga del Activo
        $activo = Activo::with(['tipo', 'marca', 'ubicacion', 'estado', 'condicion', 'empleado.puesto', 'empleado.departamento'])
                    ->findOrFail($id);

        // Carga del Historial (Asignaciones)
        // Usamos las relaciones correctas de tu modelo Asignacion
        $historial = Asignacion::with(['empleado', 'estadoEntrega', 'estadoDevolucion'])
                        ->where('activo_id', $id)
                        ->orderBy('fecha_asignacion', 'desc')
                        ->get();

        return view('seguimiento.show', compact('activo', 'historial'));
    }
}