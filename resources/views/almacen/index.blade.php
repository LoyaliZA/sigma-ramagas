@extends('layouts.app')

@section('title', 'Almacén Central')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold">Almacén Central</h1>
            <p class="text-muted small mb-0">Gestión de inventario y control de existencias</p>
        </div>
        <div>
            <span class="badge bg-white text-primary border shadow-sm p-2">
                <i class="bi bi-building me-1"></i> Inventario General
            </span>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card card-dashboard h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <div class="kpi-label text-muted small text-uppercase fw-bold">Total Artículos</div>
                        <div class="kpi-value h3 fw-bold text-gray-800 mb-0">{{ $kpis['total_items'] }}</div>
                    </div>
                    <div class="icon-box bg-soft-primary text-primary rounded-3 p-3">
                        <i class="bi bi-box-seam fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-dashboard h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <div class="kpi-label text-muted small text-uppercase fw-bold">Disponibles</div>
                        <div class="kpi-value h3 fw-bold text-gray-800 mb-0">{{ $kpis['total_disponibles'] }}</div>
                        <div class="small text-success mt-1"><i class="bi bi-check-circle"></i> Listos para asignar</div>
                    </div>
                    <div class="icon-box bg-soft-success text-success rounded-3 p-3">
                        <i class="bi bi-check-lg fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-dashboard h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <div class="kpi-label text-muted small text-uppercase fw-bold">En Revisión</div>
                        <div class="kpi-value h3 fw-bold text-gray-800 mb-0">{{ $kpis['total_reparacion'] }}</div>
                        <div class="small text-warning mt-1"><i class="bi bi-tools"></i> Mto. o Diagnóstico</div>
                    </div>
                    <div class="icon-box bg-soft-warning text-warning rounded-3 p-3">
                        <i class="bi bi-exclamation-triangle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-dashboard h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <div class="kpi-label text-muted small text-uppercase fw-bold">Valor Inventario</div>
                        <div class="kpi-value h3 fw-bold text-gray-800 mb-0">${{ number_format($kpis['total_valor'], 2) }}</div>
                    </div>
                    <div class="icon-box bg-soft-info text-info rounded-3 p-3">
                        <i class="bi bi-currency-dollar fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-dashboard border-0 shadow-sm">
        <div class="card-header bg-white border-bottom-0 pt-4 px-4">
            <ul class="nav nav-tabs card-header-tabs" id="almacenTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active text-primary fw-medium" id="todo-tab" data-bs-toggle="tab" data-bs-target="#todo" type="button" role="tab">
                        <i class="bi bi-collection me-2"></i>Todo
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-success fw-medium" id="dispo-tab" data-bs-toggle="tab" data-bs-target="#dispo" type="button" role="tab">
                        <i class="bi bi-check-circle me-2"></i>Disponibles
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-warning fw-medium" id="diag-tab" data-bs-toggle="tab" data-bs-target="#diag" type="button" role="tab">
                        <i class="bi bi-search me-2"></i>Diagnóstico
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-info fw-medium" id="mto-tab" data-bs-toggle="tab" data-bs-target="#mto" type="button" role="tab">
                        <i class="bi bi-tools me-2"></i>Mantenimiento
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-secondary fw-medium" id="bajas-tab" data-bs-toggle="tab" data-bs-target="#bajas" type="button" role="tab">
                        <i class="bi bi-archive me-2"></i>Bajas / Pendientes
                    </button>
                </li>
            </ul>
        </div>
        
        <div class="card-body p-0">
            <div class="tab-content" id="almacenTabsContent">
                <div class="tab-pane fade show active" id="todo" role="tabpanel">
                    @include('almacen.partials.tabla', ['activos' => $activosAlmacen])
                </div>
                <div class="tab-pane fade" id="dispo" role="tabpanel">
                    @include('almacen.partials.tabla', ['activos' => $disponibles])
                </div>
                <div class="tab-pane fade" id="diag" role="tabpanel">
                    @include('almacen.partials.tabla', ['activos' => $enDiagnostico])
                </div>
                <div class="tab-pane fade" id="mto" role="tabpanel">
                    @include('almacen.partials.tabla', ['activos' => $enMantenimiento])
                </div>
                <div class="tab-pane fade" id="bajas" role="tabpanel">
                    @include('almacen.partials.tabla', ['activos' => $bajas])
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEstado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Mover Activo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formEstado">
                    <input type="hidden" id="activo_id_estado">
                    <div class="mb-3">
                        <label class="form-label fw-medium text-muted small text-uppercase">Nuevo Estado</label>
                        <select class="form-select bg-light border-0" id="nuevo_estado_id" required>
                            @foreach($estados as $estado)
                                <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium text-muted small text-uppercase">Observaciones</label>
                        <textarea class="form-control bg-light border-0" id="obs_estado" rows="3" placeholder="Motivo del cambio..."></textarea>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Guardar Cambio
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalVerActivo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" id="modalVerActivoContent">
            </div>
    </div>
</div>

@push('scripts')
<script>
    var modalEstado = new bootstrap.Modal(document.getElementById('modalEstado'));

    window.abrirModalEstado = function(id, estadoActualId) {
        document.getElementById('activo_id_estado').value = id;
        document.getElementById('nuevo_estado_id').value = estadoActualId;
        document.getElementById('obs_estado').value = '';
        modalEstado.show();
    }

    document.getElementById('formEstado').addEventListener('submit', function(e) {
        e.preventDefault();
        var id = document.getElementById('activo_id_estado').value;
        var estado = document.getElementById('nuevo_estado_id').value;
        var obs = document.getElementById('obs_estado').value;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/almacen/${id}/cambiar-estado`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ nuevo_estado_id: estado, observaciones: obs })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error al actualizar');
            }
        })
        .catch(err => console.error(err));
    });

    function verActivo(id) {
        var modalEl = document.getElementById('modalVerActivo');
        var modal = new bootstrap.Modal(modalEl);
        var contentEl = document.getElementById('modalVerActivoContent');

        // 1. Mostrar spinner mientras carga
        contentEl.innerHTML = `
            <div class="modal-body text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2 text-muted small">Cargando ficha técnica...</p>
            </div>`;
        modal.show();

        // 2. Pedir la vista al controlador
        fetch(`/activos/${id}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            contentEl.innerHTML = html; // Poner el diseño bonito
        });
    }
</script>
@endpush
@endsection