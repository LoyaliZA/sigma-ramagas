@extends('layouts.app')

@section('title', 'Trazabilidad de Activos')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold">Seguimiento y Trazabilidad</h1>
            <p class="text-muted small mb-0">Rastreo de ubicación y asignaciones históricas</p>
        </div>
        
        {{-- Botón para limpiar filtros si hay alguno activo --}}
        @if($filtro || $busqueda)
            <a href="{{ route('seguimiento.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-x-lg me-1"></i>Limpiar Filtros
            </a>
        @endif
    </div>

    {{-- TARJETAS DE FILTRO (KPIs Clicables) --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('seguimiento.index', ['filtro' => 'total', 'q' => $busqueda]) }}" class="text-decoration-none">
                <div class="card card-dashboard h-100 shadow-sm {{ !$filtro || $filtro == 'total' ? 'border-primary ring-2 bg-soft-primary' : 'border-0' }}" style="{{ !$filtro || $filtro == 'total' ? 'border: 2px solid #4e73df !important;' : '' }}">
                    <div class="card-body d-flex align-items-center justify-content-between p-4">
                        <div>
                            <div class="kpi-label text-muted small fw-bold text-uppercase">Total Rastreados</div>
                            <div class="kpi-value h3 fw-bold text-gray-800 mb-0">{{ $kpis['total_rastreados'] }}</div>
                        </div>
                        <div class="icon-box bg-soft-primary text-primary rounded-3 p-3"><i class="bi bi-crosshair fs-4"></i></div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('seguimiento.index', ['filtro' => 'en_uso', 'q' => $busqueda]) }}" class="text-decoration-none">
                <div class="card card-dashboard h-100 shadow-sm {{ $filtro == 'en_uso' ? 'border-success' : 'border-0' }}" style="{{ $filtro == 'en_uso' ? 'border: 2px solid #1cc88a !important; background-color: #f0fdf4;' : '' }}">
                    <div class="card-body d-flex align-items-center justify-content-between p-4">
                        <div>
                            <div class="kpi-label text-muted small fw-bold text-uppercase">En Uso</div>
                            <div class="kpi-value h3 fw-bold text-gray-800 mb-0">{{ $kpis['en_uso'] }}</div>
                        </div>
                        <div class="icon-box bg-soft-success text-success rounded-3 p-3"><i class="bi bi-person-check fs-4"></i></div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('seguimiento.index', ['filtro' => 'disponibles', 'q' => $busqueda]) }}" class="text-decoration-none">
                <div class="card card-dashboard h-100 shadow-sm {{ $filtro == 'disponibles' ? 'border-info' : 'border-0' }}" style="{{ $filtro == 'disponibles' ? 'border: 2px solid #36b9cc !important; background-color: #f0f9ff;' : '' }}">
                    <div class="card-body d-flex align-items-center justify-content-between p-4">
                        <div>
                            <div class="kpi-label text-muted small fw-bold text-uppercase">Disponibles</div>
                            <div class="kpi-value h3 fw-bold text-gray-800 mb-0">{{ $kpis['disponibles'] }}</div>
                        </div>
                        <div class="icon-box bg-soft-info text-info rounded-3 p-3"><i class="bi bi-box-seam fs-4"></i></div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('seguimiento.index', ['filtro' => 'mantenimiento', 'q' => $busqueda]) }}" class="text-decoration-none">
                <div class="card card-dashboard h-100 shadow-sm {{ $filtro == 'mantenimiento' ? 'border-warning' : 'border-0' }}" style="{{ $filtro == 'mantenimiento' ? 'border: 2px solid #f6c23e !important; background-color: #fefce8;' : '' }}">
                    <div class="card-body d-flex align-items-center justify-content-between p-4">
                        <div>
                            <div class="kpi-label text-muted small fw-bold text-uppercase">Mantenimiento</div>
                            <div class="kpi-value h3 fw-bold text-gray-800 mb-0">{{ $kpis['mantenimiento'] }}</div>
                        </div>
                        <div class="icon-box bg-soft-warning text-warning rounded-3 p-3"><i class="bi bi-tools fs-4"></i></div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- BARRA DE BÚSQUEDA --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form action="{{ route('seguimiento.index') }}" method="GET">
                {{-- Mantenemos el filtro activo al buscar --}}
                @if($filtro)
                    <input type="hidden" name="filtro" value="{{ $filtro }}">
                @endif
                
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" class="form-control border-start-0 shadow-none ps-0" 
                           placeholder="Buscar por serie, modelo, marca o empleado..." value="{{ $busqueda ?? '' }}">
                    <button class="btn btn-primary px-4" type="submit">Buscar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-primary mb-0">
                <i class="bi bi-list-ul me-2"></i>Resultados 
                @if($filtro) 
                    <span class="text-muted fw-normal small">({{ ucfirst(str_replace('_', ' ', $filtro)) }})</span>
                @endif
            </h6>
            <small class="text-muted">Mostrando {{ $activos->count() }} resultados</small>
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @forelse($activos as $activo)
                    <a href="{{ route('seguimiento.show', $activo->id) }}" class="list-group-item list-group-item-action p-3 d-flex align-items-center justify-content-between border-bottom-light">
                        <div class="d-flex align-items-center">
                            <div class="avatar-md bg-light text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                @if(Str::contains(strtolower($activo->tipo->nombre ?? ''), 'laptop')) <i class="bi bi-laptop fs-5"></i>
                                @elseif(Str::contains(strtolower($activo->tipo->nombre ?? ''), 'impresora')) <i class="bi bi-printer fs-5"></i>
                                @elseif(Str::contains(strtolower($activo->tipo->nombre ?? ''), 'celular')) <i class="bi bi-phone fs-5"></i>
                                @else <i class="bi bi-box-seam fs-5"></i>
                                @endif
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">{{ $activo->tipo->nombre ?? 'Activo' }} {{ $activo->marca->nombre ?? '' }}</h6>
                                <small class="text-muted d-block">
                                    Serie: <span class="fw-medium text-dark">{{ $activo->numero_serie }}</span> 
                                    <span class="mx-1">•</span> 
                                    {{ Str::limit($activo->modelo, 30) }}
                                </small>
                            </div>
                        </div>
                        <div class="text-end">
                            @php
                                $badgeClass = match($activo->estado_id) {
                                    1 => 'bg-success-subtle text-success',
                                    2 => 'bg-primary-subtle text-primary',
                                    3 => 'bg-warning-subtle text-warning',
                                    4 => 'bg-warning-subtle text-warning',
                                    default => 'bg-light text-muted'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} rounded-pill px-3 mb-1">{{ $activo->estado->nombre ?? 'N/A' }}</span>
                            <div class="small text-muted" style="font-size: 0.75rem;">
                                <i class="bi bi-geo-alt me-1"></i>{{ $activo->ubicacion->nombre ?? 'Sin ubicación' }}
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-search fs-1 text-muted opacity-50"></i>
                        <p class="text-muted mt-3">No se encontraron activos con ese criterio.</p>
                        @if($filtro || $busqueda)
                            <a href="{{ route('seguimiento.index') }}" class="btn btn-sm btn-link">Borrar filtros</a>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>
        <div class="card-footer bg-white py-3">
            {{ $activos->links() }}
        </div>
    </div>
</div>
@endsection