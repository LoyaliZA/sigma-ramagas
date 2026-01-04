<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\Activo;
use App\Models\Empleado;
use App\Models\CatalogoEstadoAsignacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf; // Importante

class AsignacionController extends Controller
{
    public function index()
    {
        $asignaciones = Asignacion::with(['activo.tipo', 'empleado', 'estadoEntrega'])
                        ->orderBy('fecha_asignacion', 'desc')
                        ->get();

        $empleados = Empleado::where('estatus', 'Activo')->orderBy('nombre')->get();

        $activosDisponibles = Activo::where('estado_id', 1)
                              ->with(['tipo', 'marca'])
                              ->get();

        $estadosEntrega = CatalogoEstadoAsignacion::orderBy('nombre')->get();

        return view('asignaciones.index', compact('asignaciones', 'empleados', 'activosDisponibles', 'estadosEntrega'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'empleado_id' => 'required|exists:empleado,id',
                'activo_id' => 'required|exists:activo,id',
                'fecha_asignacion' => 'required|date',
                'estado_entrega_id' => 'required|exists:catalogo_estadosasignacion,id',
                'observaciones' => 'nullable|string'
            ]);

            DB::beginTransaction();

            $asignacion = Asignacion::create([
                'empleado_id' => $request->empleado_id,
                'activo_id' => $request->activo_id,
                'fecha_asignacion' => $request->fecha_asignacion,
                'estado_entrega_id' => $request->estado_entrega_id,
                'observaciones_entrega' => $request->observaciones
            ]);

            $activo = Activo::findOrFail($request->activo_id);
            
            if($activo->estado_id != 1) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'El activo ya no está disponible.'], 409);
            }

            $activo->estado_id = 2;
            $activo->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Asignación registrada correctamente.'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function devolver(Request $request, $id)
    {
        try {
            $request->validate([
                'fecha_devolucion' => 'required|date',
                'estado_devolucion_id' => 'required|exists:catalogo_estadosasignacion,id',
                'observaciones' => 'nullable|string'
            ]);

            DB::beginTransaction();

            $asignacion = Asignacion::findOrFail($id);
            
            if($asignacion->fecha_devolucion != null) {
                return response()->json(['success' => false, 'message' => 'Este activo ya fue devuelto.'], 400);
            }

            $asignacion->fecha_devolucion = $request->fecha_devolucion;
            $asignacion->estado_devolucion_id = $request->estado_devolucion_id;
            $asignacion->observaciones_devolucion = $request->observaciones;
            $asignacion->save();

            $activo = Activo::findOrFail($asignacion->activo_id);
            
            if ($request->estado_devolucion_id == 1) {
                $activo->estado_id = 1;
            } else {
                $activo->estado_id = 4;
            }
            $activo->save();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Devolución registrada. Activo actualizado.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function imprimirCarta($id)
    {
        $asignacion = Asignacion::with(['activo.tipo', 'activo.marca', 'empleado.puesto', 'empleado.departamento', 'estadoEntrega'])
                      ->findOrFail($id);

        $pdf = Pdf::loadView('pdf.carta_responsiva', compact('asignacion'));
        
        // Se abre en el navegador (stream) en lugar de descargar (download)
        return $pdf->stream('Carta_Responsiva_' . $asignacion->empleado->numero_empleado . '.pdf');
    }

    // NUEVO MÉTODO (Devolución)
    public function imprimirCartaDevolucion($id)
    {
        $asignacion = Asignacion::with(['activo.tipo', 'activo.marca', 'empleado.departamento', 'estadoDevolucion'])
                      ->findOrFail($id);

        // Validar que realmente se haya devuelto
        if (!$asignacion->fecha_devolucion) {
            return back()->with('error', 'El activo aún no ha sido devuelto.');
        }

        $pdf = Pdf::loadView('pdf.carta_devolucion', compact('asignacion'));
        return $pdf->stream('Devolucion_' . $asignacion->empleado->numero_empleado . '.pdf');
    }
}