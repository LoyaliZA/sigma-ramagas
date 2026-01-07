@extends('layouts.app')

@section('title', 'Directorio de Empleados - SIGMA')

@section('content')
<div class="container-fluid p-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark">Directorio de Empleados</h1>
            <p class="mb-0 text-muted small">Gestión de personal, historial y asignaciones.</p>
        </div>
        <button class="btn btn-primary px-4 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoEmpleado">
            <i class="bi bi-person-plus-fill me-2"></i>Nuevo Empleado
        </button>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="row g-3 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" id="searchInput" class="form-control border-start-0 ps-0" 
                               placeholder="Buscar por nombre, número o correo..." autocomplete="off">
                    </div>
                </div>

                <div class="col-md-3">
                    <select id="filtroDepartamento" class="form-select text-muted" aria-label="Filtrar por Departamento">
                        <option value="">Todos los Departamentos</option>
                        @foreach($departamentos as $depto)
                            <option value="{{ $depto->id }}">{{ $depto->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select id="filtroUbicacion" class="form-select text-muted" aria-label="Filtrar por Ubicación">
                        <option value="">Todas las Ubicaciones</option>
                        @foreach($ubicaciones as $ubi)
                            <option value="{{ $ubi->id }}">{{ $ubi->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-1 text-end">
                    <span class="badge bg-light text-secondary border" id="contadorRegistros">{{ $empleados->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs nav-fill mb-3 border-bottom-0" id="tabsEstatus">
        <li class="nav-item">
            <a class="nav-link active fw-bold" href="#" data-estatus="todos" onclick="cambiarTab(this)">
                <i class="bi bi-people me-2"></i>Todos
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-success" href="#" data-estatus="Activo" onclick="cambiarTab(this)">
                <i class="bi bi-check-circle me-2"></i>Activos
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="#" data-estatus="Inactivo" onclick="cambiarTab(this)">
                <i class="bi bi-pause-circle me-2"></i>Inactivos
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-danger" href="#" data-estatus="Baja" onclick="cambiarTab(this)">
                <i class="bi bi-x-circle me-2"></i>Bajas
            </a>
        </li>
    </ul>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col" class="ps-4 py-3 text-muted small text-uppercase fw-bold">Empleado</th>
                            <th scope="col" class="py-3 text-muted small text-uppercase fw-bold">Puesto / Depto</th>
                            <th scope="col" class="py-3 text-muted small text-uppercase fw-bold">Ubicación</th>
                            <th scope="col" class="py-3 text-muted small text-uppercase fw-bold">Estatus</th>
                            <th scope="col" class="text-center py-3 text-muted small text-uppercase fw-bold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="empleadosTableBody">
                        @forelse ($empleados as $empleado)
                            <tr class="empleado-row" 
                                data-nombre="{{ strtolower($empleado->nombre_completo) }}"
                                data-numero="{{ strtolower($empleado->numero_empleado) }}"
                                data-correo="{{ strtolower($empleado->correo) }}"
                                data-estatus="{{ $empleado->estatus }}" 
                                data-depto-id="{{ $empleado->departamento_id }}"
                                data-ubicacion-id="{{ $empleado->planta_id }}">
                                
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        @if($empleado->foto_url)
                                            <img src="{{ asset('storage/' . $empleado->foto_url) }}" class="rounded-circle me-3 object-fit-cover shadow-sm border" width="40" height="40">
                                        @else
                                            <div class="rounded-circle me-3 bg-soft-primary text-primary d-flex align-items-center justify-content-center fw-bold border" style="width:40px; height:40px;">
                                                {{ substr($empleado->nombre, 0, 1) }}{{ substr($empleado->apellido_paterno, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold text-dark">{{ $empleado->nombre_completo }}</div>
                                            <div class="small text-muted">{{ $empleado->numero_empleado }} | {{ $empleado->correo }}</div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td>
                                    <div class="fw-bold text-dark">{{ $empleado->puesto->nombre ?? 'Sin Puesto' }}</div>
                                    <div class="small text-muted">{{ $empleado->departamento->nombre ?? 'Sin Depto' }}</div>
                                </td>
                                
                                <td>
                                    <span class="badge bg-light text-secondary border fw-normal">
                                        <i class="bi bi-geo-alt me-1"></i>{{ $empleado->ubicacion->nombre ?? 'N/A' }}
                                    </span>
                                </td>
                                
                                <td>
                                    @php
                                        $badgeClass = match($empleado->estatus) {
                                            'Activo' => 'bg-success bg-opacity-10 text-success',
                                            'Inactivo' => 'bg-secondary bg-opacity-10 text-secondary',
                                            'Baja' => 'bg-danger bg-opacity-10 text-danger',
                                            default => 'bg-light text-dark'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} px-3 py-2 rounded-pill">
                                        {{ $empleado->estatus }}
                                    </span>
                                </td>
                                
                                <td class="text-center pe-4">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-light border" title="Ver Detalles" onclick="verEmpleado('{{ $empleado->id }}')">
                                            <i class="bi bi-eye text-primary"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light border" title="Editar" onclick="editarEmpleado('{{ $empleado->id }}')">
                                            <i class="bi bi-pencil text-secondary"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="noResultsRow">
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted mb-2"><i class="bi bi-people fs-1 opacity-25"></i></div>
                                    <p class="text-muted small">No hay empleados registrados en el sistema.</p>
                                </td>
                            </tr>
                        @endforelse
                        
                        <tr id="noSearchRow" style="display: none;">
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted mb-2"><i class="bi bi-search fs-1 opacity-25"></i></div>
                                <p class="text-muted small">No se encontraron coincidencias con los filtros actuales.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('empleados.modal_nuevo')
@include('empleados.modal_ver')
@include('empleados.modal_editar')

@endsection

@push('scripts')
<script>
    // Inicialización de Modales
    var modalNuevoEmpleado = new bootstrap.Modal(document.getElementById('modalNuevoEmpleado'));
    var modalVerEmpleado = new bootstrap.Modal(document.getElementById('modalVerEmpleado'));
    var modalEditarEmpleado = new bootstrap.Modal(document.getElementById('modalEditarEmpleado'));
    
    var formNuevoEmpleado = document.getElementById('formNuevoEmpleado');
    var formEditarEmpleado = document.getElementById('formEditarEmpleado');

    // --- ALGORITMO DE NORMALIZACIÓN (El secreto para buscar sin acentos) ---
    function limpiarTexto(texto) {
        if (!texto) return '';
        return texto
            .toString()                            // Aseguramos que sea texto
            .normalize("NFD")                      // Separa letras de acentos (á -> a + ´)
            .replace(/[\u0300-\u036f]/g, "")       // Elimina los acentos
            .toLowerCase();                        // Convierte a minúsculas
    }

    // --- LÓGICA DE FILTRADO EN VIVO ---
    
    // Variables de Estado
    let filtroTexto = '';
    let filtroDepto = '';
    let filtroUbi = '';
    let filtroEstatus = 'todos'; 

    // Referencias al DOM
    const inputSearch = document.getElementById('searchInput');
    const selectDepto = document.getElementById('filtroDepartamento');
    const selectUbi = document.getElementById('filtroUbicacion');
    const rows = document.querySelectorAll('.empleado-row');
    const noSearchRow = document.getElementById('noSearchRow');
    const contadorBadge = document.getElementById('contadorRegistros');
    const tabLinks = document.querySelectorAll('#tabsEstatus .nav-link');

    // Event Listeners (Ahora usamos limpiarTexto al capturar lo que escribes)
    inputSearch.addEventListener('input', (e) => { 
        filtroTexto = limpiarTexto(e.target.value); // <--- AQUI APLICAMOS EL ALGORITMO AL INPUT
        aplicarFiltros(); 
    });
    
    selectDepto.addEventListener('change', (e) => { filtroDepto = e.target.value; aplicarFiltros(); });
    selectUbi.addEventListener('change', (e) => { filtroUbi = e.target.value; aplicarFiltros(); });

    // Función para cambiar de Pestaña (Tab)
    function cambiarTab(elemento) {
        tabLinks.forEach(link => link.classList.remove('active', 'border-bottom', 'border-primary', 'border-3'));
        elemento.classList.add('active', 'border-bottom', 'border-primary', 'border-3');
        filtroEstatus = elemento.getAttribute('data-estatus');
        aplicarFiltros();
    }

    // Función Maestra de Filtrado
    function aplicarFiltros() {
        let visibles = 0;

        rows.forEach(row => {
            // Obtenemos los datos y les aplicamos el MISMO algoritmo de limpieza
            // Así comparamos "peras con peras" (zarate vs za)
            const nombre = limpiarTexto(row.getAttribute('data-nombre'));
            const numero = limpiarTexto(row.getAttribute('data-numero'));
            const correo = limpiarTexto(row.getAttribute('data-correo'));
            
            // Estos son IDs, no necesitan limpieza de texto
            const deptoId = row.getAttribute('data-depto-id');
            const ubiId = row.getAttribute('data-ubicacion-id');
            const estatus = row.getAttribute('data-estatus');

            // 1. Validar Texto (Ahora sí encuentra Zárate aunque escribas Zarate)
            const coincideTexto = nombre.includes(filtroTexto) || 
                                  numero.includes(filtroTexto) || 
                                  correo.includes(filtroTexto);

            // 2. Validar Depto
            const coincideDepto = filtroDepto === '' || deptoId === filtroDepto;

            // 3. Validar Ubicación
            const coincideUbi = filtroUbi === '' || ubiId === filtroUbi;

            // 4. Validar Estatus (Tab)
            const coincideEstatus = filtroEstatus === 'todos' || estatus === filtroEstatus;

            // Decisión final
            if (coincideTexto && coincideDepto && coincideUbi && coincideEstatus) {
                row.style.display = '';
                visibles++;
            } else {
                row.style.display = 'none';
            }
        });

        // Manejo de UI
        if (visibles === 0) {
            if(noSearchRow) noSearchRow.style.display = '';
        } else {
            if(noSearchRow) noSearchRow.style.display = 'none';
        }

        if(contadorBadge) contadorBadge.textContent = visibles;
    }

    // --- AQUÍ MANTENEMOS TU LÓGICA DE MODALES (CREAR, VER, EDITAR) ---
    // (Pega aquí las funciones verEmpleado, editarEmpleado y los submit de los forms
    //  que corregimos en la respuesta anterior).
    
    // ... [PEGAR EL JS DE MODALES AQUÍ] ...

    // CREAR EMPLEADO (Con Foto)
    formNuevoEmpleado.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this); // FormData captura archivos automáticamente

        try {
            const response = await fetch("{{ route('empleados.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                    // NO poner 'Content-Type': 'application/json' cuando se usa FormData
                },
                body: formData 
            });
            
            const result = await response.json();

            if (!response.ok) {
                let errorMessages = result.message || 'Error desconocido';
                if (result.errors) {
                    errorMessages = Object.values(result.errors).flat().join('\n');
                }
                throw new Error(errorMessages);
            }

            if (result.success) {
                modalNuevoEmpleado.hide();
                formNuevoEmpleado.reset();
                // Recargar página para simplificar la actualización de foto en tabla
                window.location.reload(); 
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            alert('Error al guardar:\n' + error.message);
        }
    });
    
    // VER EMPLEADO (Con Foto y Activos)
        async function verEmpleado(id) {
    try {
        const response = await fetch(`/empleados/${id}`, { headers: { 'Accept': 'application/json' } });
        if (!response.ok) throw new Error('Empleado no encontrado');
        
        const emp = await response.json();

        // 1. Llenar Datos de Texto
        document.getElementById('verNombreCompleto').textContent = emp.nombre_completo || 'Sin nombre';
        document.getElementById('verPuesto').textContent = emp.puesto?.nombre || 'N/A';
        document.getElementById('verNumeroEmpleado').textContent = emp.numero_empleado || '';
        
        // Estatus con badge
        const badgeClass = emp.estatus === 'Activo' ? 'bg-success' : (emp.estatus === 'Baja' ? 'bg-danger' : 'bg-secondary');
        document.getElementById('verEstatus').innerHTML = `<span class="badge ${badgeClass}">${emp.estatus}</span>`;
        
        document.getElementById('verDepartamento').textContent = emp.departamento?.nombre || 'N/A';
        document.getElementById('verUbicacion').textContent = emp.ubicacion?.nombre || 'N/A';
        document.getElementById('verCorreo').textContent = emp.correo || 'N/A';
        document.getElementById('verFechaIngreso').textContent = emp.fecha_ingreso || 'N/A';

        // 2. --- LÓGICA CORREGIDA FOTO ---
        const img = document.getElementById('verFoto');
        const icon = document.getElementById('verIconoDefault');

        if (emp.foto_url) {
            // Caso A: El empleado TIENE foto en BD
            img.src = `/storage/${emp.foto_url}`;
            
            // Forzamos mostrar imagen y OCULTAR icono
            img.style.display = 'block';
            icon.style.setProperty('display', 'none', 'important'); // 'important' fuerza el ocultado
        } else {
            // Caso B: El empleado NO tiene foto
            img.style.display = 'none';
            icon.style.display = 'flex'; // Usamos flex para centrar el icono
        }

        // 3. Botón PDF
        const btnPdf = document.getElementById('btnHistorialPdf');
        if(btnPdf) btnPdf.href = `/empleados/${emp.id}/historial-pdf`;

        // 4. Tabla Activos (Tu lógica existente)
        const tbody = document.getElementById('tablaActivosAsignados');
        tbody.innerHTML = '';
        
        if (emp.asignaciones_activas && emp.asignaciones_activas.length > 0) {
            emp.asignaciones_activas.forEach(asig => {
                const activo = asig.activo;
                const fecha = new Date(asig.fecha_asignacion).toLocaleDateString('es-MX');
                const html = `
                    <tr>
                        <td class="fw-bold text-primary">${activo.numero_serie}</td>
                        <td>${activo.tipo?.nombre || ''} - ${activo.marca?.nombre || ''}</td>
                        <td>${activo.modelo}</td>
                        <td>${fecha}</td>
                    </tr>`;
                tbody.insertAdjacentHTML('beforeend', html);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">Sin activos asignados actualmente.</td></tr>';
        }

        // Mostrar Modal
        modalVerEmpleado.show();

    } catch (error) {
        console.error(error);
        alert('Error al cargar detalles del empleado.');
    }
}

    // EDITAR EMPLEADO
    async function editarEmpleado(id) {
        try {
            const response = await fetch(`/empleados/${id}`, { headers: { 'Accept': 'application/json' } });
            if (!response.ok) throw new Error('Error de conexión');
            
            const emp = await response.json();

            document.getElementById('editId').value = emp.id;
            document.getElementById('editNumeroEmpleado').value = emp.numero_empleado;
            document.getElementById('editNombre').value = emp.nombre;
            document.getElementById('editApellidoPaterno').value = emp.apellido_paterno;
            document.getElementById('editApellidoMaterno').value = emp.apellido_materno;
            document.getElementById('editPuestoId').value = emp.puesto_id;
            document.getElementById('editCorreo').value = emp.correo;
            document.getElementById('editDepartamentoId').value = emp.departamento_id;
            document.getElementById('editPlantaId').value = emp.planta_id;
            document.getElementById('editFechaIngreso').value = emp.fecha_ingreso;

            // Manejo especial de Estatus/Baja
            const estatusSelect = document.getElementById('editEstatus');
            estatusSelect.value = emp.estatus;
            
            // Si está de baja, llenamos los campos
            if (emp.estatus === 'Baja') {
                document.getElementById('editFechaBaja').value = emp.fecha_baja;
                document.getElementById('editMotivoBaja').value = emp.motivo_baja;
            } else {
                document.getElementById('editFechaBaja').value = '';
                document.getElementById('editMotivoBaja').value = '';
            }
            toggleBajaFields(); // Actualizar visibilidad

            formEditarEmpleado.action = `/empleados/${emp.id}`;
            modalEditarEmpleado.show();

        } catch (error) {
            alert('Error al cargar datos de edición.');
        }
    }
    
    // GUARDAR EDICIÓN (Con Foto y Baja)
    formEditarEmpleado.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        // Laravel PUT method spoofing con FormData
        formData.append('_method', 'PUT'); 

        const id = document.getElementById('editId').value;

        try {
            const response = await fetch(`/empleados/${id}`, {
                method: 'POST', // Usamos POST con _method=PUT para archivos
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();
            
            if (!response.ok) {
                let errorMessages = result.message || 'Error';
                if (result.errors) errorMessages = Object.values(result.errors).flat().join('\n');
                throw new Error(errorMessages);
            }

            if (result.success) {
                modalEditarEmpleado.hide();
                window.location.reload(); // Recargar para ver cambios
            }
        } catch (error) {
            alert('Error al actualizar:\n' + error.message);
        }
    });

    // Función auxiliar para mostrar campos de baja
    function toggleBajaFields() {
        const estatus = document.getElementById('editEstatus').value;
        const fields = document.getElementById('bajaFields');
        const inputs = fields.querySelectorAll('input, select');

        if (estatus === 'Baja') {
            fields.style.display = 'flex'; // o 'block'
            inputs.forEach(i => i.required = true);
        } else {
            fields.style.display = 'none';
            inputs.forEach(i => i.required = false);
        }
    }

</script>
@endpush