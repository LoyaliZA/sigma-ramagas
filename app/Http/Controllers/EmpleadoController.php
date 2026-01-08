<?php

namespace App\Http\Controllers;

use App\Models\CatalogoDepartamento;
use App\Models\CatalogoUbicacion;
use App\Models\CatalogoPuesto;
use App\Models\Empleado;
use App\Models\EmpleadoContacto; // <--- [IMPORTANTE] Nuevo modelo
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; // <--- [IMPORTANTE] Para transacciones
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
                    // 'numero_empleado' => ... ELIMINADO (Lo generamos nosotros)
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

                // --- LÓGICA DE AUTOGENERACIÓN (RMA-XXX) ---
                // Buscamos el último que empiece con RMA-, ordenado por longitud y luego valor 
                // para evitar problemas entre RMA-9 y RMA-10. Usamos lockForUpdate para evitar duplicados.
                $ultimo = Empleado::where('numero_empleado', 'LIKE', 'RMA-%')
                                  ->orderByRaw('LENGTH(numero_empleado) DESC')
                                  ->orderBy('numero_empleado', 'DESC')
                                  ->lockForUpdate()
                                  ->first();

                $consecutivo = 1;
                if ($ultimo) {
                    // Extraemos los dígitos después del guion
                    $partes = explode('-', $ultimo->numero_empleado);
                    if (isset($partes[1]) && is_numeric($partes[1])) {
                        $consecutivo = intval($partes[1]) + 1;
                    }
                }
                
                // Formateamos a 3 dígitos (001, 002...)
                $data['numero_empleado'] = 'RMA-' . str_pad($consecutivo, 3, '0', STR_PAD_LEFT);
                // ------------------------------------------

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
        // Cargamos relaciones extra para el modal de Ver Detalles
        $empleado->load([
            'departamento', 
            'ubicacion', 
            'puesto', 
            'contactos', // [Nuevo] Cargamos los contactos extra
            'asignacionesActivas.activo.tipo', 
            'asignacionesActivas.activo.marca'
        ]);
        return response()->json($empleado);
    }

    public function update(Request $request, Empleado $empleado)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:100',
                'apellido_paterno' => 'required|string|max:100',
                'estatus' => 'required',
                'foto' => 'nullable|image|max:2048',
                // Validaciones condicionales para Baja
                'fecha_baja' => 'required_if:estatus,Baja|nullable|date',
                'motivo_baja' => 'required_if:estatus,Baja|nullable|string',
                // Validamos que el código de empresa sea único (excluyendo al empleado actual)
                'codigo_empresa' => 'nullable|string|max:50|unique:empleado,codigo_empresa,' . $empleado->id,
            ]);

            // Excluimos datos sensibles o manuales
            $data = $request->except(['numero_empleado', 'foto']);

            // Lógica de Foto (Reemplazo)
            if ($request->hasFile('foto')) {
                // Borrar anterior si existe
                if ($empleado->foto_url && Storage::disk('public')->exists($empleado->foto_url)) {
                    Storage::disk('public')->delete($empleado->foto_url);
                }
                $path = $request->file('foto')->store('empleados', 'public');
                $data['foto_url'] = $path;
            }

            // Lógica de Baja
            if ($request->estatus === 'Baja') {
                $data['fecha_baja'] = $request->fecha_baja;
                $data['motivo_baja'] = $request->motivo_baja;
            } else {
                // Si lo reactivan, limpiamos la baja
                $data['fecha_baja'] = null;
                $data['motivo_baja'] = null;
            }

            $empleado->update($data);

            // NOTA: La actualización de contactos se hará en un endpoint separado o 
            // en una futura actualización del modal "Editar". Por ahora solo actualizamos datos base.

            return response()->json([
                'success' => true,
                'message' => 'Empleado actualizado exitosamente.',
                'empleado' => $empleado->load(['departamento', 'ubicacion', 'puesto'])
            ]);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // Generar PDF de historial
    public function generarHistorialPdf($id)
    {
        $empleado = Empleado::with([
            'departamento', 
            'ubicacion', 
            'puesto',
            'contactos', // [Nuevo] Incluimos contactos en el reporte
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
        // Validar que no tenga activos antes de borrar
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
}