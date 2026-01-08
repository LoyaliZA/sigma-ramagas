@extends('layouts.app')

@section('title', 'Generación de Reportes')

@section('content')
<div class="container-fluid p-4">
    
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-0 fw-bold text-dark">Documentación y Reportes</h2>
            <p class="text-muted small mb-0">Genera reportes y documentos formales del sistema</p>
        </div>
    </div>

    {{-- Navegación de Pestañas --}}
    <ul class="nav nav-pills mb-4" id="reporteTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4" id="inventario-tab" data-bs-toggle="tab" data-bs-target="#inventario" type="button" role="tab">
                <i class="bi bi-box-seam me-2"></i>Inventario General
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 ms-2" id="almacen-tab" data-bs-toggle="tab" data-bs-target="#almacen" type="button" role="tab">
                <i class="bi bi-building me-2"></i>Control de Almacén
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 ms-2" id="documentos-tab" data-bs-toggle="tab" data-bs-target="#documentos" type="button" role="tab">
                <i class="bi bi-file-text me-2"></i>Documentos
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
                        <p class="text-muted small mb-3">Listado completo de todos los activos tecnológicos registrados en la empresa.</p>
                        <div class="d-flex gap-2">
                            <a href="{{ route('reportes.inventario') }}" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                <i class="bi bi-file-earmark-pdf me-2"></i>PDF
                            </a>
                            <a href="{{ route('reportes.inventario_csv') }}" class="btn btn-dark btn-sm rounded-pill px-3">
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
                        <p class="text-muted small mb-3">Activos que se encuentran actualmente en manos de empleados.</p>
                        <div class="d-flex gap-2">
                            <a href="{{ route('reportes.inventario', ['estado_id' => 2]) }}" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                <i class="bi bi-file-earmark-pdf me-2"></i>PDF
                            </a>
                            <a href="{{ route('reportes.inventario_csv', ['estado_id' => 2]) }}" class="btn btn-dark btn-sm rounded-pill px-3">
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
                {{-- Disponibles --}}
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
                        <p class="text-muted small mb-3">Equipos en almacén listos para ser asignados a personal.</p>
                        <a href="{{ route('reportes.inventario', ['estado_id' => 1]) }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill w-100">
                            <i class="bi bi-file-earmark-pdf me-2"></i>Reporte Disponibles
                        </a>
                    </div>
                </div>

                {{-- Mantenimiento --}}
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
                        <p class="text-muted small mb-3">Equipos que requieren o están en proceso de reparación.</p>
                        <a href="{{ route('reportes.inventario', ['estado_id' => 3]) }}" target="_blank" class="btn btn-outline-warning btn-sm rounded-pill w-100">
                            <i class="bi bi-file-earmark-pdf me-2"></i>Reporte Mantenimiento
                        </a>
                    </div>
                </div>

                {{-- Bajas --}}
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
                        <p class="text-muted small mb-3">Equipos retirados permanentemente del inventario operativo.</p>
                        <a href="{{ route('reportes.bajas') }}" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill w-100">
                            <i class="bi bi-file-earmark-pdf me-2"></i>Reporte de Bajas
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pestaña Documentos --}}
        <div class="tab-pane fade" id="documentos" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="bg-soft-primary d-inline-block rounded-circle p-4 mb-3 text-primary">
                        <i class="bi bi-file-earmark-richtext display-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Generación Automática de Documentos</h5>
                    <p class="text-muted" style="max-width: 600px; margin: 0 auto;">
                        Las Cartas Responsivas y de Devolución se generan individualmente desde el módulo de <strong>Asignaciones</strong> para garantizar la integridad de los datos vinculados al empleado.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('asignaciones.index') }}" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-arrow-right-circle me-2"></i>Ir a Asignaciones
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
}