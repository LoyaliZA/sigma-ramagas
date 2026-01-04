<?php

namespace App\Http\Controllers;

use App\Models\CatalogoDepartamento;
use App\Models\CatalogoUbicacion;
use App\Models\CatalogoPuesto;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf; // Asegúrate de tener instalado dompdf

class EmpleadoController extends Controller
{
    public function index()
    {
        $empleados = Empleado::with(['departamento', 'ubicacion', 'puesto'])->orderBy('created_date', 'desc')->get();
        $departamentos = CatalogoDepartamento::orderBy('nombre')->get();
        $ubicaciones = CatalogoUbicacion::orderBy('nombre')->get();
        $puestos = CatalogoPuesto::orderBy('nombre')->get();
        
        return view('empleados.index', compact('empleados', 'departamentos', 'ubicaciones', 'puestos'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'numero_empleado' => 'required|string|max:50|unique:empleado',
                'nombre' => 'required|string|max:100',
                'apellido_paterno' => 'required|string|max:100',
                'puesto_id' => 'required|integer',
                'departamento_id' => 'required|integer',
                'planta_id' => 'required|integer',
                'estatus' => 'required',
                'foto' => 'nullable|image|max:2048' // Validación de imagen
            ]);

            $data = $request->except('foto');

            // Manejo de la foto
            if ($request->hasFile('foto')) {
                $path = $request->file('foto')->store('empleados', 'public');
                $data['foto_url'] = $path;
            }

            $empleado = Empleado::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Empleado creado exitosamente.',
                'empleado' => $empleado->load(['departamento', 'ubicacion', 'puesto'])
            ], 201);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function show(Empleado $empleado)
    {
        // Cargamos también las asignaciones activas y los datos del activo para el Modal Ver
        $empleado->load([
            'departamento', 
            'ubicacion', 
            'puesto', 
            'asignacionesActivas.activo.tipo', 
            'asignacionesActivas.activo.marca'
        ]);
        return response()->json($empleado);
    }

    public function update(Request $request, Empleado $empleado)
    {
        try {
            // Validamos todo MENOS numero_empleado (es inmodificable)
            $request->validate([
                'nombre' => 'required|string|max:100',
                'estatus' => 'required',
                // Si el estatus es Baja, validamos motivo y fecha
                'motivo_baja' => 'required_if:estatus,Baja',
                'fecha_baja' => 'required_if:estatus,Baja|date|nullable',
                'foto' => 'nullable|image|max:2048'
            ]);

            // Excluimos numero_empleado para protegerlo
            $data = $request->except(['numero_empleado', 'foto']);

            // Manejo de Foto (Reemplazo)
            if ($request->hasFile('foto')) {
                // Borrar foto anterior si existe
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
                // Si lo reactivan, limpiamos datos de baja
                $data['fecha_baja'] = null;
                $data['motivo_baja'] = null;
            }

            $empleado->update($data);

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

    public function generarHistorialPdf($id)
    {
        $empleado = Empleado::with([
            'departamento', 
            'ubicacion', 
            'puesto',
            // Traemos TODAS las asignaciones (historial), ordenadas por fecha reciente
            'asignaciones' => function($query) {
                $query->orderBy('fecha_asignacion', 'desc');
            },
            'asignaciones.activo.tipo',
            'asignaciones.activo.marca',
            'asignaciones.estadoEntrega' // Para saber condición
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.historial_empleado', compact('empleado'));
        
        // Configuramos papel carta vertical
        return $pdf->setPaper('letter', 'portrait')
                   ->stream('Historial_Activos_' . $empleado->numero_empleado . '.pdf');
    }

    public function destroy(Empleado $empleado)
    {
        // Validar que no tenga activos asignados antes de borrar
        if($empleado->asignacionesActivas()->count() > 0){
             return response()->json(['success' => false, 'message' => 'No se puede eliminar: El empleado tiene activos asignados.'], 422);
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