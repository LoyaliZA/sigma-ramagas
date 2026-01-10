@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold">Gestión de Usuarios</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('configuracion.index') }}" class="text-decoration-none">Configuración</a></li>
                    <li class="breadcrumb-item active">Usuarios</li>
                </ol>
            </nav>
        </div>
        <button type="button" class="btn btn-primary shadow-sm" onclick="abrirModalCrear()">
            <i class="bi bi-person-plus-fill me-2"></i>Nuevo Usuario
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-start-4 border-success" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill text-success fs-4 me-3"></i>
                <div>
                    <strong>¡Éxito!</strong> {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-start-4 border-danger" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-4 me-3"></i>
                <div>
                    <strong>Atención:</strong> Por favor corrige los siguientes errores:
                    <ul class="mb-0 small mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-secondary small text-uppercase fw-bold">Usuario</th>
                            <th class="px-4 py-3 text-secondary small text-uppercase fw-bold">Correo Electrónico</th>
                            <th class="px-4 py-3 text-secondary small text-uppercase fw-bold">Rol Asignado</th>
                            <th class="px-4 py-3 text-secondary small text-uppercase fw-bold text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @foreach($users as $user)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-soft-primary text-primary d-flex align-items-center justify-content-center me-3 fw-bold border border-primary-subtle" 
                                         style="width: 40px; height: 40px; font-size: 1.1rem;">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $user->name }}</div>
                                        <small class="text-muted" style="font-size: 0.75rem;">ID: {{ $user->id }}</small>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-muted">
                                {{ $user->email }}
                            </td>

                            <td class="px-4 py-3">
                                @foreach($user->roles as $rol)
                                    @if($rol->nombre == 'Super Admin')
                                        <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3">
                                            <i class="bi bi-shield-lock-fill me-1"></i> {{ $rol->nombre }}
                                        </span>
                                    @else
                                        <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-3">
                                            <i class="bi bi-person-badge me-1"></i> {{ $rol->nombre }}
                                        </span>
                                    @endif
                                @endforeach
                            </td>

                            <td class="px-4 py-3 text-end">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary border-0" 
                                            title="Editar"
                                            onclick="editarUsuario({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->roles->first()->id ?? '' }}')">
                                        <i class="bi bi-pencil-square fs-5"></i>
                                    </button>
                                    
                                    @if(auth()->id() !== $user->id)
                                        <button type="button" class="btn btn-sm btn-outline-danger border-0" 
                                                title="Eliminar"
                                                onclick="confirmarEliminacion({{ $user->id }}, '{{ $user->name }}')">
                                            <i class="bi bi-trash fs-5"></i>
                                        </button>
                                        
                                        <form id="delete-form-{{ $user->id }}" action="{{ route('configuracion.usuarios.destroy', $user->id) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($users->isEmpty())
                <div class="text-center py-5">
                    <p class="text-muted">No hay usuarios registrados.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="modalUsuario" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalTitle">Nuevo Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <form id="formUsuario" action="{{ route('configuracion.usuarios.store') }}" method="POST" onsubmit="btnSubmit.disabled = true; return true;">
                <div class="modal-body p-4">
                    @csrf
                    <div id="method_put"></div> <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Nombre Completo</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                            <input type="text" name="name" id="name" class="form-control border-start-0 ps-0" placeholder="Ej: Juan Pérez" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Correo Electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" id="email" class="form-control border-start-0 ps-0" placeholder="usuario@bellaroma.mx" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Rol de Acceso</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-check"></i></span>
                            <select name="role_id" id="role_id" class="form-select border-start-0 ps-0" required>
                                <option value="" disabled selected>Seleccione un rol...</option>
                                @foreach($roles as $rol)
                                    <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr class="my-4 text-muted">

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-key"></i></span>
                            <input type="password" name="password" id="password" class="form-control border-start-0 ps-0" placeholder="••••••••">
                        </div>
                        <small class="text-primary fst-italic mt-1 d-none" id="pass_hint">
                            <i class="bi bi-info-circle me-1"></i>Deja este campo vacío si no deseas cambiar la contraseña.
                        </small>
                    </div>

                    <div class="mb-3" id="div_confirm_pass">
                        <label class="form-label fw-bold small text-muted text-uppercase">Confirmar Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-key-fill"></i></span>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control border-start-0 ps-0" placeholder="••••••••">
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="btnSubmit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-2"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Inicializar Bootstrap y referencias al DOM una vez cargado todo
        const modalEl = document.getElementById('modalUsuario');
        // Aseguramos que bootstrap esté disponible
        if (typeof bootstrap !== 'undefined') {
            var modal = new bootstrap.Modal(modalEl);
        } else {
            console.error('Error: Bootstrap no se ha cargado correctamente.');
        }

        const form = document.getElementById('formUsuario');
        const submitBtn = document.querySelector('button[name="btnSubmit"]');

        // 2. Definir funciones globales (window) para que el HTML pueda verlas
        
        // Función para abrir modal CREAR
        window.abrirModalCrear = function() {
            form.reset();
            form.action = "{{ route('configuracion.usuarios.store') }}";
            
            // Limpiar PUT y Títulos
            document.getElementById('method_put').innerHTML = '';
            document.getElementById('modalTitle').innerText = 'Nuevo Usuario';
            
            // Lógica de contraseñas
            document.getElementById('password').required = true;
            document.getElementById('password_confirmation').required = true;
            document.getElementById('div_confirm_pass').classList.remove('d-none');
            document.getElementById('pass_hint').classList.add('d-none');
            
            // Habilitar botón por si estaba deshabilitado
            if(submitBtn) submitBtn.disabled = false;

            if (modal) modal.show();
        };

        // Función para abrir modal EDITAR
        window.editarUsuario = function(id, name, email, roleId) {
            form.action = `/configuracion/usuarios/${id}`;
            document.getElementById('method_put').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            
            document.getElementById('name').value = name;
            document.getElementById('email').value = email;
            document.getElementById('role_id').value = roleId;
            
            document.getElementById('modalTitle').innerText = 'Editar Usuario';
            
            // En edición, la contraseña es opcional
            document.getElementById('password').required = false;
            document.getElementById('password_confirmation').required = false;
            
            document.getElementById('pass_hint').classList.remove('d-none');
            document.getElementById('div_confirm_pass').classList.remove('d-none');
            
            // Habilitar botón
            if(submitBtn) submitBtn.disabled = false;

            if (modal) modal.show();
        };

        // Función para confirmar eliminación
        window.confirmarEliminacion = function(userId, userName) {
            Swal.fire({
                title: '¿Eliminar usuario?',
                text: `Estás a punto de eliminar a "${userName}". Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formDelete = document.getElementById('delete-form-' + userId);
                    if(formDelete) formDelete.submit();
                }
            });
        };
    });
</script>
@endsection