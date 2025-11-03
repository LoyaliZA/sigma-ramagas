<!-- Modal Editar Empleado -->
<div class="modal fade" id="modalEditarEmpleado" tabindex="-1" aria-labelledby="modalEditarEmpleadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarEmpleadoLabel">Editar Empleado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarEmpleado">
                @method('PUT')
                <input type="hidden" id="editId" name="id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="editNumeroEmpleado" class="form-label">Número de Empleado <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editNumeroEmpleado" name="numero_empleado" required>
                        </div>
                        <div class="col-md-6">
                            <label for="editNombre" class="form-label">Nombre(s) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editNombre" name="nombre" required>
                        </div>
                        <div class="col-md-6">
                            <label for="editApellidoPaterno" class="form-label">Apellido Paterno <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editApellidoPaterno" name="apellido_paterno" required>
                        </div>
                        <div class="col-md-6">
                            <label for="editApellidoMaterno" class="form-label">Apellido Materno</label>
                            <input type="text" class="form-control" id="editApellidoMaterno" name="apellido_materno">
                        </div>
                         <div class="col-md-6">
                            <label for="editPuestoId" class="form-label">Puesto <span class="text-danger">*</span></label>
                            <select class="form-select" id="editPuestoId" name="puesto_id" required>
                                <option value="" disabled>Seleccionar puesto</option>
                                @foreach($puestos as $puesto)
                                    <option value="{{ $puesto->id }}">{{ $puesto->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="editDepartamentoId" class="form-label">Departamento <span class="text-danger">*</span></label>
                            <select class="form-select" id="editDepartamentoId" name="departamento_id" required>
                                <option value="" disabled>Seleccionar departamento</option>
                                @foreach($departamentos as $depto)
                                    <option value="{{ $depto->id }}">{{ $depto->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="editPlantaId" class="form-label">Planta / Ubicación <span class="text-danger">*</span></label>
                            <select class="form-select" id="editPlantaId" name="planta_id" required>
                                <option value="" disabled>Seleccionar ubicación</option>
                                @foreach($ubicaciones as $ubicacion)
                                    <option value="{{ $ubicacion->id }}">{{ $ubicacion->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="editCorreo" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="editCorreo" name="correo">
                        </div>
                        <div class="col-md-6">
                            <label for="editEstatus" class="form-label">Estatus <span class="text-danger">*</span></label>
                            <select class="form-select" id="editEstatus" name="estatus" required>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="editFechaIngreso" class="form-label">Fecha de Ingreso</label>
                            <input type="date" class="form-control" id="editFechaIngreso" name="fecha_ingreso">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save-fill me-2"></i>
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
