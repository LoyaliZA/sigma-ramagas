<?php

namespace App\Http\Controllers;

use Intervention\Image\Laravel\Facades\Image;
use App\Models\CatalogoDepartamento;
use App\Models\CatalogoUbicacion;
use App\Models\CatalogoPuesto;
use App\Models\Empleado;
use App\Models\EmpleadoContacto;
use App\Models\EmpleadoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class EmpleadoController extends Controller
{
    public function index()
    {
        $empleados = Empleado::with(['departamento', 'ubicacion', 'puesto'])
                             ->orderBy('created_date', 'desc')
                             ->get();

        $departamentos = CatalogoDepartamento::orderBy('nombre')->get();
        $ubicaciones = CatalogoUbicacion::orderBy('nombre')->get();
        $puestos = CatalogoPuesto::orderBy('nombre')->get();
        
        return view('empleados.index', compact('empleados', 'departamentos', 'ubicaciones', 'puestos'));
    }

    public function store(Request $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                
                $request->validate([
                    'codigo_empresa'  => 'nullable|string|max:50|unique:empleado',
                    'nombre' => 'required|string|max:100',
                    'apellido_paterno' => 'required|string|max:100',
                    'puesto_id' => 'required|integer',
                    'departamento_id' => 'required|integer',
                    'planta_id' => 'required|integer',
                    'estatus' => 'required',
                    'foto' => 'nullable|image|max:10240', // 10MB Máx
                    'contactos' => 'nullable|array',
                ]);

                $data = $request->except('foto', 'contactos');

                // Autogeneración RMA-XXX
                $ultimo = Empleado::where('numero_empleado', 'LIKE', 'RMA-%')
                                  ->orderByRaw('LENGTH(numero_empleado) DESC')
                                  ->orderBy('numero_empleado', 'DESC')
                                  ->lockForUpdate()
                                  ->first();

                $consecutivo = 1;
                if ($ultimo) {
                    $partes = explode('-', $ultimo->numero_empleado);
                    if (isset($partes[1]) && is_numeric($partes[1])) {
                        $consecutivo = intval($partes[1]) + 1;
                    }
                }
                $data['numero_empleado'] = 'RMA-' . str_pad($consecutivo, 3, '0', STR_PAD_LEFT);

                // --- COMPRESIÓN DE IMAGEN (VERSIÓN 3) ---
                if ($request->hasFile('foto')) {
                    $file = $request->file('foto');
                    $filename = 'empleados/' . uniqid() . '.jpg';
                    
                    // Leer imagen
                    $image = Image::read($file);
                    
                    // Redimensionar a 800px de ancho (mantiene aspecto)
                    $image->scale(width: 800);
                    
                    // Guardar en Storage como JPG calidad 75
                    Storage::disk('public')->put($filename, $image->toJpeg(75));
                    
                    $data['foto_url'] = $filename;
                }
                // ----------------------------------------

                $empleado = Empleado::create($data);

                if ($request->has('contactos')) {
                    foreach ($request->contactos as $contacto) {
                        if (!empty($contacto['valor'])) {
                            EmpleadoContacto::create([
                                'empleado_id' => $empleado->id,
                                'tipo' => $contacto['tipo'],
                                'valor' => $contacto['valor'],
                                'descripcion' => $contacto['descripcion'] ?? null,
                            ]);
                        }
                    }
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Empleado creado: ' . $empleado->numero_empleado,
                    'empleado' => $empleado->load(['departamento', 'ubicacion', 'puesto'])
                ], 201);
            });

        } catch (\Exception $e) {
            Log::error("Error store empleado: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function show(Empleado $empleado)
    {
        $empleado->load([
            'departamento', 
            'ubicacion', 
            'puesto', 
            'contactos', 
            'documentos',
            'asignacionesActivas.activo.tipo', 
            'asignacionesActivas.activo.marca'
        ]);
        return response()->json($empleado);
    }

    public function update(Request $request, Empleado $empleado)
    {
        try {
            if ($request->estatus === 'Baja' && $empleado->asignacionesActivas()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'NO SE PUEDE DAR DE BAJA: Tiene activos asignados. Registre devolución primero.'
                ], 422);
            }

            return DB::transaction(function () use ($request, $empleado) {
                
                $request->validate([
                    'nombre' => 'required|string|max:100',
                    'apellido_paterno' => 'required|string|max:100',
                    'estatus' => 'required',
                    'foto' => 'nullable|image|max:10240',
                    'codigo_empresa' => 'nullable|string|max:50|unique:empleado,codigo_empresa,' . $empleado->id,
                    'contactos' => 'nullable|array',
                ]);

                $data = $request->except(['numero_empleado', 'foto', 'contactos']);

                // --- COMPRESIÓN DE IMAGEN (VERSIÓN 3) ---
                if ($request->hasFile('foto')) {
                    if ($empleado->foto_url && Storage::disk('public')->exists($empleado->foto_url)) {
                        Storage::disk('public')->delete($empleado->foto_url);
                    }

                    $file = $request->file('foto');
                    $filename = 'empleados/' . uniqid() . '.jpg';

                    $image = Image::read($file);
                    $image->scale(width: 800);
                    Storage::disk('public')->put($filename, $image->toJpeg(75));
                    
                    $data['foto_url'] = $filename;
                }
                // ----------------------------------------

                if ($request->estatus === 'Baja') {
                    $data['fecha_baja'] = $request->fecha_baja;
                    $data['motivo_baja'] = $request->motivo_baja;
                } else {
                    $data['fecha_baja'] = null;
                    $data['motivo_baja'] = null;
                }

                $empleado->update($data);

                if ($request->has('contactos')) {
                    $empleado->contactos()->delete();
                    foreach ($request->contactos as $contacto) {
                        if (!empty($contacto['valor'])) {
                            EmpleadoContacto::create([
                                'empleado_id' => $empleado->id,
                                'tipo' => $contacto['tipo'],
                                'valor' => $contacto['valor'],
                                'descripcion' => $contacto['descripcion'] ?? null,
                            ]);
                        }
                    }
                } elseif ($request->has('contactos') && empty($request->contactos)) {
                    $empleado->contactos()->delete();
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Actualizado correctamente.',
                    'empleado' => $empleado->load(['departamento', 'ubicacion', 'puesto'])
                ]);
            });

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function generarHistorialPdf($id)
    {
        $empleado = Empleado::with([
            'departamento', 
            'ubicacion', 
            'puesto',
            'contactos', 
            'asignaciones' => function($query) {
                $query->orderBy('fecha_asignacion', 'desc');
            },
            'asignaciones.activo.tipo',
            'asignaciones.activo.marca',
            'asignaciones.estadoEntrega'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.historial_empleado', compact('empleado'));
        
        return $pdf->setPaper('letter', 'portrait')
                   ->stream('Historial_' . $empleado->numero_empleado . '.pdf');
    }

    public function destroy(Empleado $empleado)
    {
        // Doble seguridad: tampoco permitir borrar si tiene activos
        if($empleado->asignacionesActivas()->count() > 0){
             return response()->json(['success' => false, 'message' => 'No se puede eliminar: Tiene activos asignados.'], 422);
        }

        try {
            if ($empleado->foto_url && Storage::disk('public')->exists($empleado->foto_url)) {
                Storage::disk('public')->delete($empleado->foto_url);
            }
            $empleado->delete();
            return response()->json(['success' => true, 'message' => 'Empleado eliminado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar.'], 500);
        }
    }

    public function subirDocumento(Request $request, $id)
    {
        try {
            $request->validate([
                'tipo_documento' => 'required|string',
                'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:1024',
            ]);

            $empleado = Empleado::findOrFail($id);

            if ($request->hasFile('archivo')) {
                $file = $request->file('archivo');
                $safeName = str_replace(' ', '_', $request->tipo_documento);
                $filename = strtoupper($safeName) . '_' . $empleado->numero_empleado . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('expedientes/' . $empleado->id, $filename, 'public');

                $empleado->documentos()->create([
                    'nombre' => $filename,
                    'ruta_archivo' => $path,
                    'tipo_documento' => $request->tipo_documento,
                    'subido_por' => auth()->id()
                ]);

                return response()->json(['success' => true, 'message' => 'Documento subido correctamente.']);
            }
            return response()->json(['success' => false, 'message' => 'No se envió ningún archivo.'], 400);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al subir: ' . $e->getMessage()], 500);
        }
    }

    public function eliminarDocumento($id)
    {
        try {
            $doc = EmpleadoDocumento::findOrFail($id);
            
            if (Storage::disk('public')->exists($doc->ruta_archivo)) {
                Storage::disk('public')->delete($doc->ruta_archivo);
            }
            $doc->delete();
            return response()->json(['success' => true, 'message' => 'Documento eliminado correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar.'], 500);
        }
    }
}