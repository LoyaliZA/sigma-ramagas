@extends('layouts.app')

@section('title', 'Historial del Activo')

@section('content')
<div class="container-fluid p-4">
    <div class="mb-4">
        <a href="{{ route('seguimiento.index') }}" class="text-decoration-none text-muted small fw-bold">
            <i class="bi bi-arrow-left me-1"></i> VOLVER AL BUSCADOR
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-body text-center pt-5 pb-4">
                    <div class="avatar-lg bg-soft-primary text-primary mx-auto rounded-circle d-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 80px; height: 80px; font-size: 2rem;">
                        <i class="bi bi-qr-code"></i>
                    </div>
                    <h5 class="fw-bold text-gray-800 mb-1">{{ $activo->numero_serie }}</h5>
                    <p class="text-muted mb-3">{{ $activo->tipo->nombre }} {{ $activo->marca->nombre }}</p>
                    
                    <span class="badge bg-{{ $activo->estado_id == 1 ? 'success' : ($activo->estado_id == 2 ? 'primary' : 'warning') }} rounded-pill px-3 py-2 mb-4">
                        {{ $activo->estado->nombre ?? 'Desconocido' }}
                    </span>

                    <div class="text-start bg-light p-3 rounded border">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="small text-muted">Modelo:</span>
                            <span class="small fw-bold">{{ $activo->modelo }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="small text-muted">Código:</span>
                            <span class="small fw-bold">{{ $activo->codigo_interno ?? 'S/N' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="small text-muted">Ubicación:</span>
                            <span class="small fw-bold">{{ $activo->ubicacion->nombre ?? 'N/A' }}</span>
                        </div>
                    </div>
                    
                    @if($activo->empleado)
                    <div class="mt-3 text-start bg-soft-primary p-3 rounded border border-primary-subtle">
                        <label class="small text-primary fw-bold text-uppercase mb-2">Asignado Actualmente</label>
                        <div class="d-flex align-items-center">
                            <div class="avatar-xs bg-white text-primary rounded-circle me-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark small">{{ $activo->empleado->nombre }} {{ $activo->empleado->apellido_paterno }}</h6>
                                <small class="text-muted" style="font-size: 0.7rem;">
                                    {{ $activo->empleado->puesto->nombre ?? 'Empleado' }}
                                </small>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history me-2"></i>Línea de Tiempo de Asignaciones</h6>
                </div>
                <div class="card-body">
                    @if($historial->count() > 0)
                        <div class="timeline">
                            @foreach($historial as $index => $h)
                                <div class="timeline-item pb-4 ps-4 border-start {{ $loop->last ? 'border-transparent' : 'border-2' }} position-relative" style="border-color: #e9ecef;">
                                    
                                    <div class="position-absolute top-0 start-0 translate-middle rounded-circle bg-white border border-2 {{ $h->fecha_devolucion ? 'border-secondary' : 'border-success' }} d-flex align-items-center justify-content-center" style="width: 16px; height: 16px;"></div>

                                    <div class="card border bg-light shadow-sm ms-2">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="fw-bold text-primary mb-0">
                                                        {{ $h->empleado->nombre ?? 'Usuario' }} {{ $h->empleado->apellido_paterno ?? '' }}
                                                    </h6>
                                                    <small class="text-muted">{{ $h->empleado->departamento->nombre ?? 'General' }}</small>
                                                </div>
                                                
                                                <div class="text-end">
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Asignado</span>
                                                    <div class="small fw-bold mt-1 text-dark">
                                                        {{ $h->fecha_asignacion ? $h->fecha_asignacion->format('d M Y') : 'N/A' }}
                                                    </div>
                                                    <div class="small text-muted">
                                                        {{ $h->fecha_asignacion ? $h->fecha_asignacion->format('h:i A') : '--:--' }}
                                                    </div>
                                                </div>
                                            </div>

                                            @if($h->carta_responsiva_url)
                                                <div class="mb-2">
                                                    <a href="{{ Storage::url($h->carta_responsiva_url) }}" target="_blank" class="btn btn-xs btn-outline-primary py-0" style="font-size: 0.75rem;">
                                                        <i class="bi bi-file-earmark-pdf me-1"></i>Ver Responsiva
                                                    </a>
                                                </div>
                                            @endif

                                            @if($h->fecha_devolucion)
                                                <div class="mt-3 pt-3 border-top">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">Devuelto el:</small>
                                                            <div class="fw-medium text-dark">
                                                                {{ $h->fecha_devolucion->format('d M Y, h:i A') }}
                                                            </div>
                                                            <div class="small text-secondary">
                                                                Condición: {{ $h->estadoDevolucion->nombre ?? 'N/A' }}
                                                            </div>
                                                        </div>
                                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Finalizado</span>
                                                    </div>
                                                    
                                                    @if($h->observaciones_devolucion)
                                                        <div class="mt-2 p-2 bg-white rounded border small text-muted fst-italic">
                                                            "{{ $h->observaciones_devolucion }}"
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="mt-2 text-success small fw-bold">
                                                    <i class="bi bi-circle-fill me-1" style="font-size: 6px; vertical-align: middle;"></i> Activo Actualmente
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x fs-1 opacity-50"></i>
                            <p class="mt-3">Este activo no tiene historial de asignaciones registrado.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection