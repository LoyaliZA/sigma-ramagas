<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SIGMA - Ramagas')</title>

    <link rel="icon" href="{{ asset('img/ramagas_mini.ico') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- 1. CONFIGURACIÓN DE PERMISOS PARA JS --}}
    <script>
        window.SigmaConfig = {
            // CORRECCIÓN CRÍTICA AQUÍ:
            // Usamos tu relación 'roles' definida en User.php.
            // Obtenemos el primer rol asociado y sacamos su 'nombre'.
            userRole: "{{ optional(auth()->user()->roles->first())->nombre ?? 'Guest' }}",
            
            // Diccionario de URLs
            urls: {
                dashboard: "{{ route('dashboard') }}",
                activos: "{{ route('activos.index') }}",
                empleados: "{{ route('empleados.index') }}",
                asignaciones: "{{ route('asignaciones.index') }}",
                almacen: "{{ route('almacen.index') }}",
                seguimiento: "{{ route('seguimiento.index') }}",
                reportes: "{{ route('reportes.index') }}",
                configuracion: "{{ route('configuracion.index') }}"
            }
        };
    </script>
</head>

<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <i class="bi bi-file-earmark-text-fill"></i>
            </div>
            <div class="app-title">
                <strong>SIGMA</strong>
                <span>Grupo Ramagas</span>
            </div>
        </div>

        <nav class="nav-menu flex-grow-1">
            <span class="nav-label">Sistema de Gestión</span>
            <ul class="nav flex-column">
                
                {{-- APLICAMOS EL FILTRO onclick="navegarSeguro..." A TODO --}}

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('dashboard*') ? 'active' : '' }}"
                        href="#" onclick="navegarSeguro(event, 'dashboard')">
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('activos*') ? 'active' : '' }}" 
                       href="#" onclick="navegarSeguro(event, 'activos')">
                        <i class="bi bi-box-seam-fill"></i>
                        <span>Activos</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('empleados*') ? 'active' : '' }}"
                        href="#" onclick="navegarSeguro(event, 'empleados')">
                        <i class="bi bi-people-fill"></i>
                        <span>Empleados</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('asignaciones*') ? 'active' : '' }}"
                        href="#" onclick="navegarSeguro(event, 'asignaciones')">
                        <i class="bi bi-display-fill"></i>
                        <span>Asignaciones</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('almacen*') ? 'active' : '' }}"
                        href="#" onclick="navegarSeguro(event, 'almacen')">
                        <i class="bi bi-building-fill"></i>
                        <span>Almacén</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('seguimiento*') ? 'active' : '' }}"
                       href="#" onclick="navegarSeguro(event, 'seguimiento')">
                        <i class="bi bi-clock-history"></i>
                        <span>Seguimiento</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('reportes*') ? 'active' : '' }}"
                        href="#" onclick="navegarSeguro(event, 'reportes')">
                        <i class="bi bi-file-earmark-bar-graph-fill"></i>
                        <span>Reportes</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('configuracion*') ? 'active' : '' }}"
                        href="#" onclick="navegarSeguro(event, 'configuracion')">
                        <i class="bi bi-gear-fill me-2"></i>
                        Configuración
                    </a>
                </li>
            </ul>
        </nav>

        <div class="mt-auto pt-3 border-top">
            <div class="d-flex align-items-center mb-2 px-2 text-muted small">
                <i class="bi bi-person-circle me-2"></i>
                <span class="text-truncate">{{ Auth::user()->name ?? 'Usuario' }}</span>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="btn btn-sm btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </div>

    <main class="main-content">
        @if(isset($slot))
        <div class="container-fluid">
            {{ $slot }}
        </div>
        @else
        @yield('content')
        @endif
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        const metaCsrf = document.querySelector('meta[name="csrf-token"]');
        if (metaCsrf) {
            window.csrfToken = metaCsrf.getAttribute('content');
        }

        @if(session('error_permisos'))
            Swal.fire({
                icon: 'warning',
                title: 'Acceso Restringido',
                text: '{{ session('error_permisos') }}',
                confirmButtonColor: '#f39c12',
                confirmButtonText: 'Entendido'
            });
        @endif

        // 3. MATRIZ DE ACCESO
        function navegarSeguro(event, modulo) {
            event.preventDefault(); 
            
            const role = window.SigmaConfig.userRole; 
            
            // IMPORTANTE: Asegúrate de que estos nombres coinciden EXACTAMENTE
            // con lo que tienes en tu base de datos (columna 'nombre' en tabla 'roles')
            const permisos = {
                'Super Admin': ['all'], 
                'Admin': ['dashboard', 'activos', 'empleados', 'asignaciones', 'almacen', 'seguimiento', 'reportes'],
                'Empleado': ['dashboard', 'empleados']
            };

            const accesoPermitido = 
                (permisos[role] && permisos[role].includes('all')) || 
                (permisos[role] && permisos[role].includes(modulo));

            if (accesoPermitido) {
                window.location.href = window.SigmaConfig.urls[modulo];
            } else {
                let mensaje = 'No tienes permisos para acceder a esta sección.';
                
                if(role === 'Empleado') {
                    mensaje = 'Tu perfil de Empleado solo tiene acceso al Directorio y Dashboard.';
                } else if (role === 'Guest') {
                    mensaje = 'No se ha detectado un rol asignado a tu usuario. Contacta a soporte.';
                }
                
                Swal.fire({
                    icon: 'warning',
                    title: 'Acceso Restringido',
                    text: mensaje,
                    footer: '<small class="text-muted">Permisos Insuficientes</small>',
                    confirmButtonColor: '#f39c12',
                    confirmButtonText: 'Entendido'
                });
            }
        }
    </script>
    @stack('scripts')
</body>
</html>