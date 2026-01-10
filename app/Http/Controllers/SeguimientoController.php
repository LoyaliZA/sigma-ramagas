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
        $filtro = $request->get('filtro'); 
        
        // 1. KPIs
        $kpis = [
            'total_rastreados' => Activo::where('estado_id', '!=', 6)->count(),
            'en_uso' => Activo::where('estado_id', 2)->count(),
            'disponibles' => Activo::where('estado_id', 1)->count(),
            'mantenimiento' => Activo::whereIn('estado_id', [3, 4])->count(),
        ];

        // 2. Consulta Principal
        $query = Activo::with(['tipo', 'marca', 'ubicacion', 'estado'])
                        ->where('estado_id', '!=', 6); 

        // --- LÓGICA DE FILTROS RÁPIDOS ---
        if ($filtro) {
            switch ($filtro) {
                case 'en_uso':
                    $query->where('estado_id', 2);
                    break;
                case 'disponibles':
                    $query->where('estado_id', 1);
                    break;
                case 'mantenimiento':
                    $query->whereIn('estado_id', [3, 4]); 
                    break;
            }
        }

        // --- BÚSQUEDA ---
        if ($busqueda) {
            $query->where(function($q) use ($busqueda) {
                $q->where('numero_serie', 'like', "%$busqueda%")
                  ->orWhere('modelo', 'like', "%$busqueda%")
                  ->orWhere('codigo_interno', 'like', "%$busqueda%")
                  ->orWhereHas('marca', fn($qM) => $qM->where('nombre', 'like', "%$busqueda%"))
                  ->orWhereHas('tipo', fn($qT) => $qT->where('nombre', 'like', "%$busqueda%"))
                  ->orWhereHas('empleado', fn($qE) => $qE->where('nombre', 'like', "%$busqueda%")->orWhere('apellido_paterno', 'like', "%$busqueda%"));
            });
        }

        $activos = $query->orderBy('updated_date', 'desc')
                        ->paginate($limit)
                        ->appends($request->query()); 

        // Redirección inteligente
        if ($busqueda && !$filtro && $activos->total() == 1) {
            return redirect()->route('seguimiento.show', $activos->items()[0]->id);
        }

        return view('seguimiento.index', compact('activos', 'busqueda', 'limit', 'kpis', 'filtro'));
    }

    public function show($id)
    {
        $activo = Activo::with(['tipo', 'marca', 'ubicacion', 'estado', 'condicion', 'empleado.puesto', 'empleado.departamento'])
                    ->findOrFail($id);

        $historial = Asignacion::with(['empleado', 'estadoEntrega', 'estadoDevolucion'])
                        ->where('activo_id', $id)
                        ->orderBy('fecha_asignacion', 'desc')
                        ->get();

        // [LOG] Registrar Consulta Detallada
        // Registramos que el usuario vio el historial completo de este activo específico
        $this->logAction(
            'Consulta de Rastreo', 
            'activos', 
            $id, 
            null, 
            [
                'accion' => 'Visualización de Historial Completo',
                'activo' => $activo->codigo_interno,
                'serie' => $activo->numero_serie
            ]
        );

        return view('seguimiento.show', compact('activo', 'historial'));
    }
}