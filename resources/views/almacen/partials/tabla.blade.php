<div class="table-responsive">
    <table class="table table-hover align-middle mb-0" style="border-collapse: collapse;">
        <thead class="bg-light">
            <tr>
                <th class="ps-4 py-3 text-uppercase text-muted small fw-bold border-0 rounded-start">Activo</th>
                <th class="py-3 text-uppercase text-muted small fw-bold border-0">Detalles</th>
                <th class="py-3 text-uppercase text-muted small fw-bold border-0 text-center">Estado</th>
                <th class="py-3 text-uppercase text-muted small fw-bold border-0">Ubicación</th>
                <th class="pe-4 py-3 text-uppercase text-muted small fw-bold border-0 rounded-end text-end">Acciones</th>
            </tr>
        </thead>
        <tbody class="bg-white">
            @forelse($activos as $activo)
            <tr style="border-bottom: 1px solid #f0f2f5;">
                
                <td class="ps-4 py-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm me-3 bg-light text-primary rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 35px; height: 35px;">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div>
                            <span class="d-block fw-bold text-dark" style="font-size: 0.95rem;">{{ $activo->numero_serie }}</span>
                            <small class="text-muted" style="font-size: 0.75rem;">{{ $activo->codigo_interno ?? 'S/N' }}</small>
                        </div>
                    </div>
                </td>
                
                <td class="py-3">
                    <span class="d-block fw-medium text-dark">{{ $activo->tipo->nombre ?? 'N/A' }}</span>
                    <small class="text-muted">{{ $activo->marca->nombre ?? '' }} <span class="mx-1">•</span> {{ Str::limit($activo->modelo, 20) }}</small>
                </td>

                <td class="text-center py-3">
                    @php
                        $badgeClass = match($activo->estado_id) {
                            1 => 'bg-emerald-100 text-emerald-700', // Disponible (Verde suave)
                            3 => 'bg-amber-100 text-amber-700',      // Mantenimiento (Ambar)
                            4 => 'bg-blue-100 text-blue-700',        // Diagnóstico (Azul)
                            5,6 => 'bg-red-100 text-red-700',        // Bajas (Rojo)
                            default => 'bg-gray-100 text-gray-700'
                        };
                        // Estilos en línea para simular clases Tailwind si no las tienes compiladas
                        $estiloBadge = match($activo->estado_id) {
                            1 => 'background-color: #d1fae5; color: #065f46;',
                            3 => 'background-color: #fef3c7; color: #92400e;',
                            4 => 'background-color: #dbeafe; color: #1e40af;',
                            5,6 => 'background-color: #fee2e2; color: #991b1b;',
                            default => 'background-color: #f3f4f6; color: #374151;'
                        };
                    @endphp
                    <span class="badge border-0 rounded-pill px-3 py-1 fw-medium" style="{{ $estiloBadge }}">
                        {{ $activo->estado->nombre ?? 'N/A' }}
                    </span>
                </td>

                <td class="py-3">
                    <div class="d-flex align-items-center text-muted">
                        <i class="bi bi-geo-alt me-2 text-secondary"></i>
                        <span class="small text-dark">{{ $activo->ubicacion->nombre ?? 'Sin ubicación' }}</span>
                    </div>
                </td>

                <td class="pe-4 py-3 text-end">
                    <div class="dropdown">
                         <button class="btn btn-sm btn-white border shadow-sm text-dark d-inline-flex align-items-center gap-2" 
                                 onclick="abrirModalEstado('{{ $activo->id }}', '{{ $activo->estado_id }}')"
                                 title="Cambiar Estado">
                            <i class="bi bi-arrow-left-right text-primary"></i>
                            <span class="d-none d-md-inline small fw-medium">Gestionar</span>
                        </button>
                        
                        <a href="{{ route('activos.show', $activo->id) }}" class="btn btn-sm btn-light text-muted ms-1" title="Ver Ficha Completa">
                            <i class="bi bi-eye"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-5">
                    <div class="d-flex flex-column align-items-center justify-content-center opacity-75">
                        <div class="bg-light rounded-circle p-3 mb-3">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                        </div>
                        <h6 class="text-muted fw-bold">Sin resultados</h6>
                        <p class="text-muted small mb-0">No hay activos en esta categoría.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<style>
    .btn-white {
        background-color: #fff;
        border-color: #e5e7eb;
        transition: all 0.2s;
    }
    .btn-white:hover {
        background-color: #f9fafb;
        border-color: #d1d5db;
        transform: translateY(-1px);
    }
    .avatar-sm {
        font-size: 1.1rem;
    }
</style>