@extends('layouts.app')

@section('title', 'Directorio de Empleados - SIGMA')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3">Directorio de Empleados</h1>
            <p class="mb-0 text-muted">Gestión de personal y asignaciones</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoEmpleado">
            <i class="bi bi-plus-circle-fill me-2"></i>
            Nuevo Empleado
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Lista de Empleados ({{ $empleados->count() }})</h5>
                <div class="w-50">
                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar por nombre, número de empleado o puesto...">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">No. Empleado</th>
                            <th scope="col">Nombre Completo</th>
                            <th scope="col">Puesto</th>
                            <th scope="col">Departamento</th>
                            <th scope="col">Estatus</th>
                            <th scope="col" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="empleadosTableBody">
                        @forelse ($empleados as $empleado)
                            <tr id="empleadoRow-{{ $empleado->id }}">
                                <td><strong>{{ $empleado->numero_empleado }}</strong></td>
                                <td>{{ $empleado->nombre_completo }}</td>
                                <td>{{ $empleado->puesto->nombre ?? 'N/A' }}</td>
                                <td>{{ $empleado->departamento->nombre ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge status-badge status-{{ strtolower($empleado->estatus) }}">
                                        {{ $empleado->estatus }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-secondary border-0" title="Ver Detalles" onclick="verEmpleado('{{ $empleado->id }}')">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary border-0" title="Editar" onclick="editarEmpleado('{{ $empleado->id }}')">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr id="noEmpleadosRow">
                                <td colspan="6" class="text-center text-muted">No hay empleados registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('empleados.modal_nuevo')
    @include('empleados.modal_ver')
    @include('empleados.modal_editar')

@endsection

@push('scripts')
<script>
    // Usamos 'var' en lugar de 'const' para evitar errores de "already declared"
    var modalNuevoEmpleado = new bootstrap.Modal(document.getElementById('modalNuevoEmpleado'));
    var modalVerEmpleado = new bootstrap.Modal(document.getElementById('modalVerEmpleado'));
    var modalEditarEmpleado = new bootstrap.Modal(document.getElementById('modalEditarEmpleado'));
    var formNuevoEmpleado = document.getElementById('formNuevoEmpleado');
    var formEditarEmpleado = document.getElementById('formEditarEmpleado');
    
    // YA NO declaramos 'csrfToken' aquí, porque ya existe (fue declarado en app.blade.php)

    formNuevoEmpleado.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('{{ route('empleados.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken, // Usamos la variable global
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();

            if (!response.ok) {
                // Si la respuesta no es OK, lanzamos un error con los mensajes de validación
                let errorMessages = 'Error desconocido.';
                if (response.status === 422 && result.errors) {
                    // Si es un error de validación 422, formateamos los errores
                    errorMessages = Object.values(result.errors).map(err => err.join('\n')).join('\n');
                } else if (result.message) {
                    errorMessages = result.message;
                }
                throw new Error(errorMessages);
            }

            if (result.success) {
                modalNuevoEmpleado.hide();
                formNuevoEmpleado.reset();
                agregarFilaEmpleado(result.empleado);
            } else {
                alert('Error al crear empleado: ' + (result.message || 'Error desconocido'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Ocurrió un error al guardar el empleado:\n\n' + error.message);
        }
    });
    
    async function verEmpleado(id) {
        try {
            const response = await fetch(`/empleados/${id}`, {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error('Empleado no encontrado');
            
            const empleado = await response.json();

            document.getElementById('verNombreCompleto').textContent = `${empleado.nombre} ${empleado.apellido_paterno} ${empleado.apellido_materno || ''}`;
            document.getElementById('verPuesto').textContent = empleado.puesto ? empleado.puesto.nombre : 'N/A';
            document.getElementById('verNumeroEmpleado').textContent = empleado.numero_empleado;
            document.getElementById('verEstatus').innerHTML = `<span class="badge status-badge status-${empleado.estatus.toLowerCase()}">${empleado.estatus}</span>`;
            document.getElementById('verDepartamento').textContent = empleado.departamento ? empleado.departamento.nombre : 'N/A';
            document.getElementById('verUbicacion').textContent = empleado.ubicacion ? empleado.ubicacion.nombre : 'N/A';
            document.getElementById('verCorreo').textContent = empleado.correo || 'N/A';
            document.getElementById('verFechaIngreso').textContent = empleado.fecha_ingreso ? new Date(empleado.fecha_ingreso + 'T12:00:00Z').toLocaleDateString('es-MX', { day: '2-digit', month: 'long', year: 'numeric' }) : 'N/A';

            modalVerEmpleado.show();
        } catch (error) {
            console.error('Error:', error);
            alert('No se pudo cargar la información del empleado.');
        }
    }

    async function editarEmpleado(id) {
        try {
            const response = await fetch(`/empleados/${id}`, {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error('Empleado no encontrado');
            
            const empleado = await response.json();

            document.getElementById('editId').value = empleado.id;
            document.getElementById('editNumeroEmpleado').value = empleado.numero_empleado;
            document.getElementById('editNombre').value = empleado.nombre;
            document.getElementById('editApellidoPaterno').value = empleado.apellido_paterno;
            document.getElementById('editApellidoMaterno').value = empleado.apellido_materno;
            document.getElementById('editPuestoId').value = empleado.puesto_id;
            document.getElementById('editCorreo').value = empleado.correo;
            document.getElementById('editEstatus').value = empleado.estatus;
            document.getElementById('editFechaIngreso').value = empleado.fecha_ingreso;
            document.getElementById('editDepartamentoId').value = empleado.departamento_id;
            document.getElementById('editPlantaId').value = empleado.planta_id;
            
            formEditarEmpleado.action = `/empleados/${empleado.id}`;
            
            modalEditarEmpleado.show();
        } catch (error) {
            console.error('Error:', error);
            alert('No se pudo cargar la información del empleado para editar.');
        }
    }
    
    formEditarEmpleado.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        const id = document.getElementById('editId').value;

        try {
            const response = await fetch(`/empleados/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken, // Usamos la variable global
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            
            if (!response.ok) {
                let errorMessages = 'Error desconocido.';
                if (response.status === 422 && result.errors) {
                    errorMessages = Object.values(result.errors).map(err => err.join('\n')).join('\n');
                } else if (result.message) {
                    errorMessages = result.message;
                }
                throw new Error(errorMessages);
            }

            if (result.success) {
                modalEditarEmpleado.hide();
                actualizarFilaEmpleado(result.empleado);
            } else {
                alert('Error al actualizar empleado: ' + (result.message || 'Error desconocido'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Ocurrió un error al actualizar el empleado:\n\n' + error.message);
        }
    });

    function agregarFilaEmpleado(empleado) {
        // Remover el mensaje de "No hay empleados" si existe
        const noEmpleadosRow = document.getElementById('noEmpleadosRow');
        if (noEmpleadosRow) {
            noEmpleadosRow.remove();
        }

        const fila = `
            <tr id="empleadoRow-${empleado.id}">
                <td><strong>${empleado.numero_empleado}</strong></td>
                <td>${empleado.nombre} ${empleado.apellido_paterno} ${empleado.apellido_materno || ''}</td>
                <td>${empleado.puesto ? empleado.puesto.nombre : 'N/A'}</td>
                <td>${empleado.departamento ? empleado.departamento.nombre : 'N/A'}</td>
                <td>
                    <span class="badge status-badge status-${empleado.estatus.toLowerCase()}">
                        ${empleado.estatus}
                    </span>
                </td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-secondary border-0" title="Ver Detalles" onclick="verEmpleado('${empleado.id}')">
                        <i class="bi bi-eye-fill"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-primary border-0" title="Editar" onclick="editarEmpleado('${empleado.id}')">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                </td>
            </tr>
        `;
        document.getElementById('empleadosTableBody').insertAdjacentHTML('beforeend', fila);
    }
    
    function actualizarFilaEmpleado(empleado) {
        const fila = document.getElementById(`empleadoRow-${empleado.id}`);
        if (fila) {
            fila.innerHTML = `
                <td><strong>${empleado.numero_empleado}</strong></td>
                <td>${empleado.nombre} ${empleado.apellido_paterno} ${empleado.apellido_materno || ''}</td>
                <td>${empleado.puesto ? empleado.puesto.nombre : 'N/A'}</td>
                <td>${empleado.departamento ? empleado.departamento.nombre : 'N/A'}</td>
                <td>
                    <span class="badge status-badge status-${empleado.estatus.toLowerCase()}">
                        ${empleado.estatus}
                    </span>
                </td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-secondary border-0" title="Ver Detalles" onclick="verEmpleado('${empleado.id}')">
                        <i class="bi bi-eye-fill"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-primary border-0" title="Editar" onclick="editarEmpleado('${empleado.id}')">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                </td>
            `;
        }
    }

    document.getElementById('searchInput').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = document.getElementById('empleadosTableBody').getElementsByTagName('tr');
        
        for (let row of rows) {
            // Omitir la fila de "no hay empleados" del filtro
            if (row.id === 'noEmpleadosRow') continue;

            const cells = row.getElementsByTagName('td');
            let found = false;
            for (let i = 0; i < cells.length; i++) {
                if (i === 0 || i === 1 || i === 2) { 
                    if (cells[i].textContent.toLowerCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
            }
            row.style.display = found ? '' : 'none';
        }
    });

</script>
@endpush
