<?php

namespace App\Http\Controllers;

use App\Models\CatalogoDepartamento;
use App\Models\CatalogoUbicacion;
use App\Models\CatalogoPuesto;
use App\Models\Empleado;
use App\Models\EmpleadoContacto;
use App\Models\EmpleadoDocumento; // <--- [NUEVO] Importado
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
                    'foto' => 'nullable|image|max:2048',
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

                if ($request->hasFile('foto')) {
                    $path = $request->file('foto')->store('empleados', 'public');
                    $data['foto_url'] = $path;
                }

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
            'documentos', // <--- [NUEVO] Cargamos el expediente
            'asignacionesActivas.activo.tipo', 
            'asignacionesActivas.activo.marca'
        ]);
        return response()->json($empleado);
    }

    public function update(Request $request, Empleado $empleado)
    {
        try {
            return DB::transaction(function () use ($request, $empleado) {
                
                $request->validate([
                    'nombre' => 'required|string|max:100',
                    'apellido_paterno' => 'required|string|max:100',
                    'estatus' => 'required',
                    'foto' => 'nullable|image|max:2048',
                    'fecha_baja' => 'required_if:estatus,Baja|nullable|date',
                    'motivo_baja' => 'required_if:estatus,Baja|nullable|string',
                    'codigo_empresa' => 'nullable|string|max:50|unique:empleado,codigo_empresa,' . $empleado->id,
                    'contactos' => 'nullable|array',
                ]);

                $data = $request->except(['numero_empleado', 'foto', 'contactos']);

                if ($request->hasFile('foto')) {
                    if ($empleado->foto_url && Storage::disk('public')->exists($empleado->foto_url)) {
                        Storage::disk('public')->delete($empleado->foto_url);
                    }
                    $path = $request->file('foto')->store('empleados', 'public');
                    $data['foto_url'] = $path;
                }

                if ($request->estatus === 'Baja') {
                    $data['fecha_baja'] = $request->fecha_baja;
                    $data['motivo_baja'] = $request->motivo_baja;
                } else {
                    $data['fecha_baja'] = null;
                    $data['motivo_baja'] = null;
                }

                $empleado->update($data);

                // Actualización de contactos (Borrar y Recrear)
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
                } else {
                     if ($request->has('contactos') && empty($request->contactos)) {
                        $empleado->contactos()->delete();
                     }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Empleado actualizado exitosamente.',
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

    // --- NUEVOS MÉTODOS PARA EXPEDIENTE DIGITAL ---

    public function subirDocumento(Request $request, $id)
    {
        try {
            $request->validate([
                'tipo_documento' => 'required|string',
                'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:1024', // Max 1MB
            ]);

            $empleado = Empleado::findOrFail($id);

            if ($request->hasFile('archivo')) {
                $file = $request->file('archivo');
                // Nombre: TIPO_RMA-XXX_TIMESTAMP.ext
                // Reemplazamos espacios por guiones bajos
                $safeName = str_replace(' ', '_', $request->tipo_documento);
                $filename = strtoupper($safeName) . '_' . $empleado->numero_empleado . '_' . time() . '.' . $file->getClientOriginalExtension();
                
                // Guardar en storage/app/public/expedientes/{id}/
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
            
            // Eliminar archivo físico
            if (Storage::disk('public')->exists($doc->ruta_archivo)) {
                Storage::disk('public')->delete($doc->ruta_archivo);
            }
            
            // Eliminar registro BD
            $doc->delete();
            
            return response()->json(['success' => true, 'message' => 'Documento eliminado correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar.'], 500);
        }
    }
}