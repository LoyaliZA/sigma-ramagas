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
        
        $query = Activo::with(['tipo', 'marca', 'ubicacion', 'estado']);

        if ($busqueda) {
            $query->where(function($q) use ($busqueda) {
                $q->where('numero_serie', 'like', "%$busqueda%")
                  ->orWhere('modelo', 'like', "%$busqueda%")
                  ->orWhereHas('marca', function($qMarca) use ($busqueda) {
                      $qMarca->where('nombre', 'like', "%$busqueda%");
                  })
                  ->orWhereHas('tipo', function($qTipo) use ($busqueda) {
                      $qTipo->where('nombre', 'like', "%$busqueda%");
                  });
            });
        }

        // Ejecutamos la paginación PRIMERO para obtener los resultados y el conteo real
        // Esto evita errores por reutilizar el $query después de un count() manual.
        $activos = $query->orderBy('created_date', 'desc')
                         ->paginate($limit)
                         ->appends($request->query());

        // Truco UX: Si hay búsqueda y el total de resultados es exactamente 1, redirigimos.
        // Usamos $activos->total() que ya viene de la paginación.
        if ($busqueda && $activos->total() == 1) {
            // Obtenemos el ID del primer elemento de la colección actual
            return redirect()->route('seguimiento.show', $activos->items()[0]->id);
        }

        return view('seguimiento.index', compact('activos', 'busqueda', 'limit'));
    }

    public function show($id)
    {
        $activo = Activo::with(['tipo', 'marca', 'ubicacion', 'estado', 'condicion'])
                    ->findOrFail($id);

        // AGREGADO: 'empleado.puesto' para que la vista tenga el dato listo y no falle.
        $historial = Asignacion::with(['empleado.departamento', 'empleado.puesto', 'estadoEntrega', 'estadoDevolucion'])
                        ->where('activo_id', $id)
                        ->orderBy('fecha_asignacion', 'desc')
                        ->get();

        return view('seguimiento.show', compact('activo', 'historial'));
    }
}