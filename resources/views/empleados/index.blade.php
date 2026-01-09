@extends('layouts.app')

@section('title', 'Directorio de Empleados - SIGMA')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
    /* Ajuste para que Select2 se vea bien dentro de Modales y con Bootstrap 5 */
    .select2-container--bootstrap-5 .select2-selection {
        border-color: #dee2e6;
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
    }
    .select2-container {
        z-index: 9999; /* Asegurar que el dropdown salga sobre el modal */
    }
</style>
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
                        <tr class="empleado-row" id="fila-empleado-{{ $empleado->id }}"
                            data-nombre="{{ strtolower($empleado->nombre_completo) }}"
                            data-numero="{{ strtolower($empleado->numero_empleado) }}"
                            data-correo="{{ strtolower($empleado->correo) }}" data-estatus="{{ $empleado->estatus }}"
                            data-depto-id="{{ $empleado->departamento_id }}"
                            data-ubicacion-id="{{ $empleado->planta_id }}">

                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    @if($empleado->foto_url)
                                    {{-- Agregamos ID a la imagen para actualizarla sin recargar --}}
                                    <img src="{{ asset('storage/' . $empleado->foto_url) }}"
                                        class="rounded-circle me-3 object-fit-cover shadow-sm border img-avatar"
                                        width="40" height="40">
                                    @else
                                    <div class="rounded-circle me-3 bg-soft-primary text-primary d-flex align-items-center justify-content-center fw-bold border div-avatar"
                                        style="width:40px; height:40px;">
                                        {{ substr($empleado->nombre, 0, 1) }}{{ substr($empleado->apellido_paterno, 0, 1) }}
                                    </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold text-dark nombre-empleado">{{ $empleado->nombre_completo }}
                                        </div>
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

                            <td class="text-center">
                                {{-- AQUI: Agregamos la clase 'badge-estatus' para identificarlo con JS --}}
                                @php
                                $badgeClass = match($empleado->estatus) {
                                'Activo' => 'bg-success bg-opacity-10 text-success',
                                'Inactivo' => 'bg-secondary bg-opacity-10 text-secondary',
                                'Baja' => 'bg-danger bg-opacity-10 text-danger',
                                default => 'bg-light text-dark'
                                };
                                @endphp
                                <span class="badge {{ $badgeClass }} px-3 py-2 rounded-pill badge-estatus">
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
@push('scripts')
<script>
    // ==========================================
    // 1. INICIALIZACIÓN
    // ==========================================
    const modalNuevo = new bootstrap.Modal(document.getElementById('modalNuevoEmpleado'));
    const modalVer = new bootstrap.Modal(document.getElementById('modalVerEmpleado'));
    const modalEditar = new bootstrap.Modal(document.getElementById('modalEditarEmpleado'));

    const formNuevo = document.getElementById('formNuevoEmpleado');
    const formEditar = document.getElementById('formEditarEmpleado');
    
    let currentEmpleadoId = null; // Para expediente digital

    // ==========================================
    // 2. FILTROS Y BÚSQUEDA
    // ==========================================
    function limpiarTexto(t) { return t ? t.toString().normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase() : ''; }

    let filtro = { texto: '', depto: '', ubi: '', estatus: 'todos' };

    document.getElementById('searchInput').addEventListener('input', (e) => { filtro.texto = limpiarTexto(e.target.value); aplicarFiltros(); });
    document.getElementById('filtroDepartamento').addEventListener('change', (e) => { filtro.depto = e.target.value; aplicarFiltros(); });
    document.getElementById('filtroUbicacion').addEventListener('change', (e) => { filtro.ubi = e.target.value; aplicarFiltros(); });

    function cambiarTab(el) {
        document.querySelectorAll('#tabsEstatus .nav-link').forEach(l => l.classList.remove('active', 'border-bottom', 'border-primary', 'border-3'));
        el.classList.add('active', 'border-bottom', 'border-primary', 'border-3');
        filtro.estatus = el.getAttribute('data-estatus');
        aplicarFiltros();
    }

    function aplicarFiltros() {
        let visibles = 0;
        document.querySelectorAll('.empleado-row').forEach(row => {
            const data = {
                nombre: limpiarTexto(row.dataset.nombre),
                numero: limpiarTexto(row.dataset.numero),
                correo: limpiarTexto(row.dataset.correo),
                depto: row.dataset.deptoId,
                ubi: row.dataset.ubicacionId,
                estatus: row.dataset.estatus
            };

            const matchTexto = data.nombre.includes(filtro.texto) || data.numero.includes(filtro.texto) || data.correo.includes(filtro.texto);
            const matchDepto = filtro.depto === '' || data.depto === filtro.depto;
            const matchUbi = filtro.ubi === '' || data.ubi === filtro.ubi;
            const matchEstatus = filtro.estatus === 'todos' || data.estatus === filtro.estatus;

            if (matchTexto && matchDepto && matchUbi && matchEstatus) {
                row.style.display = '';
                visibles++;
            } else {
                row.style.display = 'none';
            }
        });
        
        document.getElementById('noSearchRow').style.display = (visibles === 0) ? '' : 'none';
        document.getElementById('contadorRegistros').textContent = visibles;
    }

    // ==========================================
    // 3. CRUD EMPLEADOS
    // ==========================================

    // --- NUEVO ---
    formNuevo.addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;

        try {
            const res = await axios.post("{{ route('empleados.store') }}", new FormData(this));
            if (res.data.success) {
                modalNuevo.hide();
                formNuevo.reset();
                Swal.fire({ icon: 'success', title: 'Guardado', text: 'Empleado creado correctamente.', timer: 1500, showConfirmButton: false })
                    .then(() => window.location.reload());
            }
        } catch (error) {
            Swal.fire('Error', error.response?.data?.message || 'Error al guardar', 'error');
        } finally {
            btn.disabled = false;
        }
    });

    // --- VER DETALLES ---
    async function verEmpleado(id) {
        currentEmpleadoId = id;
        
        // Reset Tabs
        const tabPerfil = document.querySelector('#empleadoTabs button[data-bs-target="#perfil"]');
        if(tabPerfil) bootstrap.Tab.getOrCreateInstance(tabPerfil).show();

        // Loaders
        document.getElementById('verListaContactos').innerHTML = '<div class="spinner-border spinner-border-sm"></div>';
        const listaDocs = document.getElementById('listaDocumentos');
        if(listaDocs) listaDocs.innerHTML = '<tr><td colspan="3" class="text-center">Cargando...</td></tr>';

        try {
            const { data: emp } = await axios.get(`/empleados/${id}`);
            
            // Datos Básicos
            document.getElementById('verNombreCompleto').textContent = emp.nombre_completo;
            document.getElementById('verPuesto').textContent = emp.puesto?.nombre || 'N/A';
            document.getElementById('verNumeroEmpleado').textContent = emp.numero_empleado;
            document.getElementById('verCodigoEmpresa').textContent = emp.codigo_empresa || '-';
            document.getElementById('verDepartamento').textContent = emp.departamento?.nombre || 'N/A';
            document.getElementById('verUbicacion').textContent = emp.ubicacion?.nombre || 'N/A';
            document.getElementById('verCorreo').textContent = emp.correo || 'N/A';
            document.getElementById('verFechaIngreso').textContent = emp.fecha_ingreso ? new Date(emp.fecha_ingreso).toLocaleDateString() : 'N/A';

            // Foto
            const img = document.getElementById('verFoto');
            const icon = document.getElementById('verIconoDefault');
            if(emp.foto_url) {
                img.src = `/storage/${emp.foto_url}`;
                img.classList.remove('d-none');
                icon.classList.add('d-none');
            } else {
                img.classList.add('d-none');
                icon.classList.remove('d-none');
            }

            // Contactos
            const divContactos = document.getElementById('verListaContactos');
            divContactos.innerHTML = '';
            if(emp.contactos?.length > 0) {
                emp.contactos.forEach(c => {
                    divContactos.innerHTML += `
                        <div class="col-md-6 mb-2">
                            <div class="d-flex align-items-start border rounded p-2 bg-white h-100">
                                <i class="bi bi-telephone-fill text-primary mt-1 me-2"></i>
                                <div><div class="fw-bold small text-muted">${c.tipo}</div><div class="fw-bold text-dark">${c.valor}</div></div>
                            </div>
                        </div>`;
                });
            } else {
                divContactos.innerHTML = '<div class="col-12 text-muted fst-italic ps-3">Sin contactos adicionales.</div>';
            }

            // Activos
            const tablaActivos = document.getElementById('tablaActivosAsignados');
tablaActivos.innerHTML = '';

if (emp.asignaciones_activas && emp.asignaciones_activas.length > 0) {
    emp.asignaciones_activas.forEach(asig => {
        // CORRECCIÓN AQUÍ: Usamos .numero_serie en lugar de .serie
        const serie = asig.activo.numero_serie || 'S/N'; 
        
        tablaActivos.innerHTML += `
            <tr>
                <td><span class="badge bg-light text-dark border">${serie}</span></td>
                <td>${asig.activo.tipo.nombre}</td>
                <td class="small text-muted">${asig.activo.modelo}</td>
                <td class="small">${asig.fecha_asignacion}</td>
            </tr>`;
    });
} else {
    tablaActivos.innerHTML = '<tr><td colspan="4" class="text-center text-muted small py-3">No tiene activos asignados.</td></tr>';
}

            // PDF Button
            const btnPdf = document.getElementById('btnHistorialPdf');
            if(btnPdf) btnPdf.href = `/empleados/${emp.id}/historial-pdf`;

            // Documentos
            renderizarDocs(emp.documentos);

            modalVer.show();

        } catch (e) {
            console.error(e);
            Swal.fire('Error', 'No se pudo cargar la información.', 'error');
        }
    }

    // --- EDITAR ---
    async function editarEmpleado(id) {
        formEditar.reset();
        document.getElementById('contenedor-contactos-edit').innerHTML = '';
        
        try {
            const { data: emp } = await axios.get(`/empleados/${id}`);
            
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
            document.getElementById('editEstatus').value = emp.estatus;

            toggleBajaFields();
            if(emp.estatus === 'Baja') {
                document.getElementById('editFechaBaja').value = emp.fecha_baja;
                document.getElementById('editMotivoBaja').value = emp.motivo_baja;
            }

            if(emp.contactos?.length > 0) {
                emp.contactos.forEach(c => {
                    if(typeof agregarFilaContactoEdit === 'function') agregarFilaContactoEdit(c);
                });
            }

            modalEditar.show();
        } catch (e) {
            Swal.fire('Error', 'No se pudieron cargar los datos.', 'error');
        }
    }

    // --- GUARDAR EDICIÓN (ACTUALIZACIÓN DOM INMEDIATA) ---
    formEditar.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('_method', 'PUT');
        const id = document.getElementById('editId').value;

        try {
            const res = await axios.post(`/empleados/${id}`, formData);
            
            if (res.data.success) {
                modalEditar.hide();
                
                // ACTUALIZACIÓN VISUAL INSTANTÁNEA
                const row = document.getElementById(`fila-empleado-${id}`);
                if (row) {
                    const emp = res.data.empleado;
                    
                    // 1. Actualizar Datos Data
                    row.dataset.estatus = emp.estatus;
                    row.dataset.nombre = `${emp.nombre} ${emp.apellido_paterno} ${emp.apellido_materno}`;
                    row.dataset.correo = emp.correo || '';

                    // 2. Actualizar Badge
                    const badge = row.querySelector('.badge-estatus');
                    badge.className = `badge badge-estatus rounded-pill ${emp.estatus == 'Activo' ? 'bg-success bg-opacity-10 text-success' : (emp.estatus == 'Baja' ? 'bg-danger bg-opacity-10 text-danger' : 'bg-secondary bg-opacity-10 text-secondary')}`;
                    badge.textContent = emp.estatus;

                    // 3. Actualizar Nombre
                    const nombreEl = row.querySelector('.nombre-empleado');
                    if(nombreEl) nombreEl.textContent = emp.nombre_completo;

                    // 4. Re-filtrar por si cambió de estatus y debe desaparecer
                    aplicarFiltros();
                }

                // Toast no intrusivo
                const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                Toast.fire({ icon: 'success', title: 'Actualizado correctamente' });
            }
        } catch (error) {
            Swal.fire('Error', error.response?.data?.message || 'Error al actualizar', 'error');
        }
    });

    // ==========================================
    // 4. EXPEDIENTE DIGITAL
    // ==========================================
    function renderizarDocs(docs) {
        const tbody = document.getElementById('listaDocumentos');
        if(!tbody) return;
        tbody.innerHTML = '';
        if(docs?.length > 0) {
            docs.forEach(d => {
                const fecha = new Date(d.created_at).toLocaleDateString();
                tbody.innerHTML += `
                    <tr>
                        <td><div class="d-flex align-items-center"><i class="bi bi-file-earmark-pdf text-danger fs-5 me-2"></i><div><div class="fw-bold small">${d.tipo_documento}</div><div class="text-muted" style="font-size:10px;">${d.nombre.substring(0,20)}...</div></div></div></td>
                        <td class="small text-muted">${fecha}</td>
                        <td class="text-end"><a href="/storage/${d.ruta_archivo}" target="_blank" class="btn btn-sm btn-link"><i class="bi bi-download"></i></a><button onclick="eliminarDoc(${d.id})" class="btn btn-sm btn-link text-danger"><i class="bi bi-trash"></i></button></td>
                    </tr>`;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted small py-3 fst-italic">Carpeta vacía.</td></tr>';
        }
    }

    const formDoc = document.getElementById('formSubirDocumento');
    if(formDoc) {
        formDoc.addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            
            try {
                const res = await axios.post(`/empleados/${currentEmpleadoId}/documentos`, new FormData(this));
                if(res.data.success) {
                    const { data: emp } = await axios.get(`/empleados/${currentEmpleadoId}`);
                    renderizarDocs(emp.documentos);
                    this.reset();
                    Swal.fire({ icon: 'success', title: 'Subido', toast: true, position: 'top-end', showConfirmButton: false, timer: 1500 });
                }
            } catch (e) {
                Swal.fire('Error', 'Verifique el archivo (Max 1MB).', 'error');
            } finally {
                btn.disabled = false; btn.innerHTML = '<i class="bi bi-cloud-upload me-1"></i> Subir Archivo';
            }
        });
    }

    window.eliminarDoc = function(id) {
        Swal.fire({ title: '¿Eliminar?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Sí, borrar' }).then(async (r) => {
            if(r.isConfirmed) {
                try {
                    const res = await axios.delete(`/empleados/documentos/${id}`);
                    if(res.data.success) {
                        const { data: emp } = await axios.get(`/empleados/${currentEmpleadoId}`);
                        renderizarDocs(emp.documentos);
                        Swal.fire({ icon: 'success', title: 'Eliminado', toast: true, position: 'top-end', showConfirmButton: false, timer: 1500 });
                    }
                } catch(e) { Swal.fire('Error', 'No se pudo eliminar.', 'error'); }
            }
        });
    }

    function toggleBajaFields() {
        const estatus = document.getElementById('editEstatus').value;
        const fields = document.getElementById('bajaFields');
        if(estatus === 'Baja') {
            fields.style.display = 'flex';
            fields.querySelectorAll('input, select').forEach(i => i.required = true);
        } else {
            fields.style.display = 'none';
            fields.querySelectorAll('input, select').forEach(i => i.required = false);
        }
    }

    // --- INICIALIZACIÓN DE SELECT2 ---
    $(document).ready(function() {
        // Cargar scripts de Select2 dinámicamente si no usas npm
        $.getScript("https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js", function() {
            
            // Función helper para aplicar Select2
            const initSelect2 = (selector, modalParent) => {
                $(selector).select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $(modalParent), // VITAL: Para que funcione el input de búsqueda en el modal
                    width: '100%',
                    language: {
                        noResults: function() { return "No se encontraron resultados"; }
                    }
                });
            };

            // Inicializar en Modal Nuevo
            $('#modalNuevoEmpleado').on('shown.bs.modal', function () {
                initSelect2('#modalNuevoEmpleado select[name="puesto_id"]', '#modalNuevoEmpleado');
                initSelect2('#modalNuevoEmpleado select[name="departamento_id"]', '#modalNuevoEmpleado');
                initSelect2('#modalNuevoEmpleado select[name="planta_id"]', '#modalNuevoEmpleado');
            });

            // Inicializar en Modal Editar
            $('#modalEditarEmpleado').on('shown.bs.modal', function () {
                initSelect2('#editPuestoId', '#modalEditarEmpleado');
                initSelect2('#editDepartamentoId', '#modalEditarEmpleado');
                initSelect2('#editPlantaId', '#modalEditarEmpleado');
            });
        });
    });
</script>

@endpush
@endpush