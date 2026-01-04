@extends('layouts.app')

@section('title', 'Generación de Reportes')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Documentación y Reportes</h1>
            <p class="text-muted">Genera reportes y documentos formales del sistema</p>
        </div>
    </div>

    <ul class="nav nav-tabs mb-4" id="reporteTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="inventario-tab" data-bs-toggle="tab" data-bs-target="#inventario" type="button" role="tab">
                Inventario General
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="almacen-tab" data-bs-toggle="tab" data-bs-target="#almacen" type="button" role="tab">
                Control de Almacén
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="documentos-tab" data-bs-toggle="tab" data-bs-target="#documentos" type="button" role="tab">
                Documentos
            </button>
        </li>
    </ul>

    <div class="tab-content" id="reporteTabsContent">
        
        <div class="tab-pane fade show active" id="inventario" role="tabpanel">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-left-primary shadow-sm py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Reporte General de Inventario</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalActivos }} activos registrados</div>
                                    <p class="mt-2 text-sm text-muted">Listado completo de todos los activos tecnológicos de la empresa.</p>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-cubes fa-2x text-gray-300"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('reportes.inventario') }}" target="_blank" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-file-pdf"></i> Generar PDF
                                </a>
                                <a href="{{ route('reportes.inventario_csv') }}" class="btn btn-dark btn-sm">
                                    <i class="fas fa-file-csv"></i> CSV
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-left-success shadow-sm py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Reporte de Asignaciones Activas</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAsignados }} asignaciones activas</div>
                                    <p class="mt-2 text-sm text-muted">Activos que se encuentran actualmente en manos de empleados.</p>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('reportes.inventario', ['estado_id' => 2]) }}" target="_blank" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-file-pdf"></i> Generar PDF
                                </a>
                                <a href="{{ route('reportes.inventario_csv', ['estado_id' => 2]) }}" class="btn btn-dark btn-sm">
                                     <i class="fas fa-file-csv"></i> CSV
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Generar Reporte Personalizado</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('reportes.inventario') }}" method="GET" target="_blank">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Ubicación</label>
                                <select name="ubicacion_id" class="form-select">
                                    <option value="">Todas</option>
                                    @foreach($ubicaciones as $u)
                                        <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Estado</label>
                                <select name="estado_id" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($estados as $e)
                                        <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Tipo de Activo</label>
                                <select name="tipo_id" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($tipos as $t)
                                        <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter"></i> Generar Reporte
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="almacen" role="tabpanel">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-left-info shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-primary fw-bold text-uppercase mb-1">Activos Disponibles</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalDisponibles }} activos disponibles</div>
                                </div>
                                <i class="fas fa-box-open fa-2x text-gray-300"></i>
                            </div>
                            <p class="mt-3 text-muted small">Equipos en almacén listos para ser asignados.</p>
                            <a href="{{ route('reportes.inventario', ['estado_id' => 1]) }}" target="_blank" class="btn btn-outline-danger btn-sm w-100">
                                <i class="fas fa-file-pdf"></i> Reporte Disponibles
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-left-warning shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-warning fw-bold text-uppercase mb-1">En Mantenimiento</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalMantenimiento }} en proceso</div>
                                </div>
                                <i class="fas fa-tools fa-2x text-gray-300"></i>
                            </div>
                            <p class="mt-3 text-muted small">Equipos que requieren o están en proceso de reparación.</p>
                            <a href="{{ route('reportes.inventario', ['estado_id' => 3]) }}" target="_blank" class="btn btn-outline-danger btn-sm w-100">
                                <i class="fas fa-file-pdf"></i> Reporte Mantenimiento
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-left-danger shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-danger fw-bold text-uppercase mb-1">Activos Dados de Baja</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBajas }} activos en baja</div>
                                </div>
                                <i class="fas fa-trash-alt fa-2x text-gray-300"></i>
                            </div>
                            <p class="mt-3 text-muted small">Equipos retirados permanentemente del inventario.</p>
                            <a href="{{ route('reportes.bajas') }}" target="_blank" class="btn btn-outline-danger btn-sm w-100">
                                <i class="fas fa-file-pdf"></i> Reporte de Bajas
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="documentos" role="tabpanel">
            <div class="text-center py-5">
                <i class="fas fa-file-contract fa-4x text-gray-300 mb-3"></i>
                <h5>Generación Automática</h5>
                <p class="text-muted">Las Cartas Responsivas y de Devolución se generan individualmente desde el módulo de <strong>Asignaciones</strong>.</p>
                <a href="{{ route('asignaciones.index') }}" class="btn btn-primary">Ir a Asignaciones</a>
            </div>
        </div>
    </div>

</div>
@endsection