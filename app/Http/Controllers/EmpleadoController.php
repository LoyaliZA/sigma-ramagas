<?php

namespace App\Http\Controllers;

use App\Models\CatalogoDepartamento;
use App\Models\CatalogoUbicacion;
use App\Models\CatalogoPuesto;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // Importante para las fotos
use Barryvdh\DomPDF\Facade\Pdf; // Importante para el PDF

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
                'foto' => 'nullable|image|max:2048' // Max 2MB
            ]);

            $data = $request->except('foto');

            // Subida de Foto
            if ($request->hasFile('foto')) {
                // Guardar en storage/app/public/empleados (Requiere: php artisan storage:link)
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
        // Cargamos relaciones extra para el modal de Ver Detalles
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
            $request->validate([
                'nombre' => 'required|string|max:100',
                'apellido_paterno' => 'required|string|max:100',
                'estatus' => 'required',
                'foto' => 'nullable|image|max:2048',
                // Validaciones condicionales para Baja
                'fecha_baja' => 'required_if:estatus,Baja|nullable|date',
                'motivo_baja' => 'required_if:estatus,Baja|nullable|string'
            ]);

            // Excluimos 'numero_empleado' para que no se modifique y 'foto' para tratarla manual
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