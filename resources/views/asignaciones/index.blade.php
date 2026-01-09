@extends('layouts.app')

@section('title', 'Gestión de Asignaciones')

@section('content')
<div class="container-fluid p-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1 fw-bold text-dark">Gestión de Asignaciones</h2>
            <p class="text-muted small mb-0">Control de entregas múltiples y expedientes digitales</p>
        </div>
        <button type="button" class="btn btn-primary px-4 shadow-sm" id="btnNuevaAsignacion">
            <i class="bi bi-plus-lg me-2"></i>Nueva Asignación
        </button>
    </div>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3 gap-3">
        <ul class="nav nav-pills" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-pill px-4" id="pills-activas-tab" data-bs-toggle="pill" data-bs-target="#pills-activas" type="button" role="tab">
                    <i class="bi bi-person-workspace me-2"></i>Asignaciones Activas ({{ $asignacionesActivas->count() }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4" id="pills-historial-tab" data-bs-toggle="pill" data-bs-target="#pills-historial" type="button" role="tab">
                    <i class="bi bi-clock-history me-2"></i>Historial ({{ $historial->count() }})
                </button>
            </li>
        </ul>

        <form action="{{ route('asignaciones.index') }}" method="GET" class="d-flex position-relative" style="max-width: 300px; width: 100%;">
            <input type="text" name="search" class="form-control ps-5 rounded-pill bg-white border-0 shadow-sm" placeholder="Buscar activo, empleado..." value="{{ request('search') }}">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
            @if(request('search'))
                <a href="{{ route('asignaciones.index') }}" class="position-absolute top-50 end-0 translate-middle-y me-3 text-muted"><i class="bi bi-x-circle-fill"></i></a>
            @endif
        </form>
    </div>

    <div class="tab-content" id="pills-tabContent">
        
        <div class="tab-pane fade show active" id="pills-activas" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-uppercase small text-muted">
                                <tr>
                                    <th class="ps-4 py-3">Folio/Activo</th>
                                    <th class="py-3">Empleado</th>
                                    <th class="py-3">Fecha</th>
                                    <th class="py-3">Expediente</th>
                                    <th class="text-end pe-4 py-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($asignacionesActivas as $asig)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-primary">
                                                {{ $asig->lote_id ? substr($asig->lote_id, 0, 8) : substr($asig->id, 0, 8) }}...
                                            </span>
                                            <small class="text-dark">{{ $asig->activo->modelo }} ({{ $asig->activo->numero_serie }})</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold text-dark">{{ $asig->empleado->nombre }} {{ $asig->empleado->apellido_paterno }}</span>
                                            <small class="text-muted">{{ $asig->empleado->numero_empleado }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-muted small">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            {{ $asig->fecha_asignacion->format('d/m/Y') }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($asig->carta_responsiva_url)
                                            <button class="btn btn-sm btn-soft-success rounded-pill px-3 btn-ver-documento" 
                                                    data-id="{{ $asig->id }}" >
                                                <i class="bi bi-check-circle-fill me-1"></i> Firmado
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-soft-warning rounded-pill px-3 btn-subir-documento"
                                                    data-id="{{ $asig->id }}">
                                                <i class="bi bi-upload me-1"></i> Pendiente
                                            </button>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            {{-- LÓGICA CONDICIONAL: Lote vs Individual --}}
                                            @if($asig->lote_id)
                                                <a href="{{ route('asignaciones.carta_lote', $asig->lote_id) }}" target="_blank" class="btn btn-outline-secondary btn-sm" title="Ver Formato PDF (Lote)">
                                                    <i class="bi bi-file-pdf"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('asignaciones.carta', $asig->id) }}" target="_blank" class="btn btn-outline-secondary btn-sm" title="Ver Formato PDF (Individual)">
                                                    <i class="bi bi-file-pdf"></i>
                                                </a>
                                            @endif

                                            <button type="button" class="btn btn-outline-danger btn-sm btn-devolver-trigger" 
                                                data-id="{{ $asig->id }}"
                                                data-serie="{{ $asig->activo->numero_serie }}"
                                                data-modelo="{{ $asig->activo->modelo }}"
                                                data-empleado="{{ $asig->empleado->nombre }} {{ $asig->empleado->apellido_paterno }}"
                                                data-num-empleado="{{ $asig->empleado->numero_empleado }}"
                                                data-fecha="{{ $asig->fecha_asignacion->format('d/m/Y') }}">
                                                Devolver
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted opacity-50">
                                            <i class="bi bi-inbox fs-1"></i>
                                            <p class="mt-2">No hay asignaciones activas actualmente.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="pills-historial" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-uppercase small text-muted">
                                <tr>
                                    <th class="ps-4 py-3">Activo</th>
                                    <th class="py-3">Empleado</th>
                                    <th class="py-3">Periodo</th>
                                    <th class="py-3">Estado Final</th>
                                    <th class="py-3">Observaciones</th>
                                    <th class="text-end pe-4 py-3">Doc</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($historial as $hist)
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-bold text-dark">{{ $hist->activo->numero_serie }}</span><br>
                                        <small class="text-muted">{{ $hist->activo->modelo }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-semibold">{{ $hist->empleado->nombre }} {{ $hist->empleado->apellido_paterno }}</span>
                                    </td>
                                    <td>
                                        <small class="d-block text-muted">Del: {{ $hist->fecha_asignacion->format('d/m/Y') }}</small>
                                        <small class="d-block text-dark fw-bold">Al: {{ $hist->fecha_devolucion->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-secondary text-secondary rounded-pill px-3">
                                            {{ $hist->estadoDevolucion->nombre ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td style="max-width: 200px;">
                                        <span class="text-muted small text-truncate d-block" title="{{ $hist->observaciones_devolucion }}">
                                            {{ $hist->observaciones_devolucion ?? 'Sin observaciones' }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        @if($hist->fecha_devolucion)
                                            <a href="{{ route('asignaciones.carta_devolucion', $hist->id) }}" target="_blank" class="btn btn-sm btn-light text-danger" title="Constancia Devolución">
                                                <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">No hay historial disponible.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAsignar" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Nueva Asignación Múltiple</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAsignar">
                <div class="modal-body">
                    <div class="mb-4 p-3 bg-light rounded-3 border">
                        <label class="form-label fw-bold small text-uppercase text-muted">1. Seleccionar Empleado *</label>
                        <select class="form-select form-select-lg" name="empleado_id" required>
                            <option value="" selected disabled>Buscar empleado...</option>
                            @foreach($empleados as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->numero_empleado }} - {{ $emp->nombre }} {{ $emp->apellido_paterno }}</option>
                            @endforeach
                        </select>
                    </div>

                    <label class="form-label fw-bold small text-uppercase text-muted">2. Seleccionar Activos a Entregar *</label>
                    <div id="lista-activos-container">
                        <div class="row g-2 mb-2 activo-row align-items-center">
                            <div class="col-10">
                                <select class="form-select select-activo" name="activos[]" required onchange="verificarDuplicados(this)">
                                    <option value="" selected disabled>Seleccionar Activo...</option>
                                    @foreach($activosDisponibles as $act)
                                        <option value="{{ $act->id }}">{{ $act->tipo->nombre }} - {{ $act->modelo }} ({{ $act->numero_serie }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-2">
                                <button type="button" class="btn btn-outline-danger w-100 btn-remove-row" style="display:none;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-sm btn-soft-primary mt-2" id="btnAgregarFila">
                        <i class="bi bi-plus-circle me-1"></i> Agregar otro equipo
                    </button>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Fecha Asignación</label>
                            <input type="date" class="form-control" name="fecha_asignacion" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Condición de Entrega</label>
                            <select class="form-select" name="estado_entrega_id" required>
                                @foreach($estadosEntrega as $edo)
                                    <option value="{{ $edo->id }}">{{ $edo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mt-3">
                            <label class="form-label small fw-bold text-muted">Observaciones Generales</label>
                            <textarea class="form-control" name="observaciones" rows="2" placeholder="Cargadores, condiciones, etc..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i>Guardar y Generar PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDevolver" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Devolución de Activo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formDevolver">
                <input type="hidden" name="asignacion_id" id="dev_asignacion_id">
                <div class="modal-body">
                    <div class="p-3 mb-3 rounded-3 bg-soft-primary text-primary-emphasis border border-primary-subtle">
                        <h6 class="fw-bold small mb-2">Información de la Asignación</h6>
                        <ul class="list-unstyled small mb-0">
                            <li><strong>Activo:</strong> <span id="dev_activo_info">...</span></li>
                            <li><strong>Empleado:</strong> <span id="dev_empleado_info">...</span></li>
                            <li><strong>Fecha Asignación:</strong> <span id="dev_fecha_asignacion">...</span></li>
                        </ul>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Fecha Devolución</label>
                        <input type="date" class="form-control" name="fecha_devolucion" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Estado del Activo al Devolver *</label>
                        <select class="form-select" name="estado_devolucion_id" required>
                            @foreach($estadosEntrega as $edo)
                                <option value="{{ $edo->id }}">{{ $edo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Observaciones de Devolución *</label>
                        <textarea class="form-control" name="observaciones" rows="3" required placeholder="Describe el estado del activo..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger px-4">Procesar Devolución</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalExitoPreview" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content h-100">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-check-circle-fill me-2"></i>Asignación Exitosa</h5>
                <button type="button" class="btn-close btn-close-white" onclick="location.reload()"></button>
            </div>
            <div class="modal-body p-0" style="height: 75vh;">
                <iframe id="pdfPreviewFrame" src="" width="100%" height="100%" style="border:none;"></iframe>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" onclick="location.reload()">Cerrar</button>
                <a href="#" id="btnDescargarPdf" target="_blank" class="btn btn-primary">
                    <i class="bi bi-download me-2"></i>Descargar PDF
                </a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDocumentos" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Expediente Digital</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3" id="docTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-subir">Subir Firmado</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-historial" id="btnLoadHistorial">Historial</button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-subir">
                        <form id="formSubirDoc">
                            <input type="hidden" name="asignacion_id" id="doc_asignacion_id">
                            <div class="mb-3 text-center p-4 border rounded-3 bg-light border-dashed">
                                <i class="bi bi-cloud-arrow-up fs-1 text-muted"></i>
                                <div class="mt-2">
                                    <label for="archivoInput" class="btn btn-sm btn-primary cursor-pointer">Seleccionar PDF/Imagen</label>
                                    <input type="file" name="documento" id="archivoInput" class="d-none" accept=".pdf,.jpg,.png" required onchange="mostrarNombreArchivo(this)">
                                </div>
                                <small class="d-block mt-2 text-muted" id="nombreArchivoSel">Ningún archivo seleccionado</small>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Subir Archivo</button>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="tab-historial">
                        <div class="list-group list-group-flush small" id="listaHistorial">
                            <div class="text-center py-3"><div class="spinner-border spinner-border-sm"></div> Cargando...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = '{{ csrf_token() }}';
        
        // Inicializar Modales
        const modalAsignarElement = document.getElementById('modalAsignar');
        const modalAsignarInstance = modalAsignarElement ? new bootstrap.Modal(modalAsignarElement) : null;

        const modalDevolverElement = document.getElementById('modalDevolver');
        const modalDevolverInstance = modalDevolverElement ? new bootstrap.Modal(modalDevolverElement) : null;

        const modalExitoElement = document.getElementById('modalExitoPreview');
        const modalExitoInstance = modalExitoElement ? new bootstrap.Modal(modalExitoElement) : null;

        const modalDocsElement = document.getElementById('modalDocumentos');
        const modalDocsInstance = modalDocsElement ? new bootstrap.Modal(modalDocsElement) : null;

        // --- LÓGICA MEJORADA DE ACTIVOS (Ocultar Duplicados) ---
        function actualizarOpcionesDisponibles() {
            const todosLosSelects = document.querySelectorAll('.select-activo');
            const valoresSeleccionados = Array.from(todosLosSelects)
                                            .map(s => s.value)
                                            .filter(v => v !== "");

            todosLosSelects.forEach(selectActual => {
                const valorActualDeEsteSelect = selectActual.value;
                const opciones = selectActual.querySelectorAll('option');

                opciones.forEach(opcion => {
                    if (opcion.value === "") return; // Ignorar placeholder

                    // Si está seleccionado en otro lado Y no es el valor actual de este input
                    if (valoresSeleccionados.includes(opcion.value) && opcion.value !== valorActualDeEsteSelect) {
                        opcion.style.display = 'none';
                        opcion.disabled = true;
                    } else {
                        opcion.style.display = 'block';
                        opcion.disabled = false;
                    }
                });
            });
        }

        // 1. NUEVA ASIGNACIÓN
        const btnNueva = document.getElementById('btnNuevaAsignacion');
        if (btnNueva && modalAsignarInstance) {
            btnNueva.addEventListener('click', () => {
                document.getElementById('formAsignar').reset();
                
                // Resetear filas a 1
                const container = document.getElementById('lista-activos-container');
                
                // Estrategia para resetear dejando la primera fila limpia
                const filas = container.querySelectorAll('.activo-row');
                filas.forEach((fila, index) => {
                    if (index === 0) {
                        const select = fila.querySelector('select');
                        select.value = "";
                        // Importante: Asegurar que el listener esté activo en la fila base
                        select.removeEventListener('change', actualizarOpcionesDisponibles); // Prevenir dobles
                        select.addEventListener('change', actualizarOpcionesDisponibles);
                        
                        fila.querySelector('.btn-remove-row').style.display = 'none';
                    } else {
                        fila.remove();
                    }
                });
                
                actualizarOpcionesDisponibles(); 
                modalAsignarInstance.show();
            });
        }

        // Agregar Fila
        const btnAgregar = document.getElementById('btnAgregarFila');
        if(btnAgregar) {
            btnAgregar.addEventListener('click', function() {
                const container = document.getElementById('lista-activos-container');
                const firstRow = container.querySelector('.activo-row');
                
                // Clonar
                const newRow = firstRow.cloneNode(true);
                const newSelect = newRow.querySelector('select');
                
                // Resetear y Configurar Nuevo Select
                newSelect.value = "";
                newSelect.addEventListener('change', actualizarOpcionesDisponibles);

                // Configurar Botón Eliminar
                const btnRemove = newRow.querySelector('.btn-remove-row');
                btnRemove.style.display = 'block';
                
                // Crear nuevo listener de eliminación para esta fila específica
                // (Evitamos usar cloneNode con eventos previos para tener control limpio)
                const newBtnRemove = btnRemove.cloneNode(true);
                btnRemove.parentNode.replaceChild(newBtnRemove, btnRemove);
                
                newBtnRemove.addEventListener('click', function() {
                    newRow.remove();
                    actualizarOpcionesDisponibles(); // Recalcular al borrar
                });

                container.appendChild(newRow);
                actualizarOpcionesDisponibles(); 
            });
        }

        // Listener inicial para la primera fila existente (si carga con HTML estático)
        const primerSelect = document.querySelector('.select-activo');
        if(primerSelect) {
            primerSelect.addEventListener('change', actualizarOpcionesDisponibles);
        }

        // Guardar Asignación (AJAX)
        const formAsignar = document.getElementById('formAsignar');
        if(formAsignar) {
            formAsignar.addEventListener('submit', function(e) {
                e.preventDefault();
                const btnSubmit = this.querySelector('button[type="submit"]');
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Procesando...';

                const formData = new FormData(this);
                const data = {
                    empleado_id: formData.get('empleado_id'),
                    fecha_asignacion: formData.get('fecha_asignacion'),
                    estado_entrega_id: formData.get('estado_entrega_id'),
                    observaciones: formData.get('observaciones'),
                    activos: formData.getAll('activos[]')
                };

                fetch('{{ route("asignaciones.store") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(resp => {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="bi bi-save me-2"></i>Guardar y Generar PDF';

                    if(resp.success) {
                        modalAsignarInstance.hide();
                        // Abrir Preview
                        const pdfUrl = `/asignaciones/carta-lote/${resp.lote_id}`; 
                        document.getElementById('pdfPreviewFrame').src = pdfUrl;
                        document.getElementById('btnDescargarPdf').href = pdfUrl;
                        modalExitoInstance.show();
                    } else {
                        alert('Error: ' + resp.message);
                    }
                })
                .catch(err => {
                    console.error(err);
                    btnSubmit.disabled = false;
                    alert('Error de conexión.');
                });
            });
        }

        // 2. BOTONES DEVOLVER
        const btnsDevolver = document.querySelectorAll('.btn-devolver-trigger');
        btnsDevolver.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                document.getElementById('formDevolver').reset();
                document.getElementById('dev_asignacion_id').value = id;
                
                document.getElementById('dev_activo_info').textContent = `${this.dataset.serie} - ${this.dataset.modelo}`;
                document.getElementById('dev_empleado_info').textContent = `${this.dataset.empleado} (${this.dataset.numEmpleado})`;
                document.getElementById('dev_fecha_asignacion').textContent = this.dataset.fecha;
                
                modalDevolverInstance.show();
            });
        });

        // Submit Devolver
        const formDevolver = document.getElementById('formDevolver');
        if(formDevolver) {
            formDevolver.addEventListener('submit', function(e) {
                e.preventDefault();
                const id = document.getElementById('dev_asignacion_id').value;
                const btnSubmit = this.querySelector('button[type="submit"]');
                btnSubmit.disabled = true;

                const formData = new FormData(this);
                const data = Object.fromEntries(formData.entries());

                fetch(`/asignaciones/${id}/devolver`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        modalDevolverInstance.hide();
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                        btnSubmit.disabled = false;
                    }
                })
                .catch(err => {
                    btnSubmit.disabled = false;
                    alert('Error de conexión.');
                });
            });
        }

        // 3. DOCUMENTOS
        document.querySelectorAll('.btn-subir-documento, .btn-ver-documento').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                document.getElementById('doc_asignacion_id').value = id;
                document.getElementById('formSubirDoc').reset();
                document.getElementById('nombreArchivoSel').textContent = 'Ningún archivo seleccionado';
                
                cargarHistorial(id);
                modalDocsInstance.show();
            });
        });

        window.mostrarNombreArchivo = function(input) {
            document.getElementById('nombreArchivoSel').textContent = input.files[0] ? input.files[0].name : '';
        };

        function cargarHistorial(id) {
            const lista = document.getElementById('listaHistorial');
            lista.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm"></div></div>';

            fetch(`/asignaciones/${id}/historial-documentos`)
                .then(res => res.json())
                .then(data => {
                    lista.innerHTML = '';
                    if(!data.historial || data.historial.length === 0) {
                        lista.innerHTML = '<div class="p-3 text-center text-muted">No hay documentos subidos aún.</div>';
                        return;
                    }
                    data.historial.forEach(doc => {
                        lista.innerHTML += `
                            <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="d-block text-dark">${doc.nombre_archivo_original}</strong>
                                    <small class="text-muted">${new Date(doc.fecha_subida).toLocaleString()}</small>
                                </div>
                                <a href="/storage/${doc.url_archivo}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        `;
                    });
                });
        }

        const formSubirDoc = document.getElementById('formSubirDoc');
        if(formSubirDoc) {
            formSubirDoc.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                
                fetch('{{ route("asignaciones.subir_documento") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        alert('Documento subido correctamente');
                        cargarHistorial(document.getElementById('doc_asignacion_id').value);
                        location.reload(); 
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            });
        }
    });
</script>
@endpush