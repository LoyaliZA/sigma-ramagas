<?php

namespace App\Http\Controllers;

use App\Models\Activo;
use App\Models\CatalogoEstadoActivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlmacenController extends Controller
{
    public function index()
    {
        // Obtenemos conteos para los "Widgets" del dashboard de Almacén
        // Asumimos IDs según lógica previa: 1=Disponible, 3=Mantenimiento, 4=Baja/Diagnóstico (Ajustar según tus seeds reales)
        // Lo ideal es buscar por nombre, pero por rapidez usaremos IDs comunes o filtrado general.
        
        // Estrategia: Traer todos los activos que NO están asignados (Estado != 2 'Asignado')
        $activosAlmacen = Activo::with(['tipo', 'marca', 'estado', 'ubicacion'])
                    ->where('estado_id', '!=', 2) // 2 = 'En Uso' (Está con un empleado)
                    ->orderBy('updated_date', 'desc')
                    ->get();

        $estados = CatalogoEstadoActivo::where('id', '!=', 2)->get(); // Para poder cambiar el estado

        // Clasificación para las pestañas
        $disponibles = $activosAlmacen->where('estado_id', 1);       // 1 = Disponible
        $enDiagnostico = $activosAlmacen->where('estado_id', 4);     // 4 = En Diagnóstico (CORREGIDO)
        $enMantenimiento = $activosAlmacen->where('estado_id', 3);   // 3 = En Mantenimiento
        $bajas = $activosAlmacen->whereIn('estado_id', [5, 6]);      // 5 = Pendiente, 6 = Baja Definitiva

        return view('almacen.index', compact('activosAlmacen', 'estados', 'disponibles', 'enDiagnostico', 'enMantenimiento', 'bajas'));
    }

    public function cambiarEstado(Request $request, $id)
    {
        $request->validate([
            'nuevo_estado_id' => 'required|exists:catalogo_estadosactivo,id',
            'observaciones' => 'nullable|string'
        ]);

        $activo = Activo::findOrFail($id);
        
        // Evitar cambiar si está asignado
        if($activo->estado_id == 2) {
            return response()->json(['success' => false, 'message' => 'No puedes mover un activo que está asignado a un empleado.'], 400);
        }

        $activo->estado_id = $request->nuevo_estado_id;
        // Concatenamos la observación nueva al historial (simple)
        if($request->observaciones) {
            $activo->observaciones .= "\n[" . date('Y-m-d H:i') . "] Cambio estado: " . $request->observaciones;
        }
        $activo->save();

        return response()->json(['success' => true, 'message' => 'Estado actualizado correctamente.']);
    }
}