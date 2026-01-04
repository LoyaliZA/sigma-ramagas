@extends('layouts.app')

@section('title', 'Asignaciones')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-0 text-gray-800">Control de Asignaciones</h2>
            <p class="text-muted small mb-0">Gestión de entregas y devoluciones de equipos</p>
        </div>
        <button type="button" class="btn btn-primary" onclick="abrirModalAsignar()">
            <i class="bi bi-box-arrow-right me-2"></i>Nueva Asignación
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Folio</th>
                            <th>Empleado</th>
                            <th>Activo (Serie / Modelo)</th>
                            <th>Fecha Asignación</th>
                            <th>Estatus</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($asignaciones as $asignacion)
                        <tr>
                            <td><small class="text-muted">{{ substr($asignacion->id, 0, 8) }}...</small></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-initials bg-primary text-white rounded-circle me-2 d-flex justify-content-center align-items-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                        {{ substr($asignacion->empleado->nombre, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $asignacion->empleado->nombre }} {{ $asignacion->empleado->apellido_paterno }}</div>
                                        <div class="small text-muted">{{ $asignacion->empleado->numero_empleado }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div><i class="bi bi-laptop me-1"></i> {{ $asignacion->activo->tipo->nombre ?? 'Activo' }}</div>
                                <div class="small text-muted">SN: {{ $asignacion->activo->numero_serie }}</div>
                            </td>
                            <td>{{ $asignacion->fecha_asignacion->format('d/m/Y') }}</td>
                            <td>
                                @if($asignacion->fecha_devolucion)
                                    <span class="badge bg-secondary">Devuelto</span>
                                    <div class="small text-muted">{{ $asignacion->fecha_devolucion->format('d/m/Y') }}</div>
                                @else
                                    <span class="badge bg-success">Vigente</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('asignaciones.carta', $asignacion->id) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Ver Carta Responsiva">
                                        <i class="bi bi-file-earmark-arrow-up"></i>
                                    </a>

                                    @if($asignacion->fecha_devolucion)
                                        <a href="{{ route('asignaciones.carta_devolucion', $asignacion->id) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="Ver Constancia Devolución">
                                            <i class="bi bi-file-earmark-check"></i>
                                        </a>
                                    @else
                                        <button class="btn btn-sm btn-outline-warning" onclick="abrirModalDevolver('{{ $asignacion->id }}', '{{ $asignacion->activo->numero_serie }}')" title="Registrar Devolución">
                                            <i class="bi bi-box-arrow-in-left"></i> Devolver
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No hay asignaciones registradas.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAsignar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Asignar Activo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAsignar">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Empleado <span class="text-danger">*</span></label>
                        <select class="form-select" name="empleado_id" required>
                            <option value="">Seleccionar empleado...</option>
                            @foreach($empleados as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->numero_empleado }} - {{ $emp->nombre }} {{ $emp->apellido_paterno }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Solo se muestran empleados activos.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Activo Disponible <span class="text-danger">*</span></label>
                        <select class="form-select" name="activo_id" required>
                            <option value="">Seleccionar activo...</option>
                            @foreach($activosDisponibles as $act)
                                <option value="{{ $act->id }}">
                                    [{{ $act->tipo->nombre ?? 'Gral' }}] {{ $act->modelo }} (SN: {{ $act->numero_serie }})
                                </option>
                            @endforeach
                        </select>
                        @if($activosDisponibles->isEmpty())
                            <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle"></i> No hay activos disponibles en almacén.</div>
                        @endif
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Fecha Asignación</label>
                            <input type="date" class="form-control" name="fecha_asignacion" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Condición Entrega</label>
                            <select class="form-select" name="estado_entrega_id" required>
                                @foreach($estadosEntrega as $edo)
                                    <option value="{{ $edo->id }}">{{ $edo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea class="form-control" name="observaciones" rows="2" placeholder="Accesorios entregados (cargador, mouse...), estado físico..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Realizar Asignación</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDevolver" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning-subtle">
                <h5 class="modal-title">Registrar Devolución</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formDevolver">
                <input type="hidden" name="asignacion_id" id="dev_asignacion_id">
                <div class="modal-body">
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2"></i>
                        <div>
                            Estás recibiendo el activo serie: <strong id="dev_serie"></strong>. 
                            Al confirmar, el activo quedará liberado.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fecha Devolución</label>
                        <input type="date" class="form-control" name="fecha_devolucion" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado de Recepción <span class="text-danger">*</span></label>
                        <select class="form-select" name="estado_devolucion_id" required>
                            @foreach($estadosEntrega as $edo)
                                <option value="{{ $edo->id }}">{{ $edo->nombre }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Si seleccionas "Dañado", el activo pasará a "En Diagnóstico".</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observaciones de Recepción</label>
                        <textarea class="form-control" name="observaciones" rows="2" placeholder="Faltantes, daños visibles..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Confirmar Devolución</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    var modalAsignar = new bootstrap.Modal(document.getElementById('modalAsignar'));
    var modalDevolver = new bootstrap.Modal(document.getElementById('modalDevolver'));

    function abrirModalAsignar() {
        document.getElementById('formAsignar').reset();
        modalAsignar.show();
    }

    function abrirModalDevolver(id, serie) {
        document.getElementById('formDevolver').reset();
        document.getElementById('dev_asignacion_id').value = id;
        document.getElementById('dev_serie').textContent = serie;
        modalDevolver.show();
    }

    // PROCESAR ASIGNACIÓN
    document.getElementById('formAsignar').addEventListener('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        var data = Object.fromEntries(formData.entries());

        fetch('{{ route("asignaciones.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert('Asignación exitosa');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    });

    // PROCESAR DEVOLUCIÓN
    document.getElementById('formDevolver').addEventListener('submit', function(e) {
        e.preventDefault();
        var id = document.getElementById('dev_asignacion_id').value;
        var formData = new FormData(this);
        var data = Object.fromEntries(formData.entries());

        fetch(`/asignaciones/${id}/devolver`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert('Devolución registrada');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    });
</script>
@endpush