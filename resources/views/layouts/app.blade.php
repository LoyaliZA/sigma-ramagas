<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIGMA - Ramagas')</title>

    <link rel="icon" href="{{ asset('../../public/img/ramagas_mini.ico') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>

<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <i class="bi bi-file-earmark-text-fill"></i>
            </div>
            <div class="app-title">
                <strong>SysActivos</strong>
                <span>Grupo Ramagas</span>
            </div>
        </div>

        <nav class="nav-menu">
            <span class="nav-label">Sistema de Gestión</span>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('dashboard*') ? 'active' : '' }}" href="{{ url('/dashboard') }}">
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('activos*') ? 'active' : '' }}" href="{{ route('activos.index') }}">
                        <i class="bi bi-box-seam-fill"></i>
                        <span>Activos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('empleados*') ? 'active' : '' }}" href="{{ route('empleados.index') }}">
                        <i class="bi bi-people-fill"></i>
                        <span>Empleados</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('asignaciones*') ? 'active' : '' }}" href="{{ route('asignaciones.index') }}">
                        <i class="bi bi-display-fill"></i>
                        <span>Asignaciones</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('almacen*') ? 'active' : '' }}" href="{{ route('almacen.index') }}">
                        <i class="bi bi-building-fill"></i>
                        <span>Almacén</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('seguimiento*') ? 'active' : '' }}" href="{{ route('seguimiento.index') }}">
                        <i class="bi bi-clock-history"></i>
                        <span>Seguimiento</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('reportes*') ? 'active' : '' }}" href="{{ route('reportes.index') }}">
                        <i class="bi bi-file-earmark-bar-graph-fill"></i>
                        <span>Reportes</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <main class="main-content">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
    @stack('scripts')
</body>

</html>