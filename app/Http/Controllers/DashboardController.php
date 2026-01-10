<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activo;
use App\Models\Empleado;
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
        
        // Contar Empleados Activos
        $empleadosActivos = Empleado::where('estatus', 'Activo')->count();

        // 2. Datos para el Gráfico de Pastel
        $distribucionTipos = Activo::join('catalogo_tiposactivo', 'activo.tipo_id', '=', 'catalogo_tiposactivo.id')
            ->select('catalogo_tiposactivo.nombre', DB::raw('count(*) as total'))
            ->groupBy('catalogo_tiposactivo.nombre')
            ->get();

        // 3. Timeline de Actividades Recientes
        $actividadesRecientes = Asignacion::with(['empleado', 'activo.tipo', 'activo.marca'])
            ->orderBy('fecha_asignacion', 'desc')
            ->take(5)
            ->get();

        // [OPCIONAL] BITÁCORA DE ACCESO
        // Descomenta la siguiente línea si quieres registrar cada visita al dashboard.
        // Advertencia: Esto generará muchos registros en la tabla bitacora_cambios.
        
        $this->logAction('Acceso', 'Dashboard', 0, null, ['mensaje' => 'Usuario ingresó al panel principal']);

        return view('dashboard.index', compact(
            'totalActivos', 
            'activosAsignados', 
            'activosDisponibles', 
            'empleadosActivos',
            'distribucionTipos',
            'actividadesRecientes'
        ));
    }
}