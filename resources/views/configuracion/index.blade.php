@extends('layouts.app')

@section('title', 'Configuración del Sistema')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold">Configuración</h1>
            <p class="text-muted small mb-0">Administración general de usuarios y parámetros del sistema</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <a href="{{ route('configuracion.usuarios') }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm hover-scale">
                    <div class="card-body text-center p-5">
                        <div class="avatar-lg bg-soft-primary text-primary rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="bi bi-people-fill fs-1"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Usuarios y Permisos</h4>
                        <p class="text-muted small mt-2">Gestionar accesos, crear cuentas y asignar roles de administrador.</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('configuracion.catalogos') }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm hover-scale">
                    <div class="card-body text-center p-5">
                        <div class="avatar-lg bg-soft-success text-success rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="bi bi-tags-fill fs-1"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Catálogos</h4>
                        <p class="text-muted small mt-2">Administrar marcas, ubicaciones, puestos y listas desplegables.</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('configuracion.bitacora') }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm hover-scale">
                    <div class="card-body text-center p-5">
                        <div class="avatar-lg bg-soft-warning text-warning rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="bi bi-clock-history fs-1"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Bitácora de Cambios</h4>
                        <p class="text-muted small mt-2">Auditoría de seguridad y registro histórico de movimientos.</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<style>
    /* Efecto hover sutil para las tarjetas */
    .hover-scale {
        transition: transform 0.2s ease-in-out;
    }
    .hover-scale:hover {
        transform: translateY(-5px);
    }
</style>
@endsection