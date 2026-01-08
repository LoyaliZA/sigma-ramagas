<?php

namespace App\Http\Controllers;

use App\Models\Activo;
use App\Models\CatalogoEstadoActivo;
use Illuminate\Http\Request;

class AlmacenController extends Controller
{
    public function index()
    {
        // 1. Traer todos los activos que NO están asignados (En Almacén)
        $activosAlmacen = Activo::with(['tipo', 'marca', 'estado', 'ubicacion'])
                    ->where('estado_id', '!=', 2) // Excluir los 'En Uso'
                    ->orderBy('updated_date', 'desc')
                    ->get();

        $estados = CatalogoEstadoActivo::where('id', '!=', 2)->get();

        // 2. Clasificación para las pestañas (Tu lógica original)
        $disponibles = $activosAlmacen->where('estado_id', 1);       
        $enDiagnostico = $activosAlmacen->where('estado_id', 4);     
        $enMantenimiento = $activosAlmacen->where('estado_id', 3);   
        $bajas = $activosAlmacen->whereIn('estado_id', [5, 6]);      

        // 3. DATOS PARA KPIS (NUEVO ESTÁNDAR DE DISEÑO)
        $kpis = [
            'total_items' => $activosAlmacen->count(),
            'total_valor' => $activosAlmacen->sum('costo'), // Suma del costo de inventario
            'total_disponibles' => $disponibles->count(),
            'total_reparacion' => $enDiagnostico->count() + $enMantenimiento->count()
        ];

        return view('almacen.index', compact(
            'activosAlmacen', 
            'estados', 
            'disponibles', 
            'enDiagnostico', 
            'enMantenimiento', 
            'bajas',
            'kpis' // Enviamos los contadores
        ));
    }

    public function cambiarEstado(Request $request, $id)
    {
        $request->validate([
            'nuevo_estado_id' => 'required|exists:catalogo_estadosactivo,id',
            'observaciones' => 'nullable|string'
        ]);

        $activo = Activo::findOrFail($id);
        
        if($activo->estado_id == 2) {
            return response()->json(['success' => false, 'message' => 'No puedes mover un activo asignado.'], 400);
        }

        $activo->estado_id = $request->nuevo_estado_id;
        if($request->observaciones) {
            $activo->observaciones .= "\n[" . date('Y-m-d H:i') . "] Cambio estado: " . $request->observaciones;
        }
        $activo->save();

        return response()->json(['success' => true]);
    }
}