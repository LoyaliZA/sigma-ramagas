@extends('layouts.app')

@section('title', 'Directorio de Empleados - SIGMA')

@section('content')
<div class="container-fluid p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark">Directorio de Empleados</h1>
            <p class="mb-0 text-muted small">Gestión de personal, historial y asignaciones.</p>
        </div>
        <button class="btn btn-primary px-4 rounded-pill shadow-sm" data-bs-toggle="modal"
            data-bs-target="#modalNuevoEmpleado">
            <i class="bi bi-person-plus-fill me-2"></i>Nuevo Empleado
        </button>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="row g-3 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i
                                class="bi bi-search"></i></span>
                        <input type="text" id="searchInput" class="form-control border-start-0 ps-0"
                            placeholder="Buscar por nombre, número o correo..." autocomplete="off">
                    </div>
                </div>

                <div class="col-md-3">
                    <select id="filtroDepartamento" class="form-select text-muted"
                        aria-label="Filtrar por Departamento">
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
                    <span class="badge bg-light text-secondary border"
                        id="contadorRegistros">{{ $empleados->count() }}</span>
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
                            <th scope="col" class="text-center py-3 text-muted small text-uppercase fw-bold">Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody id="empleadosTableBody">
                        @forelse ($empleados as $empleado)
                        <tr class="empleado-row" data-nombre="{{ strtolower($empleado->nombre_completo) }}"
                            data-numero="{{ strtolower($empleado->numero_empleado) }}"
                            data-correo="{{ strtolower($empleado->correo) }}" data-estatus="{{ $empleado->estatus }}"
                            data-depto-id="{{ $empleado->departamento_id }}"
                            data-ubicacion-id="{{ $empleado->planta_id }}">

                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    @if($empleado->foto_url)
                                    <img src="{{ asset('storage/' . $empleado->foto_url) }}"
                                        class="rounded-circle me-3 object-fit-cover shadow-sm border" width="40"
                                        height="40">
                                    @else
                                    <div class="rounded-circle me-3 bg-soft-primary text-primary d-flex align-items-center justify-content-center fw-bold border"
                                        style="width:40px; height:40px;">
                                        {{ substr($empleado->nombre, 0, 1) }}{{ substr($empleado->apellido_paterno, 0, 1) }}
                                    </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold text-dark">{{ $empleado->nombre_completo }}</div>
                                        <div class="small text-muted">{{ $empleado->numero_empleado }} |
                                            {{ $empleado->correo }}</div>
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
                                    <button class="btn btn-sm btn-light border" title="Ver Detalles"
                                        onclick="verEmpleado('{{ $empleado->id }}')">
                                        <i class="bi bi-eye text-primary"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light border" title="Editar"
                                        onclick="editarEmpleado('{{ $empleado->id }}')">
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
                                <p class="text-muted small">No se encontraron coincidencias con los filtros actuales.
                                </p>
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
    // ==========================================
    // 1. INICIALIZACIÓN Y VARIABLES GLOBALES
    // ==========================================
    
    // Instancias de Modales
    var modalNuevoEmpleado = new bootstrap.Modal(document.getElementById('modalNuevoEmpleado'));
    var modalVerEmpleado = new bootstrap.Modal(document.getElementById('modalVerEmpleado'));
    var modalEditarEmpleado = new bootstrap.Modal(document.getElementById('modalEditarEmpleado'));

    // Referencias a Formularios
    var formNuevoEmpleado = document.getElementById('formNuevoEmpleado');
    var formEditarEmpleado = document.getElementById('formEditarEmpleado');

    // Variable para controlar el empleado seleccionado (Vital para el Expediente)
    let currentEmpleadoId = null;

    // ==========================================
    // 2. LÓGICA DE BÚSQUEDA Y FILTROS
    // ==========================================

    // Algoritmo de Normalización (Quitar acentos y minúsculas)
    function limpiarTexto(texto) {
        if (!texto) return '';
        return texto.toString()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .toLowerCase();
    }

    // Variables de Estado Filtros
    let filtroTexto = '';
    let filtroDepto = '';
    let filtroUbi = '';
    let filtroEstatus = 'todos';

    // Listeners de Filtros
    document.getElementById('searchInput').addEventListener('input', (e) => {
        filtroTexto = limpiarTexto(e.target.value);
        aplicarFiltros();
    });
    
    document.getElementById('filtroDepartamento').addEventListener('change', (e) => {
        filtroDepto = e.target.value;
        aplicarFiltros();
    });

    document.getElementById('filtroUbicacion').addEventListener('change', (e) => { // Asumiendo que agregaste este ID
        filtroUbi = e.target.value;
        aplicarFiltros();
    });

    // Cambio de Pestañas (Activos / Inactivos / Bajas)
    const tabLinks = document.querySelectorAll('#tabsEstatus .nav-link');
    function cambiarTab(elemento) {
        tabLinks.forEach(link => link.classList.remove('active', 'border-bottom', 'border-primary', 'border-3'));
        elemento.classList.add('active', 'border-bottom', 'border-primary', 'border-3');
        filtroEstatus = elemento.getAttribute('data-estatus');
        aplicarFiltros();
    }

    // Función Maestra de Filtrado
    function aplicarFiltros() {
        const rows = document.querySelectorAll('.empleado-row');
        const noSearchRow = document.getElementById('noSearchRow');
        let visibles = 0;

        rows.forEach(row => {
            const nombre = limpiarTexto(row.getAttribute('data-nombre'));
            const numero = limpiarTexto(row.getAttribute('data-numero'));
            const correo = limpiarTexto(row.getAttribute('data-correo'));
            const deptoId = row.getAttribute('data-depto-id');
            const ubiId = row.getAttribute('data-ubicacion-id'); // Asegúrate que tu TR tenga este data attribute
            const estatus = row.getAttribute('data-estatus');

            const coincideTexto = nombre.includes(filtroTexto) || numero.includes(filtroTexto) || correo.includes(filtroTexto);
            const coincideDepto = filtroDepto === '' || deptoId === filtroDepto;
            // Si no tienes filtroUbicacion en el HTML, puedes comentar la siguiente línea
            // const coincideUbi = filtroUbi === '' || ubiId === filtroUbi; 
            const coincideEstatus = filtroEstatus === 'todos' || estatus === filtroEstatus;

            if (coincideTexto && coincideDepto && coincideEstatus) { // Agrega && coincideUbi si usas ese filtro
                row.style.display = '';
                visibles++;
            } else {
                row.style.display = 'none';
            }
        });

        if (noSearchRow) noSearchRow.style.display = (visibles === 0) ? '' : 'none';
        
        const badge = document.getElementById('contadorRegistros');
        if (badge) badge.textContent = visibles;
    }


    // ==========================================
    // 3. LÓGICA DE EMPLEADOS (CRUD)
    // ==========================================

    // --- CREAR EMPLEADO ---
    formNuevoEmpleado.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        try {
            // Usamos axios que maneja mejor los errores y headers, pero fetch es valido
            const response = await fetch("{{ route('empleados.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();

            if (!response.ok) {
                let msg = result.message || 'Error desconocido';
                if(result.errors) msg = Object.values(result.errors).flat().join('\n');
                throw new Error(msg);
            }

            if (result.success) {
                modalNuevoEmpleado.hide();
                formNuevoEmpleado.reset();
                Swal.fire({ icon: 'success', title: '¡Éxito!', text: result.message, timer: 1500, showConfirmButton: false })
                    .then(() => window.location.reload());
            }
        } catch (error) {
            Swal.fire({ icon: 'error', title: 'Error', text: error.message });
        }
    });

    // --- VER DETALLES (CON EXPEDIENTE) ---
    async function verEmpleado(id) {
        currentEmpleadoId = id; // Guardar ID para subida de docs

        // Reset UI: Seleccionar Tab Perfil por defecto
        const firstTab = document.querySelector('#empleadoTabs button[data-bs-target="#perfil"]');
        if(firstTab) bootstrap.Tab.getOrCreateInstance(firstTab).show();

        // Loaders
        document.getElementById('verListaContactos').innerHTML = '<div class="text-center p-3"><div class="spinner-border text-primary"></div></div>';
        const listaDocs = document.getElementById('listaDocumentos');
        if(listaDocs) listaDocs.innerHTML = '<tr><td colspan="3" class="text-center p-3">Cargando expediente...</td></tr>';

        axios.get(`/empleados/${id}`)
            .then(response => {
                const emp = response.data;

                // 1. Datos Generales
                document.getElementById('verNombreCompleto').textContent = emp.nombre_completo;
                document.getElementById('verPuesto').textContent = emp.puesto?.nombre || 'N/A';
                document.getElementById('verNumeroEmpleado').textContent = emp.numero_empleado;
                document.getElementById('verCodigoEmpresa').textContent = emp.codigo_empresa || '-';
                document.getElementById('verDepartamento').textContent = emp.departamento?.nombre || 'N/A';
                document.getElementById('verUbicacion').textContent = emp.ubicacion?.nombre || 'N/A';
                document.getElementById('verCorreo').textContent = emp.correo || 'N/A';
                
                // Formateo de Fecha
                let fechaIngreso = 'N/A';
                if(emp.fecha_ingreso) {
                    const [y, m, d] = emp.fecha_ingreso.split('-');
                    fechaIngreso = `${d}/${m}/${y}`;
                }
                document.getElementById('verFechaIngreso').textContent = fechaIngreso;

                // 2. Foto (Manejo de d-none)
                const img = document.getElementById('verFoto');
                const icon = document.getElementById('verIconoDefault');
                
                if (emp.foto_url) {
                    img.src = `/storage/${emp.foto_url}`;
                    img.classList.remove('d-none');
                    icon.classList.add('d-none');
                } else {
                    img.src = '';
                    img.classList.add('d-none');
                    icon.classList.remove('d-none');
                }

                // 3. Contactos
                const divContactos = document.getElementById('verListaContactos');
                divContactos.innerHTML = '';
                if (emp.contactos && emp.contactos.length > 0) {
                    emp.contactos.forEach(c => {
                        divContactos.innerHTML += `
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-start border rounded p-2 bg-white h-100 shadow-sm">
                                    <i class="bi bi-telephone-fill text-primary mt-1 me-2"></i>
                                    <div>
                                        <div class="fw-bold small text-uppercase text-muted" style="font-size:0.75rem">${c.tipo}</div>
                                        <div class="fw-bold text-dark">${c.valor}</div>
                                        ${c.descripcion ? `<div class="small text-muted">${c.descripcion}</div>` : ''}
                                    </div>
                                </div>
                            </div>`;
                    });
                } else {
                    divContactos.innerHTML = '<div class="col-12 text-muted fst-italic ps-3">No hay contactos adicionales.</div>';
                }

                // 4. Activos
                const tablaActivos = document.getElementById('tablaActivosAsignados');
                tablaActivos.innerHTML = '';
                if (emp.asignaciones_activas && emp.asignaciones_activas.length > 0) {
                    emp.asignaciones_activas.forEach(asig => {
                        tablaActivos.innerHTML += `
                            <tr>
                                <td><span class="badge bg-light text-dark border">${asig.activo.serie}</span></td>
                                <td>${asig.activo.tipo.nombre}</td>
                                <td class="small text-muted">${asig.activo.modelo}</td>
                                <td class="small">${asig.fecha_asignacion}</td>
                            </tr>`;
                    });
                } else {
                    tablaActivos.innerHTML = '<tr><td colspan="4" class="text-center text-muted small py-3">No tiene activos asignados.</td></tr>';
                }

                // 5. Botón PDF Historial
                const btnPdf = document.getElementById('btnHistorialPdf');
                if(btnPdf) btnPdf.href = `/empleados/${emp.id}/historial-pdf`;

                // 6. Expediente Digital (Nueva Lógica)
                renderizarTablaDocumentos(emp.documentos);

                modalVerEmpleado.show();
            })
            .catch(error => {
                console.error(error);
                Swal.fire('Error', 'No se pudo cargar la información del empleado.', 'error');
            });
    }

    // --- EDITAR EMPLEADO ---
    async function editarEmpleado(id) {
        formEditarEmpleado.reset();
        document.getElementById('contenedor-contactos-edit').innerHTML = ''; // Limpiar contactos

        axios.get(`/empleados/${id}`)
            .then(response => {
                const emp = response.data;

                // Llenar campos simples
                document.getElementById('editId').value = emp.id;
                document.getElementById('editNumeroEmpleado').value = emp.numero_empleado;
                document.getElementById('editCodigoEmpresa').value = emp.codigo_empresa || '';
                document.getElementById('editNombre').value = emp.nombre;
                document.getElementById('editApellidoPaterno').value = emp.apellido_paterno;
                document.getElementById('editApellidoMaterno').value = emp.apellido_materno || '';
                document.getElementById('editPuestoId').value = emp.puesto_id;
                document.getElementById('editDepartamentoId').value = emp.departamento_id;
                document.getElementById('editPlantaId').value = emp.planta_id;
                document.getElementById('editCorreo').value = emp.correo || '';
                document.getElementById('editFechaIngreso').value = emp.fecha_ingreso || '';
                
                // Estatus y Baja
                const estatusSelect = document.getElementById('editEstatus');
                estatusSelect.value = emp.estatus;
                
                // Activar campos de baja si aplica
                if (emp.estatus === 'Baja') {
                    document.getElementById('bajaFields').style.display = 'flex';
                    document.getElementById('editFechaBaja').value = emp.fecha_baja;
                    document.getElementById('editMotivoBaja').value = emp.motivo_baja;
                } else {
                    document.getElementById('bajaFields').style.display = 'none';
                }

                // Llenar Contactos (Llama a función global definida en modal_editar.blade.php)
                if (emp.contactos && emp.contactos.length > 0) {
                    emp.contactos.forEach(c => {
                        if(typeof agregarFilaContactoEdit === 'function') {
                            agregarFilaContactoEdit(c);
                        }
                    });
                }

                modalEditarEmpleado.show();
            })
            .catch(error => {
                console.error(error);
                Swal.fire('Error', 'No se pudieron cargar los datos para edición.', 'error');
            });
    }

    // Guardar Edición
    formEditarEmpleado.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('_method', 'PUT'); // Truco Laravel para PUT con Archivos
        
        const id = document.getElementById('editId').value;

        try {
            const response = await fetch(`/empleados/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();

            if (!response.ok) {
                let msg = result.message || 'Error';
                if(result.errors) msg = Object.values(result.errors).flat().join('\n');
                throw new Error(msg);
            }

            if (result.success) {
                modalEditarEmpleado.hide();
                Swal.fire({ icon: 'success', title: 'Actualizado', text: result.message, timer: 1500, showConfirmButton: false })
                    .then(() => window.location.reload());
            }
        } catch (error) {
            Swal.fire({ icon: 'error', title: 'Error al actualizar', text: error.message });
        }
    });

    // Toggle para campos de baja (Helper)
    function toggleBajaFields() {
        const estatus = document.getElementById('editEstatus').value;
        const fields = document.getElementById('bajaFields');
        const inputs = fields.querySelectorAll('input, select');

        if (estatus === 'Baja') {
            fields.style.display = 'flex';
            inputs.forEach(i => i.required = true);
        } else {
            fields.style.display = 'none';
            inputs.forEach(i => i.required = false);
        }
    }


    // ==========================================
    // 4. LÓGICA DE EXPEDIENTE DIGITAL
    // ==========================================

    // Renderizar tabla de documentos
    function renderizarTablaDocumentos(docs) {
        const tbody = document.getElementById('listaDocumentos');
        if(!tbody) return;
        
        tbody.innerHTML = '';

        if (docs && docs.length > 0) {
            docs.forEach(doc => {
                const fecha = new Date(doc.created_at).toLocaleDateString();
                tbody.innerHTML += `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-file-earmark-pdf text-danger fs-5 me-2"></i>
                                <div>
                                    <div class="fw-bold small">${doc.tipo_documento}</div>
                                    <div class="text-muted" style="font-size: 10px;">${doc.nombre.substring(0, 25)}...</div>
                                </div>
                            </div>
                        </td>
                        <td class="small text-muted align-middle">${fecha}</td>
                        <td class="text-end align-middle">
                            <a href="/storage/${doc.ruta_archivo}" target="_blank" class="btn btn-sm btn-link text-primary p-0 me-2" title="Descargar">
                                <i class="bi bi-download"></i>
                            </a>
                            <button onclick="eliminarDocumento(${doc.id})" class="btn btn-sm btn-link text-danger p-0" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted small py-3 fst-italic">No hay documentos en el expediente SIGMA.</td></tr>';
        }
    }

    // Subir Documento
    const formSubirDoc = document.getElementById('formSubirDocumento');
    if(formSubirDoc) {
        formSubirDoc.addEventListener('submit', function(e) {
            e.preventDefault();
            if(!currentEmpleadoId) return;

            const archivoInput = document.getElementById('docArchivo');
            if(archivoInput.files.length === 0) return;

            const formData = new FormData();
            formData.append('tipo_documento', document.getElementById('docTipo').value);
            formData.append('archivo', archivoInput.files[0]);

            const btn = this.querySelector('button[type="submit"]');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Subiendo...';

            axios.post(`/empleados/${currentEmpleadoId}/documentos`, formData)
                .then(response => {
                    if(response.data.success) {
                        // Recargar solo los docs
                        axios.get(`/empleados/${currentEmpleadoId}`).then(res => {
                            renderizarTablaDocumentos(res.data.documentos);
                        });
                        this.reset();
                        Swal.fire({ icon: 'success', title: 'Subido', timer: 1000, showConfirmButton: false, position: 'top-end', toast: true });
                    }
                })
                .catch(error => {
                    console.error(error);
                    Swal.fire('Error', 'Verifique que el archivo sea PDF/Imagen y menor a 1MB.', 'error');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                });
        });
    }

    // Eliminar Documento
    window.eliminarDocumento = function(docId) {
        Swal.fire({
            title: '¿Eliminar documento?',
            text: "Se borrará permanentemente del expediente.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sí, borrar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`/empleados/documentos/${docId}`)
                    .then(response => {
                        if(response.data.success) {
                            axios.get(`/empleados/${currentEmpleadoId}`).then(res => {
                                renderizarTablaDocumentos(res.data.documentos);
                            });
                            Swal.fire({ icon: 'success', title: 'Eliminado', timer: 1000, showConfirmButton: false, position: 'top-end', toast: true });
                        }
                    })
                    .catch(() => Swal.fire('Error', 'No se pudo eliminar el archivo.', 'error'));
            }
        });
    }

</script>
@endpush