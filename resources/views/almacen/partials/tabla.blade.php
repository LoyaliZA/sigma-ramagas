<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Serie</th>
                <th>Equipo</th>
                <th>Estado Actual</th>
                <th>Ubicación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($activos as $activo)
            <tr>
                <td class="fw-bold">{{ $activo->numero_serie }}</td>
                <td>
                    {{ $activo->tipo->nombre ?? '' }} - {{ $activo->marca->nombre ?? '' }}<br>
                    <small class="text-muted">{{ $activo->modelo }}</small>
                </td>
                <td>
                    <span class="badge bg-secondary">{{ $activo->estado->nombre ?? 'N/A' }}</span>
                </td>
                <td>{{ $activo->ubicacion->nombre ?? '' }}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="abrirModalEstado('{{ $activo->id }}', '{{ $activo->estado_id }}')">
                        <i class="bi bi-arrow-left-right"></i> Mover
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-4 text-muted">No hay activos en esta sección.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>