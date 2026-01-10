@extends('layouts.app')

@section('title', 'Gestión de Catálogos')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold">Catálogos del Sistema</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('configuracion.index') }}">Configuración</a></li>
                    <li class="breadcrumb-item active">{{ $config['title'] }}</li>
                </ol>
            </nav>
        </div>
        
        <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmarReset()">
            <i class="bi bi-arrow-counterclockwise me-1"></i>Restaurar Valores de Fábrica
        </button>
        <form id="form-reset" action="{{ route('configuracion.catalogos.reset') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>

    <div class="row g-4">
        {{-- Menú Lateral --}}
        <div class="col-lg-3">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold text-muted small text-uppercase">
                    Seleccionar Catálogo
                </div>
                <div class="list-group list-group-flush" style="max-height: 70vh; overflow-y: auto;">
                    @php
                        $labels = [
                            'marcas' => 'Marcas', 'departamentos' => 'Departamentos', 'puestos' => 'Puestos',
                            'ubicaciones' => 'Ubicaciones', 'tipos_activo' => 'Tipos de Activo',
                            'condiciones' => 'Condiciones', 'estados_activo' => 'Estados Activo',
                            'estados_asignacion' => 'Estados Asignación', 'motivos_baja' => 'Motivos Baja',
                            'tipos_ram' => 'Tipos RAM', 'tipos_almacenamiento' => 'Tipos Almacenamiento'
                        ];
                        
                        $icons = [
                            'marcas' => 'bi-tag', 'departamentos' => 'bi-building', 'puestos' => 'bi-person-badge',
                            'ubicaciones' => 'bi-geo-alt', 'tipos_activo' => 'bi-pc-display',
                            'condiciones' => 'bi-heart-pulse', 'estados_activo' => 'bi-toggle-on',
                            'estados_asignacion' => 'bi-check-circle', 'motivos_baja' => 'bi-trash3',
                            'tipos_ram' => 'bi-memory', 'tipos_almacenamiento' => 'bi-hdd'
                        ];
                    @endphp

                    @foreach($menuKeys as $key)
                        <a href="{{ route('configuracion.catalogos', $key) }}" 
                           class="list-group-item list-group-item-action d-flex align-items-center {{ $activeCat == $key ? 'active fw-bold' : 'text-secondary' }}">
                            <i class="bi {{ $icons[$key] ?? 'bi-table' }} me-3"></i>
                            {{ $labels[$key] ?? ucfirst($key) }}
                            @if($key == 'tipos_activo' || $key == 'estados_activo' || $key == 'estados_asignacion')
                                <i class="bi bi-exclamation-triangle-fill text-warning ms-auto" title="Catálogo Delicado"></i>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Área Principal --}}
        <div class="col-lg-9">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <span class="avatar-sm bg-soft-primary text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi {{ $config['icon'] }} fs-5"></i>
                        </span>
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">{{ $config['title'] }}</h5>
                            @if($config['restricted'])
                                <span class="badge bg-warning text-dark mt-1"><i class="bi bi-shield-lock me-1"></i>Catálogo Esencial del Sistema</span>
                            @else
                                <span class="badge bg-light text-muted mt-1">{{ count($data) }} registros</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    {{-- Formulario de Agregar --}}
                    <form action="{{ route('configuracion.catalogos.store', $activeCat) }}" method="POST" class="row g-2 mb-4 align-items-end p-3 bg-light rounded-3 border border-dashed">
                        @csrf
                        <div class="col-md-9">
                            <label class="form-label small fw-bold text-muted">AGREGAR NUEVO ELEMENTO</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Escribe el nombre aquí..." required>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-plus-lg me-1"></i>Agregar
                            </button>
                        </div>
                    </form>

                    {{-- Tabla de Elementos --}}
                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th style="width: 60px;">ID</th>
                                    <th>Nombre</th>
                                    <th class="text-end" style="width: 150px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                    <tr>
                                        <td class="text-muted small">{{ $item->id }}</td>
                                        <td>
                                            <form id="form-edit-{{ $item->id }}" action="{{ route('configuracion.catalogos.update', [$activeCat, $item->id]) }}" method="POST" class="d-flex gap-2 align-items-center">
                                                @csrf
                                                @method('PUT')
                                                
                                                {{-- Input con ID único para manipulación JS --}}
                                                <input type="text" 
                                                       id="input-nombre-{{ $item->id }}"
                                                       name="nombre" 
                                                       value="{{ $item->nombre }}" 
                                                       class="form-control form-control-sm border-0 bg-transparent px-0 fw-semibold text-dark input-edit" 
                                                       style="box-shadow: none;"
                                                       readonly
                                                       ondblclick="activarEdicion({{ $item->id }})">
                                                
                                                <button type="submit" id="btn-save-{{ $item->id }}" class="btn btn-sm btn-success d-none fade-in-btn" title="Guardar Cambios">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                {{-- Botón Editar corregido --}}
                                                <button type="button" class="btn btn-sm btn-link text-secondary" 
                                                        onclick="activarEdicion({{ $item->id }})"
                                                        title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </button>

                                                @if(!$config['restricted'])
                                                    <form action="{{ route('configuracion.catalogos.destroy', [$activeCat, $item->id]) }}" method="POST" onsubmit="return confirm('¿Eliminar {{ $item->nombre }}?');" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-link text-danger" title="Eliminar">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-link text-muted opacity-50" title="Protegido por el Sistema" disabled>
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted fst-italic">
                                            No hay registros en este catálogo.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white text-muted small">
                    <i class="bi bi-info-circle me-1"></i>
                    @if($config['restricted'])
                        <span class="text-warning fw-bold">Modo Protegido:</span> Solo puedes editar o agregar. La eliminación está bloqueada.
                    @else
                        Haz clic en el lápiz o doble clic en el nombre para editar.
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Scripts mejorados --}}
<script>
    function activarEdicion(id) {
        // Seleccionar elementos por ID único
        const input = document.getElementById('input-nombre-' + id);
        const btnSave = document.getElementById('btn-save-' + id);

        if (input) {
            // Quitar readonly
            input.readOnly = false;
            
            // Cambiar estilos para indicar edición
            input.classList.remove('bg-transparent', 'border-0', 'px-0');
            input.classList.add('bg-white', 'border', 'form-control', 'p-2');
            
            // Dar foco
            input.focus();
            
            // Seleccionar todo el texto para facilitar la edición
            input.select();

            // Mostrar botón de guardar
            if (btnSave) {
                btnSave.classList.remove('d-none');
            }

            // Opcional: Agregar evento para guardar con Enter
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); // Evitar doble envío
                    document.getElementById('form-edit-' + id).submit();
                }
            });
        }
    }

    function confirmarReset() {
        Swal.fire({
            title: '¿Restaurar TODO de fábrica?',
            text: "¡Atención! Esto BORRARÁ todos los catálogos actuales y volverá a cargar los originales.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, restaurar todo',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-reset').submit();
            }
        })
    }
</script>

<style>
    .input-edit {
        transition: all 0.2s ease-in-out;
    }
    .input-edit:read-only {
        cursor: pointer;
    }
    .input-edit:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
        outline: none;
    }
    .fade-in-btn {
        animation: fadeIn 0.3s;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    /* Lista activa en sidebar */
    .list-group-item.active {
        background-color: #eff6ff;
        color: #1d4ed8;
        border-color: #eff6ff;
    }
    .list-group-item.active i {
        color: #1d4ed8;
    }
</style>
@endsection