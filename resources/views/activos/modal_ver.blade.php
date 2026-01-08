<div class="modal-header bg-light border-bottom-0">
    <h5 class="modal-title fw-bold text-gray-800">
        <i class="bi bi-qr-code-scan me-2 text-primary"></i>Ficha Técnica Completa
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-4">
    <div class="d-flex align-items-center mb-4 p-3 bg-soft-primary rounded-3 border border-primary-subtle shadow-sm">
        <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm me-3" style="width: 50px; height: 50px; font-size: 1.5rem;">
            @if(isset($activo->especificaciones['imei']))
                <i class="bi bi-phone"></i> @else
                <i class="bi bi-laptop"></i> @endif
        </div>
        <div>
            <h5 class="fw-bold mb-0 text-dark">{{ $activo->tipo->nombre ?? 'Equipo' }} {{ $activo->marca->nombre ?? '' }}</h5>
            <div class="d-flex flex-wrap gap-3 text-muted small mt-1">
                <span><i class="bi bi-upc-scan me-1"></i>Serie: <strong class="text-dark">{{ $activo->numero_serie }}</strong></span>
                <span class="border-start ps-3"><i class="bi bi-tag me-1"></i>Código: <strong class="text-dark">{{ $activo->codigo_interno ?? 'S/N' }}</strong></span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card h-100 border-0 bg-light">
                <div class="card-body">
                    <h6 class="text-uppercase text-primary small fw-bold mb-3 border-bottom pb-2">
                        <i class="bi bi-cpu me-1"></i>Especificaciones
                    </h6>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2 d-flex justify-content-between">
                            <span class="text-muted">Modelo:</span>
                            <span class="fw-medium text-dark">{{ $activo->modelo ?? 'No registrado' }}</span>
                        </li>

                        {{-- Lógica para Móviles (IMEI / Pantalla) --}}
                        @if(isset($activo->especificaciones['imei']))
                            <li class="mb-2 d-flex justify-content-between">
                                <span class="text-muted">IMEI:</span>
                                <span class="fw-bold text-dark font-monospace">{{ $activo->especificaciones['imei'] }}</span>
                            </li>
                            <li class="mb-2 d-flex justify-content-between">
                                <span class="text-muted">Pantalla:</span>
                                <span class="fw-medium text-dark">{{ $activo->especificaciones['pantalla'] ?? 'N/A' }}</span>
                            </li>
                        @endif

                        {{-- Lógica para Cómputo (CPU / RAM / Disco / SO) --}}
                        @if(isset($activo->especificaciones['procesador']) || isset($activo->especificaciones['ram']))
                            <li class="mb-2 d-flex justify-content-between">
                                <span class="text-muted">Procesador:</span>
                                <span class="fw-medium text-dark">{{ $activo->especificaciones['procesador'] ?? 'N/A' }}</span>
                            </li>
                            <li class="mb-2 d-flex justify-content-between">
                                <span class="text-muted">Memoria RAM:</span>
                                <span class="fw-medium text-dark">{{ $activo->especificaciones['ram'] ?? 'N/A' }}</span>
                            </li>
                            <li class="mb-2 d-flex justify-content-between">
                                <span class="text-muted">Almacenamiento:</span>
                                <span class="fw-medium text-dark">{{ $activo->especificaciones['almacenamiento'] ?? 'N/A' }}</span>
                            </li>
                            <li class="mb-2 d-flex justify-content-between">
                                <span class="text-muted">Sistema Operativo:</span>
                                <span class="fw-medium text-dark">{{ $activo->especificaciones['sistema_operativo'] ?? 'N/A' }}</span>
                            </li>
                        @endif

                        {{-- Otras especificaciones generales --}}
                        @if(isset($activo->especificaciones['otras']) && !empty($activo->especificaciones['otras']))
                            <li class="mt-3 pt-2 border-top">
                                <span class="text-muted d-block mb-1">Detalles extra:</span>
                                <span class="fst-italic text-dark">{{ $activo->especificaciones['otras'] }}</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 border-0 bg-light">
                <div class="card-body">
                    <h6 class="text-uppercase text-primary small fw-bold mb-3 border-bottom pb-2">
                        <i class="bi bi-geo-alt me-1"></i>Ubicación y Estado
                    </h6>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="small text-muted d-block">Estatus Actual</label>
                            @php
                                $badgeClass = match($activo->estado_id) {
                                    1 => 'bg-success-subtle text-success border-success', // Disponible
                                    2 => 'bg-primary-subtle text-primary border-primary', // En Uso
                                    3 => 'bg-warning-subtle text-warning border-warning', // Mantenimiento
                                    4 => 'bg-info-subtle text-info border-info',          // Diagnóstico
                                    6 => 'bg-danger-subtle text-danger border-danger',    // Baja
                                    default => 'bg-light text-muted border-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} border px-2 py-1 rounded-pill w-100">
                                {{ $activo->estado->nombre ?? 'Desconocido' }}
                            </span>
                        </div>
                        <div class="col-6">
                            <label class="small text-muted d-block">Condición Física</label>
                            <span class="badge bg-white text-dark border px-2 py-1 rounded-pill w-100">
                                {{ $activo->condicion->nombre ?? 'N/A' }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted d-block mb-1">Ubicación Física:</label>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-building text-danger me-2"></i>
                            <span class="fw-bold text-dark">{{ $activo->ubicacion->nombre ?? 'Sin Asignar' }}</span>
                        </div>
                    </div>

                    @if($activo->empleado)
                        <div class="p-2 bg-white rounded border mt-2 shadow-sm">
                            <label class="small text-muted d-block mb-1 text-uppercase fw-bold" style="font-size: 0.7rem;">Actualmente asignado a:</label>
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                    <span style="font-size: 0.8rem;">{{ substr($activo->empleado->nombre, 0, 1) }}</span>
                                </div>
                                <div>
                                    <span class="small fw-bold text-dark d-block">{{ $activo->empleado->nombre }} {{ $activo->empleado->apellido_paterno }}</span>
                                    <small class="text-muted" style="font-size: 0.75rem;">{{ $activo->empleado->puesto->nombre ?? 'Empleado' }}</small>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="p-2 bg-white rounded border mt-2 text-center text-muted small">
                            <i class="bi bi-inbox me-1"></i> No asignado a empleado
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-12">
            <div class="card border-0 border-top pt-3 rounded-0">
                <div class="row text-center divide-x">
                    <div class="col-md-4 border-end">
                        <label class="small text-muted text-uppercase fw-bold">Costo de Adquisición</label>
                        <div class="h5 text-success fw-bold mb-0">
                            ${{ number_format($activo->costo, 2) }}
                        </div>
                    </div>
                    <div class="col-md-4 border-end">
                        <label class="small text-muted text-uppercase fw-bold">Fecha de Compra</label>
                        <div class="h6 text-dark mb-0">
                            <i class="bi bi-calendar-check me-1"></i>
                            {{ $activo->fecha_adquisicion ? $activo->fecha_adquisicion->format('d/m/Y') : 'N/A' }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="small text-muted text-uppercase fw-bold">Garantía Vence</label>
                        <div class="h6 {{ ($activo->garantia_hasta && $activo->garantia_hasta->isPast()) ? 'text-danger' : 'text-primary' }} mb-0">
                            <i class="bi bi-shield-check me-1"></i>
                            {{ $activo->garantia_hasta ? $activo->garantia_hasta->format('d/m/Y') : 'N/A' }}
                            @if($activo->garantia_hasta && $activo->garantia_hasta->isPast())
                                <small class="d-block text-danger fw-bold" style="font-size: 0.7rem;">(EXPIRADA)</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @if($activo->observaciones)
    <div class="mt-4">
        <h6 class="text-uppercase text-muted small fw-bold mb-2">
            <i class="bi bi-journal-text me-1"></i>Historial y Observaciones
        </h6>
        <div class="bg-light p-3 rounded small text-muted border" style="max-height: 100px; overflow-y: auto;">
            {!! nl2br(e($activo->observaciones)) !!}
        </div>
    </div>
    @endif
</div>

<div class="modal-footer bg-light border-top-0 py-2">
    <button type="button" class="btn btn-sm btn-secondary px-4" data-bs-dismiss="modal">Cerrar</button>
</div>