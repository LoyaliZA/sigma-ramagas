<div class="modal fade" id="modalNuevoEmpleado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Empleado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formNuevoEmpleado" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-4 text-center">
                        <label class="form-label d-block fw-bold text-muted small">FOTOGRAFÍA DE PERFIL</label>
                        <input type="file" class="form-control w-75 mx-auto" name="foto" accept="image/*">
                        <div class="form-text">Opcional. Formatos: JPG, PNG.</div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Número de Empleado <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="numero_empleado" required placeholder="Ej: 12345">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estatus <span class="text-danger">*</span></label>
                            <select class="form-select" name="estatus" required>
                                <option value="Activo" selected>Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nombre(s) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombre" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Apellido Paterno <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="apellido_paterno" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Apellido Materno</label>
                            <input type="text" class="form-control" name="apellido_materno">
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Puesto <span class="text-danger">*</span></label>
                            <select class="form-select" name="puesto_id" required>
                                <option value="">Seleccionar...</option>
                                @foreach($puestos as $puesto)
                                    <option value="{{ $puesto->id }}">{{ $puesto->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Departamento <span class="text-danger">*</span></label>
                            <select class="form-select" name="departamento_id" required>
                                <option value="">Seleccionar...</option>
                                @foreach($departamentos as $depto)
                                    <option value="{{ $depto->id }}">{{ $depto->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Planta / Ubicación <span class="text-danger">*</span></label>
                            <select class="form-select" name="planta_id" required>
                                <option value="">Seleccionar...</option>
                                @foreach($ubicaciones as $ubicacion)
                                    <option value="{{ $ubicacion->id }}">{{ $ubicacion->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" name="correo">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha de Ingreso</label>
                            <input type="date" class="form-control" name="fecha_ingreso">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Empleado</button>
                </div>
            </form>
        </div>
    </div>
</div>