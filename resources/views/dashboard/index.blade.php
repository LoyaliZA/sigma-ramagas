@extends('layouts.app')

@section('title', 'Panel de Control')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold">Panel de Control</h1>
            <p class="text-muted small mb-0">Sistema de Gestión de Activos Tecnológicos - Grupo Ramagas</p>
        </div>
        <div class="text-end text-muted small">
            <i class="bi bi-calendar-event me-1"></i> 
            <span id="reloj-vivo" class="text-capitalize fw-medium">
                {{ \Carbon\Carbon::now()->translatedFormat('l, d \d\e F \d\e Y | h:i:s A') }}
            </span>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card card-dashboard h-100">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <div class="kpi-label">Total de Activos</div>
                        <div class="kpi-value">{{ $totalActivos }}</div>
                        <div class="kpi-subtext mt-2">
                            <i class="bi bi-arrow-up-short"></i> {{ $activosDisponibles }} disponibles
                        </div>
                    </div>
                    <div class="icon-box bg-soft-primary">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-dashboard h-100">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <div class="kpi-label">Activos en Uso</div>
                        <div class="kpi-value">{{ $activosAsignados }}</div>
                        <div class="kpi-subtext mt-2">
                            <i class="bi bi-activity"></i> {{ $porcentajeUso ?? '0' }}% utilización
                        </div>
                    </div>
                    <div class="icon-box bg-soft-success">
                        <i class="bi bi-pc-display"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-dashboard h-100">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <div class="kpi-label">Empleados Activos</div>
                        <div class="kpi-value">{{ $empleadosActivos }}</div>
                        <div class="kpi-subtext mt-2">
                            <i class="bi bi-check-circle"></i> Personal registrado
                        </div>
                    </div>
                    <div class="icon-box bg-soft-info"> <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-dashboard h-100">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <div class="kpi-label">En Mantenimiento</div>
                        <div class="kpi-value">{{ $activosMantenimiento ?? 0 }}</div>
                        <div class="kpi-subtext mt-2 text-muted">
                            <i class="bi bi-dash"></i> Normal
                        </div>
                    </div>
                    <div class="icon-box bg-soft-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        
        <div class="col-lg-7">
            <div class="card card-dashboard h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h6 class="m-0 fw-bold text-primary"><i class="bi bi-pie-chart me-2"></i>Distribución por Tipo</h6>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center pb-4">
                    <div style="width: 80%; max-height: 350px;">
                        <canvas id="chartTipos"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card card-dashboard h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h6 class="m-0 fw-bold text-dark"><i class="bi bi-clock-history me-2"></i>Actividad Reciente</h6>
                </div>
                <div class="card-body px-4">
                    <div class="activity-feed">
                        @forelse($actividadesRecientes as $actividad)
                        <div class="activity-item d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="activity-icon {{ $actividad->fecha_devolucion ? 'bg-soft-warning text-warning' : 'bg-soft-success text-success' }}">
                                    <i class="bi {{ $actividad->fecha_devolucion ? 'bi-arrow-left-right' : 'bi-person-check' }}"></i>
                                </div>
                                
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.95rem;">
                                        {{ $actividad->fecha_devolucion ? 'Devolución registrada' : 'Activo asignado' }}
                                    </h6>
                                    <small class="text-muted">
                                        {{ $actividad->fecha_asignacion->translatedFormat('d M Y, h:i A') }}
                                    </small>
                                </div>
                            </div>
                            
                            <div>
                                <span class="badge-soft {{ $actividad->fecha_devolucion ? 'badge-soft-warning' : 'badge-soft-success' }}">
                                    {{ $actividad->fecha_devolucion ? 'Devuelto' : 'Asignado' }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5">
                            <div class="mb-3 text-muted"><i class="bi bi-inbox fs-1"></i></div>
                            <p class="text-muted small">No hay movimientos recientes.</p>
                        </div>
                        @endforelse
                    </div>
                    
                    @if(count($actividadesRecientes) > 0)
                    <div class="text-center mt-4">
                        <a href="{{ route('asignaciones.index') }}" class="btn btn-sm btn-link text-decoration-none">Ver todas las asignaciones <i class="bi bi-arrow-right"></i></a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const datosTipos = @json($distribucionTipos);
        const labels = datosTipos.map(item => item.nombre);
        const counts = datosTipos.map(item => item.total);
        // Colores pastel para el gráfico
        const backgroundColors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'];

        const ctx = document.getElementById('chartTipos').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: counts,
                    backgroundColor: backgroundColors,
                    borderWidth: 0, // Quitamos bordes para que se vea más limpio
                    hoverOffset: 4
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
                },
                cutout: '75%', 
            },
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        function actualizarReloj() {
            const ahora = new Date();
            const opcionesFecha = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const opcionesHora = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
            let fecha = ahora.toLocaleDateString('es-MX', opcionesFecha);
            const hora = ahora.toLocaleTimeString('es-MX', opcionesHora);
            fecha = fecha.charAt(0).toUpperCase() + fecha.slice(1);
            const el = document.getElementById('reloj-vivo');
            if(el) el.textContent = `${fecha} | ${hora}`;
        }
        setInterval(actualizarReloj, 1000);
    });
</script>
@endsection