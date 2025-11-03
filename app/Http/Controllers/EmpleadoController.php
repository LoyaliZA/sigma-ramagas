<?php

namespace App\Http\Controllers;

use App\Models\CatalogoDepartamento;
use App\Models\CatalogoUbicacion;
use App\Models\CatalogoPuesto; // <-- NUEVO
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmpleadoController extends Controller
{
    public function index()
    {
        $empleados = Empleado::with(['departamento', 'ubicacion', 'puesto'])->get(); // <-- ACTUALIZADO
        $departamentos = CatalogoDepartamento::orderBy('nombre')->get();
        $ubicaciones = CatalogoUbicacion::orderBy('nombre')->get();
        $puestos = CatalogoPuesto::orderBy('nombre')->get(); // <-- NUEVO
        
        return view('empleados.index', compact('empleados', 'departamentos', 'ubicaciones', 'puestos')); // <-- ACTUALIZADO
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'numero_empleado' => 'required|string|max:50|unique:empleado',
                'nombre' => 'required|string|max:100',
                'apellido_paterno' => 'required|string|max:100', // <-- CAMBIO
                'apellido_materno' => 'nullable|string|max:100', // <-- CAMBIO
                'correo' => 'nullable|email|max:255|unique:empleado',
                'estatus' => 'required|string|max:20',
                'fecha_ingreso' => 'nullable|date',
                'departamento_id' => 'required|integer|exists:catalogo_departamentos,id',
                'planta_id' => 'required|integer|exists:catalogo_ubicaciones,id',
                'puesto_id' => 'required|integer|exists:catalogo_puestos,id', // <-- CAMBIO
            ]);

            $empleado = Empleado::create($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Empleado creado exitosamente.',
                'empleado' => $empleado->load(['departamento', 'ubicacion', 'puesto']) // <-- ACTUALIZADO
            ], 201);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al crear el empleado.'], 500);
        }
    }

    public function show(Empleado $empleado)
    {
        try {
            return response()->json($empleado->load(['departamento', 'ubicacion', 'puesto'])); // <-- ACTUALIZADO
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => 'Empleado no encontrado.'], 404);
        }
    }

    public function update(Request $request, Empleado $empleado)
    {
        try {
            $request->validate([
                'numero_empleado' => 'required|string|max:50|unique:empleado,numero_empleado,' . $empleado->id,
                'nombre' => 'required|string|max:100',
                'apellido_paterno' => 'required|string|max:100', // <-- CAMBIO
                'apellido_materno' => 'nullable|string|max:100', // <-- CAMBIO
                'correo' => 'nullable|email|max:255|unique:empleado,correo,' . $empleado->id,
                'estatus' => 'required|string|max:20',
                'fecha_ingreso' => 'nullable|date',
                'departamento_id' => 'required|integer|exists:catalogo_departamentos,id',
                'planta_id' => 'required|integer|exists:catalogo_ubicaciones,id',
                'puesto_id' => 'required|integer|exists:catalogo_puestos,id', // <-- CAMBIO
            ]);

            $empleado->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Empleado actualizado exitosamente.',
                'empleado' => $empleado->load(['departamento', 'ubicacion', 'puesto']) // <-- ACTUALIZADO
            ]);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al actualizar el empleado.'], 500);
        }
    }

    public function destroy(Empleado $empleado)
    {
        try {
            $empleado->delete();
            return response()->json(['success' => true, 'message' => 'Empleado eliminado exitosamente.']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al eliminar el empleado.'], 500);
        }
    }
}
