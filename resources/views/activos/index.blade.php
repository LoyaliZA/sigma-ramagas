@extends('layouts.app')

@section('title', 'Inventario de Activos')

@section('content')
<div class="container-fluid p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-0 fw-bold text-dark">Inventario de Activos</h2>
            <p class="text-muted small mb-0">Gestión operativa del inventario (Activos Vivos)</p>
        </div>
        <div>
            <a href="{{ route('activos.bajas') }}" class="btn btn-outline-danger px-4 rounded-pill shadow-sm me-2">
                <i class="bi bi-trash3 me-2"></i>Ver Bajas
            </a>
            <button type="button" class="btn btn-primary px-4 rounded-pill shadow-sm" onclick="abrirModalCrear()">
                <i class="bi bi-plus-lg me-2"></i>Nuevo Activo
            </button>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card card-dashboard h-100 p-3 border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold">Total Activos</div>
                        <div class="h3 mb-0 fw-bold mt-1">{{ $kpiTotal ?? 0 }}</div>
                    </div>
                    <div class="icon-box bg-soft-primary rounded-circle text-primary fs-4 p-3">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-dashboard h-100 p-3 border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold">En Uso</div>
                        <div class="h3 mb-0 fw-bold mt-1 text-success">{{ $kpiEnUso ?? 0 }}</div>
                    </div>
                    <div class="icon-box bg-soft-success rounded-circle text-success fs-4 p-3">
                        <i class="bi bi-check-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-dashboard h-100 p-3 border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold">Disponibles</div>
                        <div class="h3 mb-0 fw-bold mt-1 text-secondary">{{ $kpiDisponibles ?? 0 }}</div>
                    </div>
                    <div class="icon-box bg-soft-secondary rounded-circle text-secondary fs-4 p-3">
                        <i class="bi bi-inbox"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-dashboard h-100 p-3 border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold">Mantenimiento</div>
                        <div class="h3 mb-0 fw-bold mt-1 text-warning">{{ $kpiMantenimiento ?? 0 }}</div>
                    </div>
                    <div class="icon-box bg-soft-warning rounded-circle text-warning fs-4 p-3">
                        <i class="bi bi-tools"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-dashboard mb-4 border-0 shadow-sm">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('activos.index') }}" class="row g-3 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 ps-3"><i
                                class="bi bi-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control border-start-0 ps-0"
                            placeholder="Buscar por código, serie, modelo..." value="{{ request('q') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="tipo_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Todos los tipos</option>
                        @foreach($tipos as $t)
                        <option value="{{ $t->id }}" {{ request('tipo_id') == $t->id ? 'selected' : '' }}>
                            {{ $t->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="estado_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Todos los estados</option>
                        @foreach($estados as $e)
                        <option value="{{ $e->id }}" {{ request('estado_id') == $e->id ? 'selected' : '' }}>
                            {{ $e->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 text-end">
                    <a href="{{ route('activos.index') }}" class="btn btn-light text-muted" title="Limpiar"><i
                            class="bi bi-arrow-counterclockwise"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-dashboard border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 border-0 text-muted small text-uppercase fw-bold">Activo</th>
                            <th class="py-3 border-0 text-muted small text-uppercase fw-bold">Tipo / Modelo</th>
                            <th class="py-3 border-0 text-muted small text-uppercase fw-bold">Especificaciones</th>
                            <th class="py-3 border-0 text-muted small text-uppercase fw-bold">Estado</th>
                            <th class="pe-4 py-3 border-0 text-end text-muted small text-uppercase fw-bold">Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activos as $activo)
                        <tr class="border-bottom">
                            <td class="ps-4 py-3">
                                <div class="fw-bold text-primary">{{ $activo->codigo_interno }}</div>
                                <div class="small text-muted">SN: {{ $activo->numero_serie }}</div>
                            </td>

                            <td class="py-3">
                                <div class="badge bg-light text-dark border fw-normal mb-1">
                                    {{ $activo->tipo->nombre ?? 'N/A' }}</div>
                                <div class="small text-dark fw-bold">{{ $activo->marca->nombre ?? '' }}
                                    {{ $activo->modelo }}</div>
                            </td>

                            <td class="py-3 small">
    {{-- BLOQUE 1: Información básica --}}
    <div class="mb-1">
        {{-- Usamos 'data_get' o verificamos que sea array para evitar errores si es null --}}
        @if(isset($activo->especificaciones['procesador'])) 
            <i class="bi bi-cpu me-1"></i> {{ $activo->especificaciones['procesador'] }}
        @elseif(isset($activo->especificaciones['imei']))
            <i class="bi bi-phone me-1"></i> IMEI: {{ $activo->especificaciones['imei'] }}
        @else
            {{-- Fallback visual si no hay datos específicos --}}
            <span class="text-muted opacity-50">-</span>
        @endif
    </div>

    {{-- BLOQUE 2: LÓGICA VIDA ÚTIL --}}
    @php
        $antiguedad = 0;
        if ($activo->fecha_adquisicion) {
            $antiguedad = $activo->fecha_adquisicion->diffInYears(now());
        }
    @endphp

    @if($antiguedad >= 4)
        {{-- ALERTA OBSOLETO --}}
        <span class="badge bg-warning text-dark border border-warning mt-1" 
              title="Equipo con {{ $antiguedad }} años de uso. Considerar renovación.">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>Obsoleto ({{ $antiguedad }} años)
        </span>
    @elseif($activo->fecha_adquisicion)
        {{-- INFO FECHA NORMAL --}}
        <div class="text-muted" style="font-size: 0.75rem;">
            <i class="bi bi-calendar-check me-1"></i>
            {{ $activo->fecha_adquisicion->format('Y') }}
            
            {{-- Solo mostramos paréntesis si tiene 1 año o más --}}
            @if($antiguedad > 0)
                <span class="ms-1">({{ $antiguedad }} {{ $antiguedad == 1 ? 'año' : 'años' }})</span>
            @endif
        </div>
    @endif
</td>

                            <td class="py-3">
                                @php
                                $bgClass = match($activo->estado_id) {
                                1 => 'bg-soft-secondary text-secondary', // Disponible
                                2 => 'bg-soft-success text-success', // En Uso
                                3, 4 => 'bg-soft-warning text-warning', // Mantenimiento
                                6 => 'bg-soft-danger text-danger', // Baja (no debería salir aquí, pero por si acaso)
                                default => 'bg-light text-muted'
                                };
                                @endphp
                                <span class="badge {{ $bgClass }} px-3 py-2 rounded-pill fw-bold">
                                    {{ $activo->estado->nombre ?? 'N/A' }}
                                </span>
                            </td>

                            <td class="pe-4 py-3 text-end">
                                <button class="btn btn-sm btn-light text-primary rounded-circle me-1"
                                    title="Ver Detalles" onclick="verActivo('{{ $activo->id }}')">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-light text-info rounded-circle me-1" title="Editar"
                                    onclick="editarActivo('{{ $activo->id }}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-light text-danger rounded-circle"
                                    title="Dar de Baja Definitiva"
                                    onclick="prepararBaja('{{ $activo->id }}', '{{ $activo->codigo_interno }}')">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted mb-2"><i class="bi bi-inbox fs-1"></i></div>
                                <p class="text-muted">No se encontraron activos activos.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($activos->hasPages())
            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                <small class="text-muted">Mostrando {{ $activos->firstItem() }}-{{ $activos->lastItem() }} de
                    {{ $activos->total() }}</small>
                {{ $activos->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="modalFormActivo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-white border-bottom-0">
                <h5 class="modal-title fw-bold" id="modalTitulo">Activo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formActivo">
                <input type="hidden" name="id" id="activo_id">
                <input type="hidden" name="_method" id="method_form" value="POST">

                <input type="hidden" name="ram_tipo_texto" id="ram_tipo_texto">
                <input type="hidden" name="disco_tipo_texto" id="disco_tipo_texto">

                <div class="modal-body">
                    <h6 class="text-primary text-uppercase small fw-bold mb-3 ls-1">Información General</h6>

                    <div class="row g-3 mb-3 d-none" id="divCodigoInterno">
                        <div class="col-12">
                            <label class="form-label small text-muted fw-bold">Código Institucional</label>
                            <input type="text" class="form-control bg-soft-primary fw-bold text-primary border-0"
                                id="codigo_interno_display" readonly>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label small text-muted fw-bold">Tipo <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <select class="form-select" name="tipo_id" id="tipo_id" required
                                    onchange="toggleFormulario()">
                                    <option value="">Seleccionar...</option>
                                    @foreach($tipos as $tipo)
                                    <option value="{{ $tipo->id }}" data-nombre="{{ Str::lower($tipo->nombre) }}">
                                        {{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="abrirQuickAdd('tipo')"><i class="bi bi-plus"></i></button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted fw-bold">Costo</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">$</span>
                                {{-- LÓGICA DE SEGURIDAD VISUAL: Solo Super Admin puede escribir --}}
                                <input type="number" step="0.01" class="form-control" name="costo" id="costo"
                                    placeholder="0.00" @unless(auth()->user()->hasRole('Super Admin'))
                                readonly
                                style="background-color: #e9ecef; cursor: not-allowed;"
                                title="Solo el Super Admin puede modificar costos"
                                @endunless
                                >
                            </div>
                            {{-- Mensaje para usuarios sin permiso --}}
                            @unless(auth()->user()->hasRole('Super Admin'))
                            <div class="form-text" style="font-size: 0.7rem;"><i class="bi bi-lock-fill"></i> Solo
                                lectura</div>
                            @endunless
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted fw-bold">Modelo</label>
                            <input type="text" class="form-control" name="modelo" id="modelo">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Número de Serie <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="numero_serie" id="numero_serie" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Ubicación <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <select class="form-select" name="ubicacion_id" id="ubicacion_id" required>
                                    <option value="">Seleccionar...</option>
                                    @foreach($ubicaciones as $ubicacion) <option value="{{ $ubicacion->id }}">
                                        {{ $ubicacion->nombre }}</option> @endforeach
                                </select>
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="abrirQuickAdd('ubicacion')"><i class="bi bi-plus"></i></button>
                            </div>
                        </div>
                    </div>

                    <div id="section-computo" class="d-none dynamic-section bg-light p-3 rounded-3 mb-4">
                        <h6 class="text-primary text-uppercase small fw-bold mb-3">Hardware & Sistema</h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small text-muted">Procesador (CPU)</label>
                                <input type="text" class="form-control" name="cpu_modelo" id="cpu_modelo"
                                    placeholder="Ej: Intel Core i5-12400">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Memoria RAM</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="ram_capacidad" id="ram_capacidad"
                                        placeholder="Cant.">
                                    <select class="form-select" name="ram_unidad" style="max-width: 80px;">
                                        <option value="GB">GB</option>
                                        <option value="TB">TB</option>
                                    </select>
                                    <select class="form-select" name="ram_tipo_id" id="ram_tipo_id"
                                        onchange="updateHiddenText('ram_tipo_id', 'ram_tipo_texto')">
                                        <option value="">Tipo...</option>
                                        @foreach($tiposRam as $tr) <option value="{{ $tr->id }}">{{ $tr->nombre }}
                                        </option> @endforeach
                                    </select>
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="abrirQuickAdd('ram_tipo')"><i class="bi bi-plus"></i></button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Almacenamiento</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="disco_capacidad"
                                        id="disco_capacidad" placeholder="Cant.">
                                    <select class="form-select" name="disco_unidad" style="max-width: 80px;">
                                        <option value="GB">GB</option>
                                        <option value="TB">TB</option>
                                    </select>
                                    <select class="form-select" name="disco_tipo_id" id="disco_tipo_id"
                                        onchange="updateHiddenText('disco_tipo_id', 'disco_tipo_texto')">
                                        <option value="">Tipo...</option>
                                        @foreach($tiposDisco as $td) <option value="{{ $td->id }}">{{ $td->nombre }}
                                        </option> @endforeach
                                    </select>
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="abrirQuickAdd('disco_tipo')"><i class="bi bi-plus"></i></button>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small text-muted">Sistema Operativo</label>
                                <input type="text" class="form-control" name="so_version" id="so_version"
                                    placeholder="Ej: Windows 11 Pro">
                            </div>
                        </div>
                    </div>

                    <div id="section-movil" class="d-none dynamic-section bg-light p-3 rounded-3 mb-4">
                        <h6 class="text-primary text-uppercase small fw-bold mb-3">Datos Móviles</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">IMEI (15 dígitos)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                                    <input type="text" class="form-control" id="editImei" name="imei" maxlength="15"
                                        pattern="\d{15}"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 15);">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Tamaño Pantalla</label>
                                <input type="text" class="form-control" name="pantalla_tamano" id="pantalla_tamano"
                                    placeholder="Ej: 6.1 pulgadas">
                            </div>
                        </div>
                    </div>

                    <h6 class="text-primary text-uppercase small fw-bold mb-3 mt-4 ls-1">Estado & Finanzas</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small text-muted fw-bold">Costo</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">$</span>
                                <input type="number" step="0.01" class="form-control" name="costo" id="costo"
                                    placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted fw-bold">Compra</label>
                            <input type="date" class="form-control" name="fecha_adquisicion" id="fecha_adquisicion">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted fw-bold">Garantía</label>
                            <input type="date" class="form-control" name="garantia_hasta" id="garantia_hasta">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Estado <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" name="estado_id" id="estado_id" required>
                                @foreach($estados as $estado) <option value="{{ $estado->id }}">{{ $estado->nombre }}
                                </option> @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Condición <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" name="condicion_id" id="condicion_id" required>
                                @foreach($condiciones as $condicion) <option value="{{ $condicion->id }}">
                                    {{ $condicion->nombre }}</option> @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-muted fw-bold">Observaciones</label>
                            <textarea class="form-control" name="spec_otras" id="spec_otras" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">Guardar Activo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalQuickAdd" tabindex="-1" style="z-index: 1060;">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title mb-0 small text-uppercase fw-bold" id="quickAddTitle">Nuevo Elemento</h6>
                <button type="button" class="btn-close btn-close-white" onclick="modalQuick.hide()"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="quickAddType">
                <input type="text" class="form-control mb-3" id="quickAddName" placeholder="Escribe el nombre...">
                <div class="d-grid">
                    <button type="button" class="btn btn-sm btn-primary" onclick="guardarQuickAdd()">Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ver Activo 
<div class="modal fade" id="modalVer" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Detalles del Activo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="verContent"></div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light btn-sm w-100" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div> -->

<div class="modal fade" id="modalBaja" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h6 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Baja Definitiva de
                    Activo</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formBaja">
                <input type="hidden" id="baja_activo_id" name="id">
                <div class="modal-body">
                    <div class="alert alert-soft-danger text-danger border-0 small mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Estás a punto de dar de baja el activo <strong id="lblActivoBaja"></strong>.
                        Esta acción retirará el equipo del inventario operativo permanentemente.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Motivo de Baja <span
                                class="text-danger">*</span></label>
                        <select class="form-select" name="motivo_baja_id" required>
                            <option value="">Seleccione...</option>
                            @if(isset($motivosBaja))
                            @foreach($motivosBaja as $motivo)
                            <option value="{{ $motivo->id }}">{{ $motivo->nombre }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Comentarios / Justificación <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control" name="comentarios" rows="3" required
                            placeholder="Detalle por qué se da de baja..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Baja</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalVer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" id="modalVerContent">
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// --- VARIABLES GLOBALES DE MODALES ---
var modalForm = new bootstrap.Modal(document.getElementById('modalFormActivo'));
var modalQuick = new bootstrap.Modal(document.getElementById('modalQuickAdd'));
var modalVer = new bootstrap.Modal(document.getElementById('modalVer'));
var modalBaja = new bootstrap.Modal(document.getElementById('modalBaja'));

// --- LÓGICA DE FORMULARIO DINÁMICO ---
function updateHiddenText(selectId, hiddenId) {
    var select = document.getElementById(selectId);
    if (select.selectedIndex >= 0) {
        document.getElementById(hiddenId).value = select.options[select.selectedIndex].text;
    }
}

function toggleFormulario() {
    // Ocultar todas las secciones dinámicas
    document.querySelectorAll('.dynamic-section').forEach(el => el.classList.add('d-none'));

    var select = document.getElementById('tipo_id');
    var selectedOption = select.options[select.selectedIndex];
    if (!selectedOption) return;

    var tipoNombre = (selectedOption.getAttribute('data-nombre') || '').toLowerCase();

    // Mostrar sección según palabra clave
    if (tipoNombre.includes('laptop') || tipoNombre.includes('pc') || tipoNombre.includes('desktop') || tipoNombre
        .includes('servidor') || tipoNombre.includes('computadora')) {
        document.getElementById('section-computo').classList.remove('d-none');
    } else if (tipoNombre.includes('celular') || tipoNombre.includes('tablet') || tipoNombre.includes('movil') ||
        tipoNombre.includes('iphone') || tipoNombre.includes('ipad')) {
        document.getElementById('section-movil').classList.remove('d-none');
    }
}

// --- ABRIR MODAL CREAR ---
function abrirModalCrear() {
    document.getElementById('formActivo').reset();
    document.getElementById('activo_id').value = '';
    document.getElementById('method_form').value = 'POST';
    document.getElementById('modalTitulo').textContent = 'Nuevo Activo';

    // El código interno se genera solo, así que lo ocultamos al crear
    document.getElementById('divCodigoInterno').classList.add('d-none');

    // Serie editable
    var inputSerie = document.getElementById('numero_serie');
    inputSerie.readOnly = false;

    toggleFormulario();
    modalForm.show();
}

// --- ABRIR MODAL EDITAR ---
function editarActivo(id) {
    fetch(`/activos/${id}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('activo_id').value = data.id;
            document.getElementById('method_form').value = 'PUT';
            document.getElementById('modalTitulo').textContent = 'Editar Activo';

            // Mostrar código interno (bloqueado)
            document.getElementById('divCodigoInterno').classList.remove('d-none');
            document.getElementById('codigo_interno_display').value = data.codigo_interno;

            document.getElementById('tipo_id').value = data.tipo_id;
            document.getElementById('marca_id').value = data.marca_id;
            document.getElementById('modelo').value = data.modelo;
            document.getElementById('numero_serie').value = data.numero_serie;
            document.getElementById('ubicacion_id').value = data.ubicacion_id;
            document.getElementById('estado_id').value = data.estado_id;
            document.getElementById('condicion_id').value = data.condicion_id;

            document.getElementById('costo').value = data.costo;
            // Manejo seguro de fechas para evitar error si vienen null
            document.getElementById('fecha_adquisicion').value = data.fecha_adquisicion ? data.fecha_adquisicion
                .substring(0, 10) : '';
            document.getElementById('garantia_hasta').value = data.garantia_hasta ? data.garantia_hasta.substring(0,
                10) : '';

            // Cargar specs
            var specs = data.especificaciones || {};
            document.getElementById('cpu_modelo').value = specs.procesador || '';
            var inputImei = document.getElementById('editImei');
            if (inputImei) inputImei.value = specs.imei || '';
            document.getElementById('pantalla_tamano').value = specs.pantalla || '';
            document.getElementById('so_version').value = specs.sistema_operativo || '';
            document.getElementById('spec_otras').value = specs.otras || data.observaciones || '';

            // RAM y Disco (Parsing básico)
            if (specs.ram) {
                let partes = specs.ram.split(' ');
                if (partes.length >= 2) {
                    document.getElementById('ram_capacidad').value = partes[0];
                }
            }
            if (specs.almacenamiento) {
                let partes = specs.almacenamiento.split(' ');
                if (partes.length >= 2) document.getElementById('disco_capacidad').value = partes[0];
            }

            toggleFormulario(); // Para mostrar los campos correctos (pc o movil)
            modalForm.show();
        })
        .catch(err => {
            console.error(err);
            alert("Error al cargar los datos del activo.");
        });
}

// --- GUARDAR ACTIVO ---
document.getElementById('formActivo').addEventListener('submit', function(e) {
    e.preventDefault();
    var id = document.getElementById('activo_id').value;
    var url = id ? `/activos/${id}` : '{{ route("activos.store") }}';

    var formData = new FormData(this);
    var data = Object.fromEntries(formData.entries());

    fetch(url, {
            method: document.getElementById('method_form').value,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => console.error(err));
});

function verActivo(id) {
    var modalEl = document.getElementById('modalVer');
    var modal = new bootstrap.Modal(modalEl);
    var contentEl = document.getElementById('modalVerContent');

    // 1. Mostrar spinner de carga
    contentEl.innerHTML = `
            <div class="modal-body text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2 text-muted small">Cargando ficha técnica...</p>
            </div>`;
    modal.show();

    // 2. Pedir la VISTA HTML al controlador
    fetch(`/activos/${id}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text()) // IMPORTANTE: Pedimos Texto/HTML, no JSON
        .then(html => {
            contentEl.innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            contentEl.innerHTML =
                `<div class="modal-body text-center text-danger py-5">Error al cargar datos.</div>`;
        });
}

// --- PROCESO DE BAJA (Confirmación) ---
function prepararBaja(id, codigo) {
    document.getElementById('baja_activo_id').value = id;
    document.getElementById('lblActivoBaja').textContent = codigo;
    document.getElementById('formBaja').reset();
    modalBaja.show();
}

document.getElementById('formBaja').addEventListener('submit', function(e) {
    e.preventDefault();
    var id = document.getElementById('baja_activo_id').value;
    var formData = new FormData(this);
    var data = Object.fromEntries(formData.entries());

    fetch(`/activos/${id}/baja`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        });
});

// --- QUICK ADD (Catalogos rápidos) ---
function abrirQuickAdd(tipo) {
    document.getElementById('quickAddType').value = tipo;
    document.getElementById('quickAddName').value = '';
    var titulo = 'Elemento';
    if (tipo === 'ram_tipo') titulo = 'Tipo de RAM';
    if (tipo === 'disco_tipo') titulo = 'Tipo de Almacenamiento';
    document.getElementById('quickAddTitle').textContent = 'Nuevo ' + titulo;
    modalQuick.show();
}

function guardarQuickAdd() {
    var tipo = document.getElementById('quickAddType').value;
    var nombre = document.getElementById('quickAddName').value;
    if (!nombre) return alert('Escribe un nombre');

    fetch('{{ route("activos.quick_add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                tipo_catalogo: tipo,
                nombre: nombre
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                var selectId = tipo + '_id';
                // Ajuste para IDs simples vs compuestos
                if (tipo === 'tipo' || tipo === 'marca' || tipo === 'ubicacion') selectId = tipo + '_id';

                var select = document.getElementById(selectId);
                var opt = new Option(data.data.nombre, data.data.id);
                if (tipo === 'tipo') opt.setAttribute('data-nombre', data.data.nombre.toLowerCase());

                select.add(opt);
                select.value = data.data.id;

                // Actualizar hiddens
                if (tipo === 'ram_tipo') document.getElementById('ram_tipo_texto').value = data.data.nombre;
                if (tipo === 'disco_tipo') document.getElementById('disco_tipo_texto').value = data.data.nombre;

                toggleFormulario();
                modalQuick.hide();
            } else {
                alert(data.message);
            }
        });
}
</script>
@endpush