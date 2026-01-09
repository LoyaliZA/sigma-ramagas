@extends('layouts.app')

@section('title', 'Generación de Reportes')

@section('content')
<div class="container-fluid p-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-0 fw-bold text-dark">Documentación y Reportes</h2>
            <p class="text-muted small mb-0">Genera reportes y gestiona expedientes digitales</p>
        </div>
    </div>

    {{-- Navegación de Pestañas --}}
    <ul class="nav nav-pills mb-4" id="reporteTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4" id="inventario-tab" data-bs-toggle="tab"
                data-bs-target="#inventario" type="button" role="tab">
                <i class="bi bi-box-seam me-2"></i>Inventario General
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 ms-2" id="almacen-tab" data-bs-toggle="tab"
                data-bs-target="#almacen" type="button" role="tab">
                <i class="bi bi-building me-2"></i>Control de Almacén
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 ms-2" id="documentos-tab" data-bs-toggle="tab"
                data-bs-target="#documentos" type="button" role="tab">
                <i class="bi bi-folder2-open me-2"></i>Expedientes Digitales
            </button>
        </li>
    </ul>

    <div class="tab-content" id="reporteTabsContent">

        {{-- Pestaña Inventario --}}
        <div class="tab-pane fade show active" id="inventario" role="tabpanel">
            <div class="row g-4 mb-4">
                {{-- KPI: Inventario General --}}
                <div class="col-md-6 col-xl-6">
                    <div class="card card-dashboard h-100 p-3 border-0 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="text-muted small text-uppercase fw-bold">Reporte General</div>
                                <div class="h3 mb-0 fw-bold mt-1 text-primary">{{ $totalActivos }} activos</div>
                            </div>
                            <div class="icon-box bg-soft-primary rounded-circle text-primary fs-4 p-3">
                                <i class="bi bi-clipboard-data"></i>
                            </div>
                        </div>
                        <p class="text-muted small mb-3">Listado completo de todos los activos tecnológicos registrados.
                        </p>
                        <div class="d-flex gap-2">
                            <a href="{{ route('reportes.inventario') }}" target="_blank"
                                class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                <i class="bi bi-file-earmark-pdf me-2"></i>PDF
                            </a>
                            <a href="{{ route('reportes.inventario_csv') }}"
                                class="btn btn-dark btn-sm rounded-pill px-3">
                                <i class="bi bi-filetype-csv me-2"></i>CSV
                            </a>
                        </div>
                    </div>
                </div>

                {{-- KPI: Asignaciones Activas --}}
                <div class="col-md-6 col-xl-6">
                    <div class="card card-dashboard h-100 p-3 border-0 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="text-muted small text-uppercase fw-bold">Asignaciones Activas</div>
                                <div class="h3 mb-0 fw-bold mt-1 text-success">{{ $totalAsignados }} asignados</div>
                            </div>
                            <div class="icon-box bg-soft-success rounded-circle text-success fs-4 p-3">
                                <i class="bi bi-people"></i>
                            </div>
                        </div>
                        <p class="text-muted small mb-3">Activos actualmente en posesión de empleados.</p>
                        <div class="d-flex gap-2">
                            <a href="{{ route('reportes.inventario', ['estado_id' => 2]) }}" target="_blank"
                                class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                <i class="bi bi-file-earmark-pdf me-2"></i>PDF
                            </a>
                            <a href="{{ route('reportes.inventario_csv', ['estado_id' => 2]) }}"
                                class="btn btn-dark btn-sm rounded-pill px-3">
                                <i class="bi bi-filetype-csv me-2"></i>CSV
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filtros Personalizados --}}
            <div class="card card-dashboard border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="m-0 fw-bold text-dark"><i class="bi bi-sliders me-2"></i>Reporte Personalizado</h6>
                </div>
                <div class="card-body pt-0">
                    <form action="{{ route('reportes.inventario') }}" method="GET" target="_blank">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small text-muted fw-bold">Ubicación</label>
                                <select name="ubicacion_id" class="form-select">
                                    <option value="">Todas</option>
                                    @foreach($ubicaciones as $u)
                                    <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small text-muted fw-bold">Estado</label>
                                <select name="estado_id" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($estados as $e)
                                    <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small text-muted fw-bold">Tipo de Activo</label>
                                <select name="tipo_id" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($tipos as $t)
                                    <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-printer me-2"></i>Generar Reporte
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Pestaña Almacén --}}
        <div class="tab-pane fade" id="almacen" role="tabpanel">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card card-dashboard h-100 p-3 border-0 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="text-muted small text-uppercase fw-bold">Disponibles</div>
                                <div class="h3 mb-0 fw-bold mt-1 text-secondary">{{ $totalDisponibles }} activos</div>
                            </div>
                            <div class="icon-box bg-soft-secondary rounded-circle text-secondary fs-4 p-3">
                                <i class="bi bi-inbox"></i>
                            </div>
                        </div>
                        <a href="{{ route('reportes.inventario', ['estado_id' => 1]) }}" target="_blank"
                            class="btn btn-outline-secondary btn-sm rounded-pill w-100">
                            <i class="bi bi-file-earmark-pdf me-2"></i>Reporte Disponibles
                        </a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-dashboard h-100 p-3 border-0 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="text-muted small text-uppercase fw-bold">En Mantenimiento</div>
                                <div class="h3 mb-0 fw-bold mt-1 text-warning">{{ $totalMantenimiento }} proceso</div>
                            </div>
                            <div class="icon-box bg-soft-warning rounded-circle text-warning fs-4 p-3">
                                <i class="bi bi-tools"></i>
                            </div>
                        </div>
                        <a href="{{ route('reportes.inventario', ['estado_id' => 3]) }}" target="_blank"
                            class="btn btn-outline-warning btn-sm rounded-pill w-100">
                            <i class="bi bi-file-earmark-pdf me-2"></i>Reporte Mantenimiento
                        </a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-dashboard h-100 p-3 border-0 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="text-muted small text-uppercase fw-bold">Bajas</div>
                                <div class="h3 mb-0 fw-bold mt-1 text-danger">{{ $totalBajas }} activos</div>
                            </div>
                            <div class="icon-box bg-soft-danger rounded-circle text-danger fs-4 p-3">
                                <i class="bi bi-trash3"></i>
                            </div>
                        </div>
                        <a href="{{ route('reportes.bajas') }}" target="_blank"
                            class="btn btn-outline-danger btn-sm rounded-pill w-100">
                            <i class="bi bi-file-earmark-pdf me-2"></i>Reporte de Bajas
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pestaña Expedientes Digitales (ACTUALIZADA) --}}
        <div class="tab-pane fade" id="documentos" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-dark"><i class="bi bi-folder-symlink me-2"></i>Directorio de Empleados
                    </h6>
                    <div class="input-group" style="width: 300px;">
                        <span class="input-group-text bg-light border-end-0"><i
                                class="bi bi-search text-muted"></i></span>
                        <input type="text" id="buscadorEmpleados" class="form-control bg-light border-start-0"
                            placeholder="Buscar empleado...">
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th class="ps-4">Empleado</th>
                                    <th>Departamento</th>
                                    <th>Documentos Cargados</th>
                                    <th class="text-end pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="listaEmpleados">
                                @forelse($empleados as $empleado)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $empleado->nombre }}
                                            {{ $empleado->apellido_paterno }}</div>
                                        <div class="small text-muted">{{ $empleado->numero_empleado }}</div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-light text-dark border">{{ $empleado->departamento->nombre ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-primary text-primary rounded-pill px-3">
                                            {{ $empleado->documentos->count() }} archivos
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        {{-- Botón que abre el modal y pasa los datos del empleado y sus docs vía data-attributes --}}
                                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                            onclick='abrirExpediente(@json($empleado))'>
                                            <i class="bi bi-folder2-open me-2"></i>Gestionar Expediente
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No hay empleados activos.</td>
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

{{-- MODAL DE EXPEDIENTE DIGITAL --}}
<div class="modal fade" id="modalExpediente" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <div>
                    <h6 class="modal-title fw-bold mb-0">Expediente Digital</h6>
                    <small id="modalEmpleadoNombre" class="opacity-75"></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    {{-- Columna Izquierda: Formulario de Subida --}}
                    {{-- ... dentro del modalExpediente ... --}}
                    <div class="col-md-4 bg-light p-4 border-end">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-cloud-upload me-2"></i>Nuevo Documento
                        </h6>

                        <form id="formSubirDocExpediente" enctype="multipart/form-data">
                            @csrf
                            {{-- Input oculto para el ID del empleado --}}
                            <input type="hidden" name="empleado_id" id="empleadoIdUpload">

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Tipo de Documento</label>
                                <select class="form-select form-select-sm" name="tipo_documento" id="selectTipoDoc"
                                    required onchange="toggleInputPersonalizado(this)">
                                    <option value="">Seleccione...</option>
                                    <option value="Carta Responsiva">Carta Responsiva</option>
                                    <option value="Carta de Devolución">Carta de Devolución</option>
                                    <option value="Reporte de Daño/Incidencia">Reporte de Daño/Incidencia</option>
                                    <option value="Reporte de Robo/Extravío">Reporte de Robo/Extravío</option>
                                    <option value="Dictamen Técnico">Dictamen Técnico</option>
                                    <option value="Ticket de Soporte">Ticket de Soporte</option>
                                    <option value="Personalizar">Personalizar...</option>
                                </select>
                            </div>

                            {{-- Este div está oculto por defecto (d-none) y se muestra con JS --}}
                            <div class="mb-3 d-none" id="divInputPersonalizado">
                                <label class="form-label small fw-bold text-primary">Nombre del Documento</label>
                                <input type="text" class="form-control form-control-sm" name="nombre_personalizado"
                                    id="inputPersonalizado" placeholder="Ej: Manual de Usuario Específico">
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Archivo (PDF/Img)</label>
                                <input type="file" class="form-control form-control-sm" name="archivo"
                                    accept=".pdf,.jpg,.jpeg,.png" required>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-upload me-2"></i>Subir Archivo
                                </button>
                            </div>
                        </form>
                    </div>
                    {{-- ... --}}

                    {{-- Columna Derecha: Lista de Archivos --}}
                    <div class="col-md-8 p-4">
                        <h6 class="fw-bold text-dark mb-3">Documentos Archivados</h6>
                        <div id="listaDocumentosContainer" style="max-height: 300px; overflow-y: auto;">
                            {{-- Se llena dinámicamente con JS --}}
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
var modalExpediente = new bootstrap.Modal(document.getElementById('modalExpediente'));

// --- BUSCADOR DE EMPLEADOS ---
document.getElementById('buscadorEmpleados').addEventListener('keyup', function() {
    let filtro = this.value.toLowerCase();
    let filas = document.querySelectorAll('#listaEmpleados tr');

    filas.forEach(fila => {
        let texto = fila.innerText.toLowerCase();
        fila.style.display = texto.includes(filtro) ? '' : 'none';
    });
});

// --- ABRIR MODAL ---
function abrirExpediente(empleado) {
    // 1. Configurar datos del modal
    document.getElementById('modalEmpleadoNombre').textContent =
        `${empleado.nombre} ${empleado.apellido_paterno} - ${empleado.numero_empleado}`;
    document.getElementById('empleadoIdUpload').value = empleado.id;

    // 2. Renderizar lista de documentos
    renderizarDocumentos(empleado.documentos, empleado.id);

    // 3. Configurar Formulario de Subida (Actualizar Action URL dinámicamente)
    const form = document.getElementById('formSubirDocExpediente');
    form.dataset.empleadoId = empleado.id; // Guardamos ID para recargar luego si es necesario

    modalExpediente.show();
}

// --- RENDERIZAR DOCUMENTOS ---
function renderizarDocumentos(documentos, empleadoId) {
    const container = document.getElementById('listaDocumentosContainer');
    container.innerHTML = '';

    if (!documentos || documentos.length === 0) {
        container.innerHTML = `
                <div class="text-center text-muted py-5">
                    <i class="bi bi-folder-x display-4 opacity-50"></i>
                    <p class="mt-2 small">El expediente está vacío.</p>
                </div>`;
        return;
    }

    let html = '<div class="list-group list-group-flush">';
    documentos.forEach(doc => {
        // Ajustamos la ruta para que sea accesible (asumiendo storage linkeado)
        let url = `/storage/${doc.ruta_archivo}`;
        let icono = doc.ruta_archivo.endsWith('.pdf') ? 'bi-file-earmark-pdf text-danger' :
            'bi-file-earmark-image text-primary';

        html += `
                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <div class="d-flex align-items-center overflow-hidden">
                        <i class="bi ${icono} fs-4 me-3"></i>
                        <div class="text-truncate">
                            <div class="fw-bold text-dark small text-truncate" style="max-width: 200px;" title="${doc.nombre}">${doc.nombre}</div>
                            <div class="text-muted" style="font-size: 0.7rem;">
                                ${doc.tipo_documento || 'Documento'} • ${new Date(doc.created_at).toLocaleDateString()}
                            </div>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="${url}" target="_blank" class="btn btn-xs btn-light border text-primary me-1" title="Ver"><i class="bi bi-eye"></i></a>
                        <button class="btn btn-xs btn-light border text-danger" title="Eliminar" onclick="eliminarDocumento('${doc.id}', '${empleadoId}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            `;
    });
    html += '</div>';
    container.innerHTML = html;
}

// --- SUBIR DOCUMENTO (AJAX) ---
document.getElementById('formSubirDocExpediente').addEventListener('submit', function(e) {
    e.preventDefault();

    let empleadoId = this.dataset.empleadoId; // O del input hidden
    if (!empleadoId) empleadoId = document.getElementById('empleadoIdUpload').value;

    let formData = new FormData(this);
    let btn = this.querySelector('button[type="submit"]');
    let originalText = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Subiendo...';

    fetch(`/empleados/${empleadoId}/documentos`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Limpiar form
                document.getElementById('formSubirDocExpediente').reset();
                // Simular recarga de lista (o recargar pagina)
                // Lo ideal sería que el servidor devuelva la nueva lista, pero para simplificar, recargamos la página
                // o añadimos el elemento manualmente.
                alert('Documento subido correctamente.');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error al subir el archivo.');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
});

// --- ELIMINAR DOCUMENTO ---
function eliminarDocumento(docId, empleadoId) {
    if (!confirm('¿Estás seguro de eliminar este documento permanentemente?')) return;

    fetch(`/empleados/documentos/${docId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Documento eliminado.');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => alert('Error de conexión al eliminar.'));
}

// --- TOGGLE INPUT PERSONALIZADO ---
    function toggleInputPersonalizado(select) {
        const div = document.getElementById('divInputPersonalizado');
        const input = document.getElementById('inputPersonalizado');
        
        if (select.value === 'Personalizar') {
            div.classList.remove('d-none');
            input.required = true; // Hacemos obligatorio el campo si se selecciona Personalizar
            input.focus();
        } else {
            div.classList.add('d-none');
            input.required = false; // Quitamos obligatorio si no se usa
            input.value = ''; // Limpiamos valor
        }
    }
</script>
@endpush