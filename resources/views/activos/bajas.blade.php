@extends('layouts.app')

@section('title', 'Historial de Bajas')

@section('content')
<div class="container-fluid p-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center text-danger mb-1">
                <i class="bi bi-trash3-fill me-2 fs-5"></i>
                <h2 class="h4 mb-0 fw-bold">Activos Dados de Baja</h2>
            </div>
            <p class="text-muted small mb-0 ms-1">Histórico de equipos retirados definitivamente del inventario.</p>
        </div>
        <div>
            <a href="{{ route('activos.index') }}" class="btn btn-outline-secondary px-4 rounded-pill shadow-sm me-2">
                <i class="bi bi-arrow-left me-2"></i>Volver al Inventario
            </a>
            <a href="{{ route('reportes.bajas') }}" class="btn btn-danger px-4 rounded-pill shadow-sm" target="_blank">
                <i class="bi bi-file-earmark-pdf me-2"></i>Reporte de Bajas
            </a>
        </div>
    </div>

    <div class="card card-dashboard mb-4 border-0 shadow-sm border-start border-danger border-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('activos.bajas') }}" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 ps-3"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control border-start-0 ps-0" 
                               placeholder="Buscar por código, serie o modelo..." value="{{ request('q') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-danger w-100">Buscar</button>
                </div>
                <div class="col-md-4 text-end">
                    <small class="text-muted fst-italic">Mostrando solo registros inactivos</small>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-dashboard border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 border-0 text-muted small text-uppercase fw-bold">Activo</th>
                            <th class="py-3 border-0 text-muted small text-uppercase fw-bold">Fecha de Baja</th>
                            <th class="py-3 border-0 text-muted small text-uppercase fw-bold">Motivo & Justificación</th>
                            <th class="py-3 border-0 text-muted small text-uppercase fw-bold">Ubicación Final</th>
                            <th class="pe-4 py-3 border-0 text-end text-muted small text-uppercase fw-bold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activos as $activo)
                        <tr class="border-bottom">
                            <td class="ps-4 py-3">
                                <div class="fw-bold text-danger text-decoration-line-through">{{ $activo->codigo_interno }}</div>
                                <div class="small text-muted">SN: {{ $activo->numero_serie }}</div>
                                <div class="badge bg-light text-secondary border fw-normal mt-1">{{ $activo->tipo->nombre }}</div>
                            </td>
                            
                            <td class="py-3">
                                <div class="fw-bold text-dark">
                                    {{ $activo->fecha_baja ? \Carbon\Carbon::parse($activo->fecha_baja)->translatedFormat('d M Y') : 'N/A' }}
                                </div>
                                <div class="small text-muted">
                                    {{ $activo->fecha_baja ? \Carbon\Carbon::parse($activo->fecha_baja)->format('h:i A') : '' }}
                                </div>
                            </td>

                            <td class="py-3">
                                <span class="badge bg-soft-danger text-danger mb-1">
                                    {{ $activo->motivoBaja->nombre ?? 'Sin clasificación' }}
                                </span>
                                <div class="text-muted small text-truncate" style="max-width: 250px;" title="{{ $activo->observaciones }}">
                                    {{ Str::limit($activo->observaciones, 50) }}
                                </div>
                            </td>

                            <td class="py-3 text-muted">
                                <i class="bi bi-geo-alt me-1"></i> {{ $activo->ubicacion->nombre ?? 'Desconocida' }}
                            </td>

                            <td class="pe-4 py-3 text-end">
                                <button class="btn btn-sm btn-outline-secondary rounded-circle" title="Ver Expediente" onclick="verBaja('{{ $activo->id }}')">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted mb-2"><i class="bi bi-emoji-smile fs-1"></i></div>
                                <p class="text-muted">No hay activos dados de baja.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="p-3 border-top">
                {{ $activos->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalVerBaja" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-soft-danger border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger">Expediente de Baja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="verContentBaja"></div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light btn-sm w-100" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    var modalVer = new bootstrap.Modal(document.getElementById('modalVerBaja'));

    function verBaja(id) {
        fetch(`/activos/${id}`).then(res => res.json()).then(data => {
            // Formateadores
            var fechaBaja = data.fecha_baja ? new Date(data.fecha_baja).toLocaleDateString('es-MX', {year: 'numeric', month: 'long', day: 'numeric'}) : 'No registrada';
            var fechaReg = data.created_date ? new Date(data.created_date).toLocaleDateString('es-MX', {year: 'numeric', month: 'short', day: 'numeric'}) : '-';
            
            var html = `
                <div class="container-fluid px-0 mt-3">
                    <div class="alert alert-danger border-0 d-flex align-items-center mb-4">
                        <i class="bi bi-x-circle-fill fs-4 me-3"></i>
                        <div>
                            <div class="fw-bold text-uppercase small">Activo Dado de Baja</div>
                            <div class="small">Este equipo ya no es operativo.</div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h6 class="text-secondary fw-bold mb-3 small text-uppercase">Datos del Equipo</h6>
                            <div class="row g-2">
                                <div class="col-6"><small class="text-muted d-block">Código</small><span class="fw-bold text-danger text-decoration-line-through">${data.codigo_interno}</span></div>
                                <div class="col-6"><small class="text-muted d-block">Serie</small><span class="fw-bold">${data.numero_serie}</span></div>
                                <div class="col-12"><hr class="my-1 opacity-25"></div>
                                <div class="col-6"><small class="text-muted d-block">Modelo</small><span>${data.modelo}</span></div>
                                <div class="col-6"><small class="text-muted d-block">Marca</small><span>${data.marca?.nombre}</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm border-start border-danger border-4">
                        <div class="card-body bg-soft-danger">
                            <h6 class="text-danger fw-bold mb-3 small text-uppercase"><i class="bi bi-file-earmark-x me-2"></i>Dictamen de Baja</h6>
                            
                            <div class="mb-3">
                                <small class="text-danger-emphasis d-block fw-bold">Fecha de Baja</small>
                                <span class="fs-5">${fechaBaja}</span>
                            </div>
                            
                            <div class="mb-3">
                                <small class="text-danger-emphasis d-block fw-bold">Motivo Principal</small>
                                <span class="badge bg-danger text-white">${data.motivo_baja?.nombre || 'No especificado'}</span>
                            </div>

                            <div class="mt-3 pt-3 border-top border-danger border-opacity-25">
                                <small class="text-danger-emphasis d-block fw-bold mb-1">Historial / Observaciones</small>
                                <p class="small text-muted bg-white p-2 rounded fst-italic mb-0">
                                    ${data.observaciones || 'Sin comentarios adicionales.'}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            document.getElementById('verContentBaja').innerHTML = html;
            modalVer.show();
        });
    }
</script>
@endpush