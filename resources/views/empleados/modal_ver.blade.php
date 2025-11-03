<!-- Modal Ver Empleado -->
<div class="modal fade" id="modalVerEmpleado" tabindex="-1" aria-labelledby="modalVerEmpleadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalVerEmpleadoLabel">Detalles del Empleado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-auto">
                        <span class="avatar avatar-xl rounded-circle" style="width: 80px; height: 80px; background-color: #e9ecef; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-person-fill" style="font-size: 2.5rem; color: #adb5bd;"></i>
                        </span>
                    </div>
                    <div class="col">
                        <h3 id="verNombreCompleto" class="mb-0"></h3>
                        <p id="verPuesto" class="text-muted mb-1"></p>
                        <span id="verEstatus" class="badge"></span>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <strong class="d-block text-muted small">Número de Empleado</strong>
                        <p id="verNumeroEmpleado" class="mb-0"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="d-block text-muted small">Departamento</strong>
                        <p id="verDepartamento" class="mb-0"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="d-block text-muted small">Planta / Ubicación</strong>
                        <p id="verUbicacion" class="mb-0"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="d-block text-muted small">Correo Electrónico</strong>
                        <p id="verCorreo" class="mb-0"></p>
                    </div>
                    <div class="col-md-6">
                        <strong class="d-block text-muted small">Fecha de Ingreso</strong>
                        <p id="verFechaIngreso" class="mb-0"></p>
                    </div>
                </div>

                <hr class="my-4">
                
                <h5>Activos Asignados (0)</h5>
                <p class="text-muted">Este empleado no tiene activos asignados actualmente.</p>
                <!-- Aquí irá la lista de activos -->

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
