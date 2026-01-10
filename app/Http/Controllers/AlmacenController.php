<?php

namespace App\Http\Controllers;

use App\Models\Activo;
use App\Models\CatalogoEstadoActivo;
use App\Models\CatalogoMotivoBaja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlmacenController extends Controller
{
    public function index()
    {
        // 1. Traer todos los activos que NO están asignados (Estado != 2)
        $activosAlmacen = Activo::with(['tipo', 'marca', 'estado', 'ubicacion'])
            ->where('estado_id', '!=', 2) 
            ->orderBy('updated_date', 'desc')
            ->get();

        // 2. Filtrar estados para el Modal "Gestionar"
        $estados = CatalogoEstadoActivo::whereNotIn('id', [2, 6])->orderBy('nombre')->get();

        // 3. Obtener catálogo de motivos para el modal de Baja
        $motivosBaja = CatalogoMotivoBaja::all();

        // 4. Clasificación para pestañas
        $disponibles = $activosAlmacen->where('estado_id', 1);
        $enDiagnostico = $activosAlmacen->where('estado_id', 4);
        $enMantenimiento = $activosAlmacen->where('estado_id', 3);
        $bajas = $activosAlmacen->whereIn('estado_id', [5, 6]);

        // 5. KPIS
        $kpis = [
            'total_items' => $activosAlmacen->count(),
            'total_valor' => $activosAlmacen->sum('costo'),
            'total_disponibles' => $disponibles->count(),
            'total_reparacion' => $enDiagnostico->count() + $enMantenimiento->count()
        ];

        return view('almacen.index', compact(
            'activosAlmacen',
            'estados',
            'motivosBaja',
            'disponibles',
            'enDiagnostico',
            'enMantenimiento',
            'bajas',
            'kpis'
        ));
    }

    public function cambiarEstado(Request $request, $id)
    {
        $request->validate([
            'nuevo_estado_id' => 'required|exists:catalogo_estadosactivo,id',
            'observaciones' => 'nullable|string'
        ]);

        $activo = Activo::findOrFail($id);

        // [LOG] 1. Capturamos el estado ANTERIOR antes de modificar nada
        // Usamos toArray() para evitar errores de serialización de objetos
        $valoresAnteriores = $activo->toArray();

        // Validaciones de seguridad
        if ($activo->estado_id == 2) {
            return response()->json(['success' => false, 'message' => 'No puedes mover un activo que está En Uso.'], 400);
        }
        if ($activo->estado_id == 6) {
            return response()->json(['success' => false, 'message' => 'Este activo ya está en Baja Definitiva y no se puede modificar.'], 400);
        }
        if ($request->nuevo_estado_id == 6) {
            return response()->json(['success' => false, 'message' => 'Para baja definitiva use el botón Confirmar Baja en la pestaña de Pendientes.'], 400);
        }

        // Aplicar cambios
        $activo->estado_id = $request->nuevo_estado_id;
        
        if ($request->observaciones) {
            $activo->observaciones .= "\n[ALMACEN " . date('Y-m-d') . "]: " . $request->observaciones;
        }
        
        $activo->save();

        // [LOG] 2. Registramos la acción usando la función heredada
        // Ahora pasamos $activo->toArray() que ya contiene los valores NUEVOS
        $this->logAction(
            'Cambio de Estado (Almacén)', // Acción
            'activo',                     // Tabla
            $activo->id,                  // ID Registro
            $valoresAnteriores,           // Antes
            $activo->toArray()            // Después
        );

        return response()->json(['success' => true]);
    }

    public function confirmarBajaDefinitiva(Request $request, $id)
    {
        $activo = Activo::findOrFail($id);

        // Validar
        $request->validate([
            'motivo_baja_id' => 'required|exists:catalogo_motivosbaja,id',
            'comentarios' => 'required|string|min:5'
        ]);

        if ($activo->estado_id != 5) {
            return response()->json(['success' => false, 'message' => 'El activo debe estar en Pendiente de Baja primero.'], 400);
        }

        // [LOG] Captura previa
        $valoresAnteriores = $activo->toArray();

        // Usamos DB::transaction y pasamos $valoresAnteriores dentro
        DB::transaction(function() use ($activo, $request, $valoresAnteriores) {
            $activo->estado_id = 6; // Baja Definitiva
            $activo->fecha_baja = now();
            $activo->motivo_baja_id = $request->motivo_baja_id;
            $activo->observaciones .= "\n[BAJA DEFINITIVA " . date('Y-m-d H:i') . "]: " . $request->comentarios;
            
            $activo->save();

            // [LOG] Registramos dentro de la transacción
            // Importante: Usamos $this->logAction dentro del closure si PHP > 7.4 lo permite
            // Si te da error aquí, avísame, pero en Laravel moderno funciona directo.
            $this->logAction(
                'Baja Definitiva', 
                'activo', 
                $activo->id, 
                $valoresAnteriores, 
                $activo->toArray()
            );
        });

        return response()->json(['success' => true]);
    }
}