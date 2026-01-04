@extends('layouts.app')

@section('title', 'Historial del Activo')

@section('content')
<div class="container-fluid p-4">
    <a href="{{ route('seguimiento.index') }}" class="btn btn-link text-decoration-none mb-3 ps-0 text-muted">
        <i class="bi bi-arrow-left"></i> Volver a buscar
    </a>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow border-0 sticky-top" style="top: 20px; z-index: 1;">
                <div class="card-body text-center pt-5 pb-4">
                    <div class="avatar-circle bg-light text-primary mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; border-radius: 50%; font-size: 32px;">
                        <i class="bi bi-laptop"></i>
                    </div>
                    <h4 class="fw-bold text-gray-800">{{ $activo->numero_serie }}</h4>
                    <p class="text-muted mb-2">{{ $activo->tipo?->nombre }} {{ $activo->marca?->nombre }}</p>
                    
                    <span class="badge bg-{{ $activo->estado_id == 1 ? 'success' : ($activo->estado_id == 2 ? 'primary' : 'secondary') }} px-3 py-2 rounded-pill">
                        {{ $activo->estado?->nombre ?? 'N/A' }}
                    </span>
                </div>
                <div class="card-footer bg-white border-top-0 p-4">
                    <h6 class="text-uppercase text-xs font-weight-bold text-muted mb-3">Detalles Técnicos</h6>
                    
                    <div class="d-flex justify-content-between mb-2 small">
                        <span>Modelo:</span>
                        <span class="fw-bold">{{ $activo->modelo }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span>Ubicación:</span>
                        <span class="fw-bold">{{ $activo->ubicacion?->nombre ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span>Condición:</span>
                        <span class="fw-bold">{{ $activo->condicion?->nombre ?? '-' }}</span>
                    </div>
                    
                    @if(is_array($activo->especificaciones) || is_object($activo->especificaciones))
                    <hr class="my-3">
                    <div class="small text-muted bg-light p-3 rounded">
                        @foreach($activo->especificaciones as $k => $v)
                            <div class="d-flex justify-content-between">
                                <span class="text-capitalize">{{ $k }}:</span>
                                <strong class="text-dark">{{ $v }}</strong>
                            </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="text-gray-800 mb-0">Línea de Tiempo</h4>
                <span class="badge bg-secondary">{{ $historial->count() }} eventos</span>
            </div>

            @if($historial->isEmpty())
                <div class="text-center py-5 text-muted bg-light rounded border border-dashed">
                    <i class="bi bi-clock-history fs-1 mb-3 d-block"></i>
                    Este activo es nuevo o nunca ha sido asignado.
                </div>
            @else
                <div class="timeline-container">
                    @foreach($historial as $h)
                    <div class="card mb-4 border-0 shadow-sm position-relative">
                        <div class="position-absolute start-0 top-0 bottom-0 rounded-start" 
                             style="width: 5px; background-color: {{ $h->fecha_devolucion ? '#6c757d' : '#4e73df' }};"></div>
                        
                        <div class="card-body ps-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="fw-bold text-primary mb-1">
                                        {{ $h->empleado?->nombre }} {{ $h->empleado?->apellido_paterno }}
                                    </h5>
                                    <p class="text-muted small mb-0">
                                        {{ $h->empleado?->puesto?->nombre ?? 'Puesto no def.' }} 
                                        <span class="mx-1">•</span> 
                                        {{ $h->empleado?->departamento?->nombre ?? '' }}
                                    </p>
                                </div>
                                <span class="badge {{ $h->fecha_devolucion ? 'bg-secondary' : 'bg-success' }}">
                                    {{ $h->fecha_devolucion ? 'Finalizado' : 'Vigente' }}
                                </span>
                            </div>

                            <hr class="my-3">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <div class="me-3 text-success fs-4"><i class="bi bi-box-arrow-right"></i></div>
                                        <div>
                                            <small class="text-muted d-block">Fecha de Asignación</small>
                                            <span class="fw-bold">{{ \Carbon\Carbon::parse($h->fecha_asignacion)->format('d/m/Y h:i A') }}</span>
                                            <div class="small text-success mt-1">
                                                Condición: {{ $h->estadoEntrega?->nombre ?? 'N/A' }}
                                            </div>
                                            @if($h->observaciones_entrega)
                                                <div class="small text-muted mt-1 fst-italic">"{{ $h->observaciones_entrega }}"</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 border-start">
                                    @if($h->fecha_devolucion)
                                        <div class="d-flex ps-3">
                                            <div class="me-3 text-secondary fs-4"><i class="bi bi-box-arrow-in-left"></i></div>
                                            <div>
                                                <small class="text-muted d-block">Fecha de Devolución</small>
                                                <span class="fw-bold">{{ \Carbon\Carbon::parse($h->fecha_devolucion)->format('d/m/Y h:i A') }}</span>
                                                <div class="small text-secondary mt-1">
                                                    Condición: {{ $h->estadoDevolucion?->nombre ?? 'N/A' }}
                                                </div>
                                                @if($h->observaciones_devolucion)
                                                    <div class="small text-muted mt-1 fst-italic">"{{ $h->observaciones_devolucion }}"</div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="d-flex ps-3 align-items-center h-100 text-success bg-light rounded ms-2">
                                            <i class="bi bi-check-circle-fill me-2"></i>
                                            <span class="fw-medium">Actualmente en uso</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection