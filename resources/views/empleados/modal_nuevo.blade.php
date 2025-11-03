<!-- Modal Nuevo Empleado -->
<div class="modal fade" id="modalNuevoEmpleado" tabindex="-1" aria-labelledby="modalNuevoEmpleadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNuevoEmpleadoLabel">Nuevo Empleado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formNuevoEmpleado">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="numero_empleado" class="form-label">Número de Empleado <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="numero_empleado" name="numero_empleado" required>
                        </div>
                        <div class="col-md-6">
                            <label for="nombre" class="form-label">Nombre(s) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="col-md-6">
                            <label for="apellido_paterno" class="form-label">Apellido Paterno <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" required>
                        </div>
                        <div class="col-md-6">
                            <label for="apellido_materno" class="form-label">Apellido Materno</label>
                            <input type="text" class="form-control" id="apellido_materno" name="apellido_materno">
                        </div>
                        <div class="col-md-6">
                            <label for="puesto_id" class="form-label">Puesto <span class="text-danger">*</span></label>
                            <select class="form-select" id="puesto_id" name="puesto_id" required>
                                <option value="" disabled selected>Seleccionar puesto</option>
                                @foreach($puestos as $puesto)
                                    <option value="{{ $puesto->id }}">{{ $puesto->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="departamento_id" class="form-label">Departamento <span class="text-danger">*</span></label>
                            <select class="form-select" id="departamento_id" name="departamento_id" required>
                                <option value="" disabled selected>Seleccionar departamento</option>
                                @foreach($departamentos as $depto)
                                    <option value="{{ $depto->id }}">{{ $depto->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="planta_id" class="form-label">Planta / Ubicación <span class="text-danger">*</span></label>
                            <select class="form-select" id="planta_id" name="planta_id" required>
                                <option value="" disabled selected>Seleccionar ubicación</option>
                                @foreach($ubicaciones as $ubicacion)
                                    <option value="{{ $ubicacion->id }}">{{ $ubicacion->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="correo" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo">
                        </div>
                        <div class="col-md-6">
                            <label for="estatus" class="form-label">Estatus <span class="text-danger">*</span></label>
                            <select class="form-select" id="estatus" name="estatus" required>
                                <option value="Activo" selected>Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="fecha_ingreso" class="form-label">Fecha de Ingreso</label>
                            <input type="date" class="form-control" id="fecha_ingreso" name="fecha_ingreso">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-person-fill-add me-2"></i>
                        Crear Empleado
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
