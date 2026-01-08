<?php

namespace App\Http\Controllers;

use App\Models\Activo;
use App\Models\CatalogoTipoActivo;
use App\Models\CatalogoMarca;
use App\Models\CatalogoEstadoActivo;
use App\Models\CatalogoUbicacion;
use App\Models\CatalogoCondicion;
use App\Models\CatalogoTipoRam;
use App\Models\CatalogoTipoAlmacenamiento;
use App\Models\CatalogoMotivoBaja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActivoController extends Controller
{
    /**
     * Muestra el inventario activo (excluyendo bajas definitivas).
     */
    public function index(Request $request)
    {
        // 1. KPIs (Tarjetas Superiores)
        // Excluimos estado 6 (Baja) de las métricas operativas
        $kpiTotal = Activo::where('estado_id', '!=', 6)->count();
        $kpiEnUso = Activo::where('estado_id', 2)->count(); // 2 = En Uso
        $kpiDisponibles = Activo::where('estado_id', 1)->count(); // 1 = Disponible
        $kpiMantenimiento = Activo::whereIn('estado_id', [3, 4])->count(); // 3 y 4 = Mantenimiento/Diagnóstico

        // 2. Consulta Principal (Solo activos VIVOS)
        $query = Activo::with(['tipo', 'marca', 'estado', 'ubicacion'])
                       ->where('estado_id', '!=', 6);

        // Filtro de Búsqueda Inteligente
        if ($request->filled('q')) {
            $busqueda = $request->input('q');
            $query->where(function($q) use ($busqueda) {
                $q->where('codigo_interno', 'like', "%$busqueda%") // Prioridad al código RMA
                  ->orWhere('numero_serie', 'like', "%$busqueda%")
                  ->orWhere('modelo', 'like', "%$busqueda%")
                  ->orWhereHas('marca', fn($qM) => $qM->where('nombre', 'like', "%$busqueda%"))
                  ->orWhereHas('tipo', fn($qT) => $qT->where('nombre', 'like', "%$busqueda%"));
            });
        }

        // Filtros laterales
        if ($request->filled('tipo_id')) $query->where('tipo_id', $request->tipo_id);
        if ($request->filled('estado_id')) $query->where('estado_id', $request->estado_id);

        // Paginación y Orden (Recientes primero)
        $limit = $request->input('limit', 10);
        $activos = $query->orderBy('created_date', 'desc') // Usamos fecha de registro
                         ->paginate($limit)
                         ->appends($request->query());
        
        // 3. Carga de Catálogos para Modales y Filtros
        $tipos = CatalogoTipoActivo::orderBy('nombre')->get();
        $marcas = CatalogoMarca::orderBy('nombre')->get();
        $estados = CatalogoEstadoActivo::where('id', '!=', 6)->orderBy('nombre')->get(); // Ocultamos "Baja" del selector manual
        $ubicaciones = CatalogoUbicacion::orderBy('nombre')->get();
        $condiciones = CatalogoCondicion::orderBy('nombre')->get();
        
        $tiposRam = CatalogoTipoRam::orderBy('nombre')->get();
        $tiposDisco = CatalogoTipoAlmacenamiento::orderBy('nombre')->get();
        $motivosBaja = CatalogoMotivoBaja::orderBy('nombre')->get(); // Para el modal de baja definitiva

        return view('activos.index', compact(
            'activos', 'limit', 
            'tipos', 'marcas', 'estados', 'ubicaciones', 'condiciones', 
            'tiposRam', 'tiposDisco', 'motivosBaja',
            'kpiTotal', 'kpiEnUso', 'kpiDisponibles', 'kpiMantenimiento'
        ));
    }

    /**
     * Muestra SOLO los activos dados de baja (El Cementerio).
     */
    public function bajas(Request $request)
    {
        $query = Activo::with(['tipo', 'marca', 'motivoBaja', 'ubicacion'])
                       ->where('estado_id', 6); // Solo ID 6 = Baja Definitiva

        if ($request->filled('q')) {
            $busqueda = $request->input('q');
            $query->where(function($q) use ($busqueda) {
                $q->where('codigo_interno', 'like', "%$busqueda%")
                  ->orWhere('numero_serie', 'like', "%$busqueda%");
            });
        }

        // Ordenamos por fecha de defunción (baja)
        $activos = $query->orderBy('fecha_baja', 'desc')->paginate(10);
        
        return view('activos.bajas', compact('activos'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'numero_serie' => 'required|string|max:100|unique:activo',
                'tipo_id' => 'required|integer',
                'marca_id' => 'required|integer',
                'estado_id' => 'required|integer',
                'ubicacion_id' => 'required|integer',
                'condicion_id' => 'required|integer',
            ]);

            $data = $request->all();
            
            // --- Construcción de JSON de Especificaciones ---
            $specs = [];
            
            // Computo
            if($request->filled('cpu_modelo')) $specs['procesador'] = $request->cpu_modelo;
            
            // RAM (Dinámico)
            if($request->filled('ram_capacidad')) {
                $tipoRamNombre = $request->ram_tipo_texto;
                // Si no viene el texto pero sí el ID, lo buscamos
                if(!$tipoRamNombre && $request->ram_tipo_id) {
                    $tipoRamNombre = CatalogoTipoRam::find($request->ram_tipo_id)?->nombre;
                }
                $specs['ram'] = trim($request->ram_capacidad . ' ' . $request->ram_unidad . ' ' . $tipoRamNombre);
            }

            // Almacenamiento (Dinámico)
            if($request->filled('disco_capacidad')) {
                $tipoDiscoNombre = $request->disco_tipo_texto;
                if(!$tipoDiscoNombre && $request->disco_tipo_id) {
                    $tipoDiscoNombre = CatalogoTipoAlmacenamiento::find($request->disco_tipo_id)?->nombre;
                }
                $specs['almacenamiento'] = trim($request->disco_capacidad . ' ' . $request->disco_unidad . ' ' . $tipoDiscoNombre);
            }

            // Móviles
            if($request->filled('imei')) $specs['imei'] = $request->imei;
            if($request->filled('pantalla_tamano')) $specs['pantalla'] = $request->pantalla_tamano;

            // Generales
            if($request->filled('so_version')) $specs['sistema_operativo'] = $request->so_version;
            if($request->filled('spec_otras')) $specs['otras'] = $request->spec_otras;

            $data['especificaciones'] = $specs;

            // Registrar usuario creador si hay sesión
            if(auth()->check()) {
                $data['user_id'] = auth()->id();
            }

            // El modelo se encarga de generar el codigo_interno automáticamente
            $activo = Activo::create($data);

            return response()->json([
                'success' => true, 
                'message' => 'Activo registrado: ' . $activo->codigo_interno
            ], 201);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // Busca la función 'show' y cámbiala por esta:
    public function show(Request $request, $id)
    {
        // 1. Cargamos las relaciones (incluyendo 'empleado' que acabamos de arreglar)
        $activo = Activo::with(['tipo', 'marca', 'estado', 'ubicacion', 'empleado', 'motivoBaja'])->findOrFail($id);

        // 2. Si la petición viene del Modal (AJAX), devolvemos el HTML pintado
        if ($request->ajax()) {
            return view('activos.modal_ver', compact('activo'))->render();
        }

        // 3. Si no es AJAX, devolvemos JSON (por compatibilidad)
        return response()->json($activo);
    }

    public function update(Request $request, Activo $activo)
    {
        // 1. Bloqueo de seguridad: No editar si YA está dado de baja definitiva
        if($activo->estado_id == 6) {
            return response()->json([
                'success' => false, 
                'message' => 'Acción denegada. Este activo está dado de baja definitiva y no se puede editar.'
            ], 403);
        }

        try {
            $request->validate([
                'numero_serie' => 'required|string|max:100|unique:activo,numero_serie,' . $activo->id,
                'estado_id' => 'required|integer' // Validamos que llegue un estado
            ]);

            // 2. VALIDACIÓN DE REGLAS DE NEGOCIO
            // Si intentan poner "Baja" (6) manualmente desde el select, lo bloqueamos
            // porque deben usar el botón rojo de "Dar de Baja" para que se guarde el motivo y fecha.
            if ($request->estado_id == 6) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Para dar de baja un equipo, por favor utiliza el botón rojo "Dar de Baja" en la lista.'
                ], 422);
            }

            // 3. Permitimos actualizar todo MENOS el código interno (ese es sagrado)
            // Ya quitamos 'estado_id' de aquí para que SÍ se guarde el cambio a Mantenimiento/Disponible
            $data = $request->except(['codigo_interno']); 
            
            // Reconstruir specs (Lógica igual a antes)
            $specs = $activo->especificaciones ?? [];
            
            if($request->filled('cpu_modelo')) $specs['procesador'] = $request->cpu_modelo;
            if($request->filled('imei')) $specs['imei'] = $request->imei;
            if($request->filled('pantalla_tamano')) $specs['pantalla'] = $request->pantalla_tamano;
            if($request->filled('spec_otras')) $specs['otras'] = $request->spec_otras;
            
            if($request->filled('ram_capacidad')) {
                $tipoRamNombre = $request->ram_tipo_texto;
                if(!$tipoRamNombre && $request->ram_tipo_id) {
                    $tipoRamNombre = CatalogoTipoRam::find($request->ram_tipo_id)?->nombre;
                }
                $specs['ram'] = trim($request->ram_capacidad . ' ' . $request->ram_unidad . ' ' . $tipoRamNombre);
            }

            if($request->filled('disco_capacidad')) {
                $tipoDiscoNombre = $request->disco_tipo_texto;
                if(!$tipoDiscoNombre && $request->disco_tipo_id) {
                    $tipoDiscoNombre = CatalogoTipoAlmacenamiento::find($request->disco_tipo_id)?->nombre;
                }
                $specs['almacenamiento'] = trim($request->disco_capacidad . ' ' . $request->disco_unidad . ' ' . $tipoDiscoNombre);
            }

            $data['especificaciones'] = $specs;
            
            $activo->update($data);

            return response()->json(['success' => true, 'message' => 'Información y estado actualizados correctamente.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Proceso de Baja Definitiva.
     * Cambia el estado a 6, guarda motivo, fecha y comentarios.
     */
    public function darBaja(Request $request, $id)
    {
        try {
            $request->validate([
                'motivo_baja_id' => 'required|exists:catalogo_motivosbaja,id',
                'comentarios' => 'required|string|min:5'
            ]);

            $activo = Activo::findOrFail($id);
            
            // Validación Lógica: No matar lo que está vivo y trabajando
            if($activo->estado_id == 2) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Imposible dar de baja: El activo está ASIGNADO. Primero debe registrar la devolución.'
                ], 409);
            }

            // Aplicar Baja
            $activo->estado_id = 6; // ID 6 = Baja Definitiva
            $activo->motivo_baja_id = $request->motivo_baja_id;
            
            // Agregamos el comentario de baja al historial de observaciones para no perderlo
            $notaBaja = "\n[BAJA DEFINITIVA " . Carbon::now()->format('d/m/Y') . "]: " . $request->comentarios;
            $activo->observaciones = $activo->observaciones . $notaBaja;
            
            $activo->fecha_baja = Carbon::now();
            $activo->save();

            return response()->json(['success' => true, 'message' => 'El activo ha sido dado de baja correctamente.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Activo $activo)
    {
        try {
            // Este método elimina físicamente el registro.
            // Se recomienda usar darBaja() para mantener historial, pero dejamos este
            // por si se creó un registro por error y se quiere borrar "de verdad".
            $activo->delete();
            return response()->json(['success' => true, 'message' => 'Registro eliminado permanentemente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar.'], 500);
        }
    }

    // --- Métodos Auxiliares para Quick Add (Modales) ---

    public function storeCatalogo(Request $request)
    {
        try {
            $request->validate(['tipo_catalogo' => 'required', 'nombre' => 'required']);
            
            $modelo = match($request->tipo_catalogo) {
                'marca' => new CatalogoMarca(),
                'tipo' => new CatalogoTipoActivo(),
                'ubicacion' => new CatalogoUbicacion(),
                'ram_tipo' => new CatalogoTipoRam(),
                'disco_tipo' => new CatalogoTipoAlmacenamiento(),
                // Agregamos motivo_baja por si quieren crear motivos al vuelo
                'motivo_baja' => new CatalogoMotivoBaja(), 
                default => null
            };

            if (!$modelo) return response()->json(['success' => false, 'message' => 'Catálogo inválido'], 400);
            
            // Evitar duplicados
            if (DB::table($modelo->getTable())->where('nombre', $request->nombre)->exists()) {
                return response()->json(['success' => false, 'message' => 'Este elemento ya existe en el catálogo.'], 422);
            }

            $modelo->nombre = $request->nombre;
            // Si es motivo de baja, a veces requieren comentario default, lo dejamos vacío por ahora
            if($request->tipo_catalogo == 'motivo_baja') {
                $modelo->comentarios_baja = ''; 
            }
            
            $modelo->save();

            return response()->json(['success' => true, 'data' => $modelo]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error interno.'], 500);
        }
    }
    
    public function quick_add(Request $request) {
        return $this->storeCatalogo($request);
    }
}