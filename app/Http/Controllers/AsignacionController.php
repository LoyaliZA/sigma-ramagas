<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\Activo;
use App\Models\Empleado;
use App\Models\CatalogoEstadoAsignacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AsignacionController extends Controller
{
    public function index(Request $request)
    {
        $query = Asignacion::with([
            'activo.tipo', 
            'activo.marca', 
            'empleado.departamento', 
            'estadoEntrega', 
            'estadoDevolucion'
        ])->orderBy('fecha_asignacion', 'desc');

        // Búsqueda
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('empleado', function($qEmp) use ($search) {
                    $qEmp->where('nombre', 'LIKE', "%{$search}%")
                        ->orWhere('apellido_paterno', 'LIKE', "%{$search}%")
                        ->orWhere('numero_empleado', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('activo', function($qAct) use ($search) {
                    $qAct->where('numero_serie', 'LIKE', "%{$search}%")
                        ->orWhere('modelo', 'LIKE', "%{$search}%");
                });
            });
        }

        $todasLasAsignaciones = $query->get();
        $asignacionesActivas = $todasLasAsignaciones->whereNull('fecha_devolucion');
        $historial = $todasLasAsignaciones->whereNotNull('fecha_devolucion');

        $empleados = Empleado::where('estatus', 'Activo')->orderBy('nombre')->get();
        $activosDisponibles = Activo::where('estado_id', 1)->with(['tipo', 'marca'])->get();
        $estadosEntrega = CatalogoEstadoAsignacion::orderBy('nombre')->get();

        return view('asignaciones.index', compact(
            'asignacionesActivas', 'historial', 'empleados', 'activosDisponibles', 'estadosEntrega'
        ));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'empleado_id' => 'required|exists:empleado,id',
                'activos' => 'required|array|min:1',
                'activos.*' => 'exists:activo,id',
                'fecha_asignacion' => 'required|date',
                'estado_entrega_id' => 'required|exists:catalogo_estadosasignacion,id',
                'observaciones' => 'nullable|string'
            ]);

            DB::beginTransaction();

            $loteId = Str::uuid();
            $fechaConHora = Carbon::parse($request->fecha_asignacion)->setTimeFrom(now());

            foreach ($request->activos as $activoId) {
                $activo = Activo::lockForUpdate()->find($activoId);
                
                if($activo->estado_id != 1) {
                    throw new \Exception("El activo {$activo->numero_serie} ya no está disponible.");
                }

                // Creamos la asignación
                $asignacion = Asignacion::create([
                    'id' => Str::uuid(),
                    'lote_id' => $loteId,
                    'empleado_id' => $request->empleado_id,
                    'activo_id' => $activoId,
                    'fecha_asignacion' => $fechaConHora,
                    'estado_entrega_id' => $request->estado_entrega_id,
                    'observaciones_entrega' => $request->observaciones
                ]);

                $activo->estado_id = 2; // En Uso
                $activo->save();

                // [LOG] Registrar creación de asignación
                // Como es nuevo, "valores_anteriores" es null
                $this->logAction(
                    'Asignación Creada', 
                    'asignacion', 
                    $asignacion->id, 
                    null, 
                    $asignacion->toArray()
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Asignación registrada correctamente.',
                'lote_id' => $loteId
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function subirDocumento(Request $request)
    {
        $request->validate([
            'asignacion_id' => 'required|exists:asignacion,id',
            'documento' => 'required|file|mimes:pdf,jpg,png|max:1024' 
        ]);

        try {
            DB::beginTransaction();

            $asignacion = Asignacion::findOrFail($request->asignacion_id);
            
            // [LOG] Captura previa
            $valoresAnteriores = $asignacion->toArray();

            $file = $request->file('documento');
            
            $referencia = $asignacion->lote_id ?? $asignacion->id;
            $filename = 'DOC_' . $referencia . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documentos', $filename, 'public');

            // Actualizar URL
            if ($asignacion->lote_id) {
                // Si es por lote, actualiza todas las del lote
                Asignacion::where('lote_id', $asignacion->lote_id)->update(['carta_responsiva_url' => $path]);
            } else {
                $asignacion->carta_responsiva_url = $path;
                $asignacion->save();
            }

            // Historial
            DB::table('asignacion_documento_historial')->insert([
                'id' => Str::uuid(),
                'asignacion_id' => $asignacion->id,
                'lote_id' => $asignacion->lote_id,
                'url_archivo' => $path,
                'nombre_archivo_original' => $file->getClientOriginalName(),
                'subido_por_id' => auth()->id() ?? null,
                'fecha_subida' => now()
            ]);

            // [LOG] Registrar subida
            // Usamos fresh() para traer el dato actualizado de la BD (especialmente util si se actualizó por lote)
            $this->logAction(
                'Subida de Responsiva', 
                'asignacion', 
                $asignacion->id, 
                $valoresAnteriores, 
                $asignacion->fresh()->toArray()
            );

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Documento subido correctamente.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function obtenerHistorial($id)
    {
        $asignacion = Asignacion::findOrFail($id);
        $query = DB::table('asignacion_documento_historial');
        
        if ($asignacion->lote_id) {
            $query->where('lote_id', $asignacion->lote_id);
        } else {
            $query->where('asignacion_id', $id);
        }

        $historial = $query->orderBy('fecha_subida', 'desc')->get();
        return response()->json(['success' => true, 'historial' => $historial]);
    }

    public function devolver(Request $request, $id)
    {
        try {
            $request->validate([
                'estado_devolucion_id' => 'required|exists:catalogo_estadosasignacion,id',
                'observaciones' => 'nullable|string'
            ]);

            DB::beginTransaction();

            $asignacion = Asignacion::findOrFail($id);
            
            // [LOG] Captura previa
            $valoresAnteriores = $asignacion->toArray();

            if($asignacion->fecha_devolucion != null) {
                return response()->json(['success' => false, 'message' => 'Ya fue devuelto.'], 400);
            }

            $fechaDevolucionExacta = now(); 

            $asignacion->update([
                'fecha_devolucion' => $fechaDevolucionExacta,
                'estado_devolucion_id' => $request->estado_devolucion_id,
                'observaciones_devolucion' => $request->observaciones
            ]);

            $activo = Activo::find($asignacion->activo_id);
            // Lógica de estado del activo (Disponible o Diagnóstico)
            $activo->estado_id = ($request->estado_devolucion_id == 1) ? 1 : 4; 
            $activo->save();

            // [LOG] Registrar devolución
            $this->logAction(
                'Devolución de Activo', 
                'asignacion', 
                $asignacion->id, 
                $valoresAnteriores, 
                $asignacion->fresh()->toArray()
            );

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Devolución procesada correctamente.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // ... Métodos de impresión sin cambios (son de lectura) ...

    public function imprimirCartaPorLote($loteId)
    {
        $asignaciones = Asignacion::with(['activo.tipo', 'activo.marca', 'empleado.puesto', 'empleado.departamento', 'estadoEntrega'])
                        ->where('lote_id', $loteId)->get();

        if($asignaciones->isEmpty()) abort(404);

        $empleado = $asignaciones->first()->empleado;
        $pdf = Pdf::loadView('pdf.carta_responsiva', compact('asignaciones', 'empleado'));
        return $pdf->stream('Responsiva_Lote_' . substr($loteId,0,8) . '.pdf');
    }

    public function imprimirCarta($id)
    {
        $asignacion = Asignacion::with(['activo.tipo', 'activo.marca', 'empleado.puesto', 'empleado.departamento', 'estadoEntrega'])
                    ->findOrFail($id);
        
        $asignaciones = collect([$asignacion]); 
        $empleado = $asignacion->empleado;

        $pdf = Pdf::loadView('pdf.carta_responsiva', compact('asignaciones', 'empleado'));
        return $pdf->stream('Responsiva_' . $asignacion->id . '.pdf');
    }

    public function imprimirCartaDevolucion($id)
    {
        $asignacion = Asignacion::with(['activo.tipo', 'activo.marca', 'empleado.departamento', 'estadoDevolucion'])
                    ->findOrFail($id);
        
        if (!$asignacion->fecha_devolucion) return back()->with('error', 'No devuelto aún.');

        $pdf = Pdf::loadView('pdf.carta_devolucion', compact('asignacion'));
        return $pdf->stream('Devolucion_' . $asignacion->empleado->numero_empleado . '.pdf');
    }
}