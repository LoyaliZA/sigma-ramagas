@extends('layouts.app')

@section('title', 'Control de Almacén')

@section('content')
<div class="container-fluid p-4">
    <h2 class="h4 mb-4 text-gray-800">Control de Almacén</h2>

    <div class="row mb-4 g-3">
        <div class="col-md-3">
            <div class="card border-start border-success border-4 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Disponibles</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $disponibles->count() }}</div>
                        </div>
                        <div class="col-auto"><i class="bi bi-box-seam fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-warning border-4 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">En Diagnóstico</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $enDiagnostico->count() }}</div>
                        </div>
                        <div class="col-auto"><i class="bi bi-activity fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-danger border-4 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Mantenimiento</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $enMantenimiento->count() }}</div>
                        </div>
                        <div class="col-auto"><i class="bi bi-tools fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
         <div class="col-md-3">
            <div class="card border-start border-secondary border-4 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Pendientes Baja</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $bajas->count() }}</div>
                        </div>
                        <div class="col-auto"><i class="bi bi-trash fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="almacenTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-disponibles" type="button">
                        <i class="bi bi-box-seam me-2"></i>Disponibles
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-diagnostico" type="button">
                         <i class="bi bi-activity me-2"></i>En Diagnóstico
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-mantenimiento" type="button">
                        <i class="bi bi-tools me-2"></i>En Mantenimiento
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-bajas" type="button">
                        <i class="bi bi-trash me-2"></i>Pendientes de Baja
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="tab-disponibles">
                    @include('almacen.partials.tabla', ['activos' => $disponibles, 'contexto' => 'disponible'])
                </div>
                <div class="tab-pane fade" id="tab-diagnostico">
                    @include('almacen.partials.tabla', ['activos' => $enDiagnostico, 'contexto' => 'diagnostico'])
                </div>
                <div class="tab-pane fade" id="tab-mantenimiento">
                    @include('almacen.partials.tabla', ['activos' => $enMantenimiento, 'contexto' => 'mantenimiento'])
                </div>
                 <div class="tab-pane fade" id="tab-bajas">
                    @include('almacen.partials.tabla', ['activos' => $bajas, 'contexto' => 'bajas'])
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEstado" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cambiar Estado / Mover</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEstado">
                    <input type="hidden" id="activo_id_estado">
                    <div class="mb-3">
                        <label class="form-label">Nuevo Estado</label>
                        <select class="form-select" id="nuevo_estado_id" required>
                            @foreach($estados as $est)
                                <option value="{{ $est->id }}">{{ $est->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observaciones / Diagnóstico</label>
                        <textarea class="form-control" id="obs_estado" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Guardar Cambio</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    var modalEstado = new bootstrap.Modal(document.getElementById('modalEstado'));

    function abrirModalEstado(id, estadoActualId) {
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

        fetch(`/almacen/${id}/cambiar-estado`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ nuevo_estado_id: estado, observaciones: obs })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        });
    });
</script>
@endpush
@endsection