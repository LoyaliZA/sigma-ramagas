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
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ActivoController extends Controller
{
    public function index(Request $request)
    {
        // ... (KPIs y Query principal se mantienen igual) ...
        $kpiTotal = Activo::where('estado_id', '!=', 6)->count();
        $kpiEnUso = Activo::where('estado_id', 2)->count();
        $kpiDisponibles = Activo::where('estado_id', 1)->count();
        $kpiMantenimiento = Activo::whereIn('estado_id', [3, 4])->count();

        $query = Activo::with(['tipo', 'marca', 'estado', 'ubicacion'])->where('estado_id', '!=', 6);

        if ($request->filled('q')) {
            $busqueda = $request->input('q');
            $query->where(function ($q) use ($busqueda) {
                $q->where('codigo_interno', 'like', "%$busqueda%")
                    ->orWhere('numero_serie', 'like', "%$busqueda%")
                    ->orWhere('modelo', 'like', "%$busqueda%")
                    ->orWhereHas('marca', fn($qM) => $qM->where('nombre', 'like', "%$busqueda%"))
                    ->orWhereHas('tipo', fn($qT) => $qT->where('nombre', 'like', "%$busqueda%"));
            });
        }

        if ($request->filled('tipo_id'))
            $query->where('tipo_id', $request->tipo_id);
        if ($request->filled('estado_id'))
            $query->where('estado_id', $request->estado_id);

        $limit = $request->input('limit', 10);
        $activos = $query->orderBy('created_date', 'desc')->paginate($limit)->appends($request->query());

        $condiciones = CatalogoCondicion::whereIn('nombre', [
            'Nuevo',
            'Funcional',
            'Detalles estéticos'
        ])->orderBy('nombre')->get();

        $tipos = CatalogoTipoActivo::orderBy('nombre')->get();
        $marcas = CatalogoMarca::orderBy('nombre')->get();
        $estados = CatalogoEstadoActivo::where('id', '!=', 6)->orderBy('nombre')->get();
        $ubicaciones = CatalogoUbicacion::orderBy('nombre')->get();
        $tiposRam = CatalogoTipoRam::orderBy('nombre')->get();
        $tiposDisco = CatalogoTipoAlmacenamiento::orderBy('nombre')->get();
        $motivosBaja = CatalogoMotivoBaja::orderBy('nombre')->get();

        return view('activos.index', compact(
            'activos',
            'limit',
            'tipos',
            'marcas',
            'estados',
            'ubicaciones',
            'condiciones',
            'tiposRam',
            'tiposDisco',
            'motivosBaja',
            'kpiTotal',
            'kpiEnUso',
            'kpiDisponibles',
            'kpiMantenimiento'
        ));
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
                'imagen' => 'nullable|image|max:5120',
            ]);

            $data = $request->except(['imagen', 'especificaciones']);

            // Specs
            $specs = [];
            if ($request->filled('cpu_modelo'))
                $specs['procesador'] = $request->cpu_modelo;

            if ($request->filled('ram_capacidad')) {
                $tipoRamNombre = $request->ram_tipo_texto;
                if (!$tipoRamNombre && $request->ram_tipo_id) {
                    $tipoRamNombre = CatalogoTipoRam::find($request->ram_tipo_id)?->nombre;
                }
                $specs['ram'] = trim($request->ram_capacidad . ' ' . $request->ram_unidad . ' ' . $tipoRamNombre);
            }

            if ($request->filled('disco_capacidad')) {
                $tipoDiscoNombre = $request->disco_tipo_texto;
                if (!$tipoDiscoNombre && $request->disco_tipo_id) {
                    $tipoDiscoNombre = CatalogoTipoAlmacenamiento::find($request->disco_tipo_id)?->nombre;
                }
                $specs['almacenamiento'] = trim($request->disco_capacidad . ' ' . $request->disco_unidad . ' ' . $tipoDiscoNombre);
            }

            if ($request->filled('imei'))
                $specs['imei'] = $request->imei;
            if ($request->filled('pantalla_tamano'))
                $specs['pantalla'] = $request->pantalla_tamano;
            if ($request->filled('so_version'))
                $specs['sistema_operativo'] = $request->so_version;
            if ($request->filled('spec_otras'))
                $specs['otras'] = $request->spec_otras;

            $data['especificaciones'] = $specs;

            // Procesamiento de Imagen
            if ($request->hasFile('imagen')) { 
                $data['foto'] = $this->procesarImagen($request->file('imagen'));
            }

            // CREAR ACTIVO
            $activo = Activo::create($data);

            // [LOG] Registrar Creación
            $this->logAction(
                'Creación de Activo', 
                'activo', 
                $activo->id, 
                null, // No hay anterior
                $activo->toArray() // Nuevo
            );

            return response()->json([
                'success' => true,
                'message' => 'Activo registrado: ' . $activo->codigo_interno
            ], 201);

        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function show(Request $request, $id)
    {
        $activo = Activo::with(['tipo', 'marca', 'estado', 'ubicacion', 'motivoBaja'])->findOrFail($id);

        if ($request->wantsJson()) {
            return response()->json($activo);
        }

        if ($request->ajax()) {
            return view('activos.modal_ver', compact('activo'))->render();
        }

        return response()->json($activo);
    }

    public function update(Request $request, $id)
    {
        $activo = Activo::findOrFail($id);

        if ($activo->estado_id == 6) {
            return response()->json(['success' => false, 'message' => 'No se puede editar un activo dado de baja.'], 403);
        }

        try {
            $request->validate([
                'numero_serie' => 'required|string|max:100|unique:activo,numero_serie,' . $activo->id,
                'estado_id' => 'required|integer',
                'imagen' => 'nullable|image|max:5120',
            ]);

            if ($request->estado_id == 6) {
                return response()->json(['success' => false, 'message' => 'Use el botón "Dar de Baja" para retirar el equipo.'], 422);
            }

            // [LOG] Capturar valor anterior
            $valoresAnteriores = $activo->toArray();

            $data = $request->except(['codigo_interno', 'imagen', 'especificaciones']);

            // Reconstruir specs
            $specs = $activo->especificaciones ?? [];
            if ($request->filled('cpu_modelo'))
                $specs['procesador'] = $request->cpu_modelo;
            if ($request->filled('imei'))
                $specs['imei'] = $request->imei;
            if ($request->filled('pantalla_tamano'))
                $specs['pantalla'] = $request->pantalla_tamano;
            if ($request->filled('spec_otras'))
                $specs['otras'] = $request->spec_otras;
            if ($request->filled('so_version'))
                $specs['sistema_operativo'] = $request->so_version;

            if ($request->filled('ram_capacidad'))
                $specs['ram'] = $request->ram_capacidad . ' ' . $request->ram_unidad;
            if ($request->filled('disco_capacidad'))
                $specs['almacenamiento'] = $request->disco_capacidad . ' ' . $request->disco_unidad;

            $data['especificaciones'] = $specs;

            // Actualizar Imagen
            if ($request->hasFile('imagen')) { 
                if (isset($activo) && $activo->foto) {
                    Storage::disk('public')->delete($activo->foto);
                }
                $data['foto'] = $this->procesarImagen($request->file('imagen'));
            }

            $activo->update($data);

            // [LOG] Registrar Edición
            // Usamos fresh() para asegurar que obtenemos los datos tal cual quedaron en BD
            $this->logAction(
                'Edición de Activo', 
                'activo', 
                $activo->id, 
                $valoresAnteriores, 
                $activo->fresh()->toArray()
            );

            return response()->json(['success' => true, 'message' => 'Actualizado correctamente.']);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function bajas(Request $request)
    {
        $query = Activo::with(['tipo', 'marca', 'motivoBaja', 'ubicacion'])
            ->where('estado_id', 6);

        if ($request->filled('q')) {
            $busqueda = $request->input('q');
            $query->where(function ($q) use ($busqueda) {
                $q->where('codigo_interno', 'like', "%$busqueda%")
                    ->orWhere('numero_serie', 'like', "%$busqueda%");
            });
        }

        $activos = $query->orderBy('fecha_baja', 'desc')->paginate(10);
        return view('activos.bajas', compact('activos'));
    }

    public function darBaja(Request $request, $id)
    {
        try {
            $request->validate([
                'motivo_baja_id' => 'required|exists:catalogo_motivosbaja,id',
                'comentarios' => 'required|string|min:5'
            ]);

            $activo = Activo::findOrFail($id);

            if ($activo->estado_id == 2) {
                return response()->json(['success' => false, 'message' => 'El activo está ASIGNADO. Registre la devolución primero.'], 409);
            }

            // [LOG] Capturar valor anterior
            $valoresAnteriores = $activo->toArray();

            $activo->estado_id = 6;
            $activo->motivo_baja_id = $request->motivo_baja_id;
            $activo->observaciones .= "\n[BAJA " . Carbon::now()->format('d/m/Y') . "]: " . $request->comentarios;
            $activo->fecha_baja = Carbon::now();
            $activo->save();

            // [LOG] Registrar Baja
            $this->logAction(
                'Baja de Activo', 
                'activo', 
                $activo->id, 
                $valoresAnteriores, 
                $activo->toArray()
            );

            return response()->json(['success' => true, 'message' => 'Baja procesada correctamente.']);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function storeCatalogo(Request $request)
    {
        try {
            $request->validate(['tipo_catalogo' => 'required', 'nombre' => 'required']);

            $modelo = match ($request->tipo_catalogo) {
                'marca' => new CatalogoMarca(),
                'tipo' => new CatalogoTipoActivo(),
                'ubicacion' => new CatalogoUbicacion(),
                'ram_tipo' => new CatalogoTipoRam(),
                'disco_tipo' => new CatalogoTipoAlmacenamiento(),
                'motivo_baja' => new CatalogoMotivoBaja(),
                default => null
            };

            if (!$modelo)
                return response()->json(['success' => false, 'message' => 'Catálogo inválido'], 400);

            if (DB::table($modelo->getTable())->where('nombre', $request->nombre)->exists()) {
                return response()->json(['success' => false, 'message' => 'Ya existe.'], 422);
            }

            $modelo->nombre = $request->nombre;
            if ($request->tipo_catalogo == 'motivo_baja')
                $modelo->comentarios_baja = '';

            $modelo->save();

            // [LOG] Registrar creación en catálogo rápido (Modal de Activos)
            $this->logAction(
                'Creación Rápida Catálogo', 
                $modelo->getTable(), 
                $modelo->id, 
                null, 
                $modelo->toArray()
            );

            return response()->json(['success' => true, 'data' => $modelo]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error interno.'], 500);
        }
    }

    public function quick_add(Request $request)
    {
        return $this->storeCatalogo($request);
    }

    private function procesarImagen($file)
    {
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);
        $image->scale(width: 800);
        $encoded = $image->toWebp(quality: 80);
        $filename = 'activos/activo_' . uniqid() . '.webp';
        Storage::disk('public')->put($filename, (string) $encoded);
        return $filename;
    }
}