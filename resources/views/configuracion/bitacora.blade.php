@extends('layouts.app')

@section('title', 'Bitácora de Cambios')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold">Bitácora</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('configuracion.index') }}">Configuración</a></li>
                    <li class="breadcrumb-item active">Bitácora</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('configuracion.bitacora') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">FECHA INICIO</label>
                    <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" class="form-control bg-light border-0">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">FECHA FIN</label>
                    <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}" class="form-control bg-light border-0">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">USUARIO</label>
                    <select name="usuario_id" class="form-select bg-light border-0">
                        <option value="">-- Todos --</option>
                        @foreach($usuarios as $user)
                            <option value="{{ $user->id }}" {{ request('usuario_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-filter me-2"></i>Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-secondary small text-uppercase">Fecha/Hora</th>
                            <th class="px-4 py-3 text-secondary small text-uppercase">Usuario</th>
                            <th class="px-4 py-3 text-secondary small text-uppercase">Acción</th>
                            <th class="px-4 py-3 text-secondary small text-uppercase">Módulo</th>
                            <th class="px-4 py-3 text-secondary small text-uppercase">Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="px-4 text-nowrap text-muted small">
                                {{ $log->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 fw-bold text-dark">
                                {{ $log->usuario->name ?? 'Sistema' }}
                            </td>
                            <td class="px-4">
                                @php
                                    $badgeClass = match($log->accion) {
                                        'Creación' => 'bg-soft-success text-success',
                                        'Edición' => 'bg-soft-primary text-primary',
                                        'Eliminación' => 'bg-soft-danger text-danger',
                                        default => 'bg-light text-muted'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $log->accion }}</span>
                            </td>
                            <td class="px-4 text-muted small">
                                {{ ucfirst($log->tabla) }} #{{ $log->registro_id }}
                            </td>
                            <td class="px-4">
                                <button class="btn btn-sm btn-link text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#logDetails{{ $log->id }}">
                                    Ver Data <i class="bi bi-chevron-down"></i>
                                </button>
                            </td>
                        </tr>
                        <tr class="collapse bg-light" id="logDetails{{ $log->id }}">
                            <td colspan="5" class="px-4 py-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-danger small fw-bold">ANTERIOR</h6>
                                        <pre class="small bg-white p-2 border rounded text-muted">{{ $log->valores_anteriores ? json_encode($log->valores_anteriores, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '---' }}</pre>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-success small fw-bold">NUEVO</h6>
                                        <pre class="small bg-white p-2 border rounded text-muted">{{ $log->valores_nuevos ? json_encode($log->valores_nuevos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '---' }}</pre>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No hay registros en la bitácora.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-top-0 py-3">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection