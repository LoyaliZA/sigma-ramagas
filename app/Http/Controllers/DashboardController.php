<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activo;
use App\Models\Empleado; // Importante: Agregamos el modelo Empleado
use App\Models\Asignacion;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Tarjetas Superiores (KPIs)
        $totalActivos = Activo::count();
        $activosAsignados = Activo::where('estado_id', 2)->count(); // 2 = En Uso
        $activosDisponibles = Activo::where('estado_id', 1)->count(); // 1 = Disponible
        
        // CAMBIO SOLICITADO: Contar Empleados Activos en lugar de sumar costos
        $empleadosActivos = Empleado::where('estatus', 'Activo')->count();

        // 2. Datos para el Gráfico de Pastel (Distribución por Tipo)
        $distribucionTipos = Activo::join('catalogo_tiposactivo', 'activo.tipo_id', '=', 'catalogo_tiposactivo.id')
            ->select('catalogo_tiposactivo.nombre', DB::raw('count(*) as total'))
            ->groupBy('catalogo_tiposactivo.nombre')
            ->get();

        // 3. Datos para el Timeline de Actividades Recientes
        // Cargamos relaciones 'tipo' y 'marca' para que el texto sea descriptivo (ej. "Laptop Dell")
        $actividadesRecientes = Asignacion::with(['empleado', 'activo.tipo', 'activo.marca'])
            ->orderBy('fecha_asignacion', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'totalActivos', 
            'activosAsignados', 
            'activosDisponibles', 
            'empleadosActivos',     // Nueva variable enviada a la vista
            'distribucionTipos',
            'actividadesRecientes'
        ));
    }
}