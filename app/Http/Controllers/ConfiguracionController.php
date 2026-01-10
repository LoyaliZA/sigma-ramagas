<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

use App\Models\User;
use App\Models\Role;
use App\Models\BitacoraCambio;

// --- MODELOS DE CATÁLOGOS ---
use App\Models\CatalogoMarca;
use App\Models\CatalogoDepartamento;
use App\Models\CatalogoPuesto;
use App\Models\CatalogoUbicacion;
use App\Models\CatalogoTipoActivo;
use App\Models\CatalogoCondicion;
use App\Models\CatalogoEstadoActivo;
use App\Models\CatalogoEstadoAsignacion;
use App\Models\CatalogoMotivoBaja;
use App\Models\CatalogoTipoRam;
use App\Models\CatalogoTipoAlmacenamiento;

class ConfiguracionController extends Controller
{
    /**
     * Menú principal de configuración
     */
    public function index()
    {
        return view('configuracion.index');
    }

    /**
     * Sección de Usuarios: Listado
     */
    public function usuarios()
    {
        $users = User::with('roles')->get();
        $roles = Role::all();
        return view('configuracion.usuarios', compact('users', 'roles'));
    }

    /**
     * Crear Usuario
     */
    public function storeUsuario(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Asignar rol
            $user->roles()->attach($request->role_id);
            
            // [LOG] Registro de nuevo usuario
            // Guardamos nombre del rol para que sea legible en el log
            $rolNombre = Role::find($request->role_id)->nombre;
            
            $datosNuevos = $user->toArray();
            $datosNuevos['rol_asignado'] = $rolNombre;

            $this->logAction('Creación de Usuario', 'users', $user->id, null, $datosNuevos);
        });

        return redirect()->route('configuracion.usuarios')->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Editar Usuario
     */
    public function updateUsuario(Request $request, $id)
    {
        $user = User::with('roles')->findOrFail($id);
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role_id' => ['required', 'exists:roles,id'],
        ]);
        
        // [LOG] Capturar datos ANTES de editar
        $oldValues = $user->toArray();
        $oldValues['rol_actual'] = $user->roles->first()->nombre ?? 'Sin rol';

        DB::transaction(function () use ($request, $user, $oldValues) {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($request->password)]);
            }

            // Actualizar rol
            $user->roles()->sync([$request->role_id]);
            
            // [LOG] Capturar datos DESPUÉS de editar
            $newValues = $user->fresh()->toArray();
            $newValues['rol_nuevo'] = Role::find($request->role_id)->nombre;

            $this->logAction('Edición de Usuario', 'users', $user->id, $oldValues, $newValues);
        });

        return redirect()->route('configuracion.usuarios')->with('success', 'Usuario actualizado.');
    }

    /**
     * Eliminar Usuario
     */
    public function destroyUsuario($id)
    {
        $user = User::with('roles')->findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        // [LOG] Capturar datos ANTES de eliminar
        $oldValues = $user->toArray();
        $oldValues['rol'] = $user->roles->first()->nombre ?? 'Sin rol';
        
        $user->delete();
        
        // [LOG] Registro de eliminación
        $this->logAction('Eliminación de Usuario', 'users', $id, $oldValues, null);

        return redirect()->route('configuracion.usuarios')->with('success', 'Usuario eliminado.');
    }

    /**
     * Sección de Bitácora (Lectura)
     */
    public function bitacora(Request $request)
    {
        // Solo lectura, pero podríamos loguear quién consultó la bitácora si quisiéramos ser muy estrictos
        
        $query = BitacoraCambio::with('usuario')->orderBy('created_at', 'desc');

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        if ($request->filled('usuario_id')) {
            $query->where('user_id', $request->usuario_id);
        }

        $logs = $query->paginate(20);
        $usuarios = User::all();

        return view('configuracion.bitacora', compact('logs', 'usuarios'));
    }

    // --- LÓGICA DINÁMICA DE CATÁLOGOS ---

    private function getCatalogoConfig($key)
    {
        $map = [
            'marcas' => ['model' => CatalogoMarca::class, 'title' => 'Marcas', 'icon' => 'bi-tag-fill', 'restricted' => false],
            'departamentos' => ['model' => CatalogoDepartamento::class, 'title' => 'Departamentos', 'icon' => 'bi-building-fill', 'restricted' => false],
            'puestos' => ['model' => CatalogoPuesto::class, 'title' => 'Puestos', 'icon' => 'bi-person-badge-fill', 'restricted' => false],
            'ubicaciones' => ['model' => CatalogoUbicacion::class, 'title' => 'Ubicaciones Físicas', 'icon' => 'bi-geo-alt-fill', 'restricted' => false],
            'tipos_activo' => ['model' => CatalogoTipoActivo::class, 'title' => 'Tipos de Activo', 'icon' => 'bi-pc-display', 'restricted' => true],
            'condiciones' => ['model' => CatalogoCondicion::class, 'title' => 'Condiciones Físicas', 'icon' => 'bi-heart-pulse-fill', 'restricted' => false],
            'estados_activo' => ['model' => CatalogoEstadoActivo::class, 'title' => 'Estados del Activo', 'icon' => 'bi-toggle-on', 'restricted' => true],
            'estados_asignacion' => ['model' => CatalogoEstadoAsignacion::class, 'title' => 'Estados de Asignación', 'icon' => 'bi-check-circle-fill', 'restricted' => true],
            'motivos_baja' => ['model' => CatalogoMotivoBaja::class, 'title' => 'Motivos de Baja', 'icon' => 'bi-trash3-fill', 'restricted' => false],
            'tipos_ram' => ['model' => CatalogoTipoRam::class, 'title' => 'Tipos de RAM', 'icon' => 'bi-memory', 'restricted' => false],
            'tipos_almacenamiento' => ['model' => CatalogoTipoAlmacenamiento::class, 'title' => 'Tipos de Almacenamiento', 'icon' => 'bi-hdd-fill', 'restricted' => false],
        ];

        return $map[$key] ?? null;
    }

    public function catalogos($cat = 'marcas')
    {
        $config = $this->getCatalogoConfig($cat);
        
        if (!$config) {
            return redirect()->route('configuracion.catalogos', 'marcas');
        }

        $data = $config['model']::orderBy('nombre')->get();
        
        $menuKeys = array_keys([
            'marcas' => '', 'departamentos' => '', 'puestos' => '', 'ubicaciones' => '',
            'tipos_activo' => '', 'condiciones' => '', 'estados_activo' => '', 
            'estados_asignacion' => '', 'motivos_baja' => '', 'tipos_ram' => '', 
            'tipos_almacenamiento' => ''
        ]);

        return view('configuracion.catalogos', [
            'activeCat' => $cat,
            'config' => $config,
            'data' => $data,
            'menuKeys' => $menuKeys
        ]);
    }

    public function storeCatalogo(Request $request, $cat)
    {
        $config = $this->getCatalogoConfig($cat);
        $request->validate(['nombre' => 'required|string|max:255']);

        $item = $config['model']::create(['nombre' => $request->nombre]);

        // [LOG] Registro de nuevo elemento en catálogo
        $this->logAction(
            'Creación en Catálogo', 
            "catalogo_$cat", 
            $item->id, 
            null, 
            $item->toArray()
        );

        return back()->with('success', 'Elemento agregado correctamente.');
    }

    public function updateCatalogo(Request $request, $cat, $id)
    {
        $config = $this->getCatalogoConfig($cat);
        $item = $config['model']::findOrFail($id);
        
        $request->validate(['nombre' => 'required|string|max:255']);

        // [LOG] Capturar antes
        $old = $item->toArray();

        $item->update(['nombre' => $request->nombre]);

        // [LOG] Registro de edición
        $this->logAction(
            'Edición en Catálogo', 
            "catalogo_$cat", 
            $item->id, 
            $old, 
            $item->fresh()->toArray()
        );

        return back()->with('success', 'Elemento actualizado correctamente.');
    }

    public function destroyCatalogo($cat, $id)
    {
        $config = $this->getCatalogoConfig($cat);
        $item = $config['model']::findOrFail($id);

        if ($config['restricted']) {
            return back()->with('error', 'Este catálogo es esencial para el sistema y no se pueden eliminar elementos.');
        }

        // [LOG] Capturar antes
        $old = $item->toArray();
        
        try {
            $item->delete();
            
            // [LOG] Registro de eliminación
            $this->logAction(
                'Eliminación en Catálogo', 
                "catalogo_$cat", 
                $id, 
                $old, 
                null
            );
            
            return back()->with('success', 'Elemento eliminado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar este elemento porque está siendo usado en activos o empleados.');
        }
    }

    public function resetCatalogos()
    {
        try {
            Artisan::call('db:seed', ['--class' => 'CatalogosSeeder']);
            
            // [LOG] Registro de RESET COMPLETO (Acción Crítica)
            $this->logAction(
                'RESET DE FÁBRICA', 
                'SISTEMA', 
                0, 
                null, 
                ['mensaje' => 'Se restauraron todos los catálogos a valores originales. ATENCIÓN: Acción irreversible ejecutada.']
            );
            
            return redirect()->route('configuracion.catalogos', 'marcas')
                ->with('success', '¡Todos los catálogos han sido restaurados a sus valores originales!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al restaurar: ' . $e->getMessage());
        }
    }
}