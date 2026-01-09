@extends('layouts.app')

@section('title', 'Historial de Bajas')

@section('content')
<div class="container-fluid p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center text-danger mb-1">
                <i class="bi bi-trash3-fill me-2 fs-4"></i>
                <h2 class="h4 mb-0 fw-bold">Activos Dados de Baja</h2>
            </div>
            <p class="text-muted small mb-0 ms-1">Cementerio de activos (Histórico de equipos retirados).</p>
        </div>
        <div>
            <a href="{{ route('activos.index') }}" class="btn btn-outline-secondary px-4 rounded-pill shadow-sm me-2">
                <i class="bi bi-arrow-left me-2"></i>Volver al Inventario
            </a>
            <a href="{{ route('reportes.bajas_pdf') }}" class="btn btn-danger px-4 rounded-pill shadow-sm"
                target="_blank">
                <i class="bi bi-file-earmark-pdf me-2"></i>Descargar Reporte
            </a>
        </div>
    </div>

    <div class="card card-dashboard mb-4 border-0 shadow-sm border-start border-danger border-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('activos.bajas') }}" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-danger"><i
                                class="bi bi-search"></i></span>
                        <input type="text" name="q" class="form-control border-start-0 ps-0"
                            placeholder="Buscar por serie, código o motivo..." value="{{ request('q') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100">Buscar</button>
                </div>
                @if(request('q'))
                <div class="col-md-2">
                    <a href="{{ route('activos.bajas') }}" class="btn btn-light w-100 text-muted">Limpiar</a>
                </div>
                @endif
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase small text-muted">
                        <tr>
                            <th scope="col" class="ps-4 py-3">Activo</th>
                            <th scope="col">Identificación</th>
                            <th scope="col">Motivo de Baja</th>
                            <th scope="col">Fecha Baja</th>
                            <th scope="col" class="text-end pe-4">Detalles</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activos as $baja)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-2 me-3 d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px;">
                                        <i class="bi bi-archive"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $baja->tipo->nombre ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $baja->marca->nombre ?? '' }}
                                            {{ $baja->modelo }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark font-monospace">{{ $baja->codigo_interno }}</span>
                                    <span class="small text-muted">SN: {{ $baja->numero_serie }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-danger bg-opacity-75 rounded-pill px-3">
                                    {{ $baja->motivoBaja->nombre ?? 'No especificado' }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-medium text-dark">
                                    {{ $baja->fecha_baja ? $baja->fecha_baja->format('d/m/Y') : 'N/A' }}
                                </div>
                                <small class="text-muted">
                                    {{ $baja->fecha_baja ? $baja->fecha_baja->diffForHumans() : '' }}
                                </small>
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-light border text-danger"
                                    onclick="verDetalleBaja('{{ $baja->id }}')" title="Ver Ficha">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <div class="mb-2"><i class="bi bi-clipboard-check fs-1 opacity-50"></i></div>
                                No hay activos dados de baja en el historial.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($activos->hasPages())
            <div class="p-3 border-top d-flex justify-content-center">
                {{ $activos->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="modalVerBaja" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white border-bottom-0">
                <h6 class="modal-title fw-bold"><i class="bi bi-archive me-2"></i>Detalle de Baja</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="verContentBaja">
                <div class="text-center py-4">
                    <div class="spinner-border text-danger" role="status"></div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function verDetalleBaja(id) {
    var modalVer = new bootstrap.Modal(document.getElementById('modalVerBaja'));
    var contentEl = document.getElementById('verContentBaja');

    // Reset spinner
    contentEl.innerHTML =
        '<div class="text-center py-4"><div class="spinner-border text-danger" role="status"></div></div>';
    modalVer.show();

    // Cargar datos via AJAX (aprovechando tu ruta show existente)
    // Nota: Como tu método show devuelve la vista parcial del activo vivo, 
    // aquí construimos el HTML manualmente para resaltar los datos de BAJA que no salen en la vista normal.

    axios.get('/activos/' + id)
        .then(function(response) {
            let data = response.data;
            // Verificar si es objeto JSON (response()->json) o string HTML
            // Tu controlador show devuelve view(...)->render() si es ajax. 
            // Pero esa vista es para activos vivos. Para bajas queremos ver el MOTIVO.
            // Así que mejor usaremos los datos JSON si es posible, o extraemos info.

            // TRUCO: Para evitar conflictos con tu método show actual, aquí inyectamos un HTML 
            // construido con los datos básicos que ya tenemos en la tabla si fallara el ajax complejo,
            // pero lo ideal es que el endpoint devuelva JSON.

            // Si tu controlador devuelve HTML, vamos a tener que adaptarnos.
            // Pero el método show actual devuelve la vista modal_ver.blade.php.
            // Esa vista NO muestra el motivo de baja explícitamente destacado.

            // Opción robusta: Solicitamos JSON explícitamente
            return axios.get('/activos/' + id, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
        })
        .then(function(response) {
            let data = response.data; // Aquí ya debería ser el objeto JSON del modelo

            // Formatear fecha
            let fechaBaja = data.fecha_baja ? new Date(data.fecha_baja).toLocaleDateString() : 'N/A';
            let motivo = data.motivo_baja ? data.motivo_baja.nombre : 'No especificado';

            let html = `
                <div class="text-center mb-4">
                    <div class="display-6 text-danger fw-bold mb-1">${data.codigo_interno || 'S/N'}</div>
                    <p class="text-muted text-uppercase fw-bold small">${data.modelo || 'Sin Modelo'}</p>
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">BAJA DEFINITIVA</span>
                </div>
                
                <div class="card border-0 bg-light rounded-3 mb-3">
                    <div class="card-body">
                        <div class="row g-3">
                             <div class="col-12">
                                <h6 class="text-danger fw-bold mb-3 small text-uppercase"><i class="bi bi-file-earmark-x me-2"></i>Dictamen</h6>
                                
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block fw-bold">Fecha de Baja</small>
                                        <span class="fs-6 text-dark">${fechaBaja}</span>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block fw-bold">Motivo</small>
                                        <span class="fw-bold text-danger">${motivo}</span>
                                    </div>
                                </div>

                                <div class="mt-3 pt-3 border-top border-danger border-opacity-25">
                                    <small class="text-muted d-block fw-bold mb-1">Historial / Observaciones</small>
                                    <div class="small text-secondary bg-white p-3 rounded border fst-italic mb-0" style="max-height: 150px; overflow-y: auto;">
                                        ${data.observaciones ? data.observaciones.replace(/\n/g, '<br>') : 'Sin comentarios adicionales.'}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <small class="text-muted">Serie: ${data.numero_serie}</small>
                </div>
            `;
            contentEl.innerHTML = html;
        })
        .catch(function(error) {
            console.error(error);
            // Si falla porque el controlador devuelve HTML en lugar de JSON, mostramos ese HTML
            // aunque no sea perfecto para bajas, es mejor que un error.
            if (error.response && error.response.data && typeof error.response.data === 'string') {
                contentEl.innerHTML = error.response.data;
            } else {
                contentEl.innerHTML =
                    '<p class="text-center text-danger">No se pudo cargar la información detallada.</p>';
            }
        });
}
</script>
@endpush