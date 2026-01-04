@extends('layouts.app')

@section('title', 'Seguimiento de Activos')

@section('content')
<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            
            <div class="text-center mb-4 mt-3">
                <h2 class="fw-bold text-primary">Seguimiento y Trazabilidad</h2>
                <p class="text-muted">Busca por serie, marca, tipo o explora el inventario.</p>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <form action="{{ route('seguimiento.index') }}" method="GET" id="formBusqueda">
                        <input type="hidden" name="limit" value="{{ $limit }}"> 
                        
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="q" class="form-control border-start-0 shadow-none" 
                                   placeholder="Buscar activo..." value="{{ $busqueda ?? '' }}">
                            @if($busqueda)
                                <a href="{{ route('seguimiento.index') }}" class="btn btn-outline-secondary border-start-0 border-end-0" title="Limpiar"><i class="bi bi-x-lg"></i></a>
                            @endif
                            <button class="btn btn-primary px-4" type="submit">Buscar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-gray-800">
                        @if($busqueda)
                            Resultados <small class="text-muted">({{ $activos->total() }})</small>
                        @else
                            <i class="bi bi-list-ul me-2"></i>Activos Recientes
                        @endif
                    </h5>
                    
                    <form method="GET" action="{{ route('seguimiento.index') }}">
                        @if($busqueda) <input type="hidden" name="q" value="{{ $busqueda }}"> @endif
                        <div class="d-flex align-items-center">
                            <span class="small text-muted me-2">Mostrar:</span>
                            <select name="limit" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                                <option value="10" {{ $limit == 10 ? 'selected' : '' }}>10</option>
                                <option value="20" {{ $limit == 20 ? 'selected' : '' }}>20</option>
                                <option value="50" {{ $limit == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ $limit == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                    </form>
                </div>
                
                <div class="list-group list-group-flush">
                    @forelse($activos as $activo)
                        <a href="{{ route('seguimiento.show', $activo->id) }}" class="list-group-item list-group-item-action p-3 d-flex justify-content-between align-items-center hover-bg-light">
                             <div class="d-flex align-items-center">
                                <div class="avatar-circle bg-light text-primary me-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 45px; height: 45px;">
                                    <i class="bi {{ Str::contains(strtolower($activo->tipo->nombre ?? ''), 'laptop') ? 'bi-laptop' : (Str::contains(strtolower($activo->tipo->nombre ?? ''), 'impresora') ? 'bi-printer' : 'bi-box-seam') }}"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">{{ $activo->tipo->nombre ?? 'Activo' }} {{ $activo->marca->nombre ?? '' }}</h6>
                                    <small class="text-muted">Serie: <b>{{ $activo->numero_serie }}</b> • {{ $activo->modelo }}</small>
                                </div>
                            </div>
                            <div>
                                <span class="badge bg-{{ $activo->estado_id == 1 ? 'success' : ($activo->estado_id == 2 ? 'primary' : 'warning') }} rounded-pill">
                                    {{ $activo->estado->nombre ?? 'N/A' }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-5 text-muted">No se encontraron registros.</div>
                    @endforelse
                </div>

                <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
                    <small class="text-muted">
                        Mostrando {{ $activos->firstItem() ?? 0 }} a {{ $activos->lastItem() ?? 0 }} de {{ $activos->total() }}
                    </small>
                    {{ $activos->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection