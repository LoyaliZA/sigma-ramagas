<div class="modal fade" id="modalEditarEmpleado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Empleado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarEmpleado" enctype="multipart/form-data">
                <input type="hidden" id="editId" name="id">
                
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Número de Empleado</label>
                            <input type="text" class="form-control bg-light" id="editNumeroEmpleado" name="numero_empleado" readonly title="No se puede modificar">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Estatus <span class="text-danger">*</span></label>
                            <select class="form-select" id="editEstatus" name="estatus" required onchange="toggleBajaFields()">
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                                <option value="Baja">Baja Definitiva</option>
                            </select>
                        </div>

                        <div class="col-12 row g-3 bg-soft-danger p-3 rounded mx-0 mb-2" id="bajaFields" style="display:none;">
                            <div class="col-12 text-danger fw-bold small">DATOS DE BAJA</div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha de Baja</label>
                                <input type="date" class="form-control" id="editFechaBaja" name="fecha_baja">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Motivo de Baja</label>
                                <select class="form-select" id="editMotivoBaja" name="motivo_baja">
                                    <option value="">Seleccione...</option>
                                    <option value="Renuncia Voluntaria">Renuncia Voluntaria</option>
                                    <option value="Rescisión de Contrato">Rescisión de Contrato</option>
                                    <option value="Fin de Contrato">Fin de Contrato</option>
                                    <option value="Jubilación">Jubilación</option>
                                    <option value="Defunción">Defunción</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Actualizar Fotografía</label>
                            <input type="file" class="form-control" name="foto" accept="image/*">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="editNombre" name="nombre" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Apellido Paterno</label>
                            <input type="text" class="form-control" id="editApellidoPaterno" name="apellido_paterno" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Apellido Materno</label>
                            <input type="text" class="form-control" id="editApellidoMaterno" name="apellido_materno">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Puesto</label>
                            <select class="form-select" id="editPuestoId" name="puesto_id" required>
                                @foreach($puestos as $puesto)
                                    <option value="{{ $puesto->id }}">{{ $puesto->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Departamento</label>
                            <select class="form-select" id="editDepartamentoId" name="departamento_id" required>
                                @foreach($departamentos as $depto)
                                    <option value="{{ $depto->id }}">{{ $depto->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Planta</label>
                            <select class="form-select" id="editPlantaId" name="planta_id" required>
                                @foreach($ubicaciones as $ubicacion)
                                    <option value="{{ $ubicacion->id }}">{{ $ubicacion->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Correo</label>
                            <input type="email" class="form-control" id="editCorreo" name="correo">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha Ingreso</label>
                            <input type="date" class="form-control" id="editFechaIngreso" name="fecha_ingreso">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>