<div class="modal fade" id="modalNuevoEmpleado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">Nuevo Empleado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formNuevoEmpleado" enctype="multipart/form-data">
                <div class="modal-body">
                    <h6 class="text-primary mb-3"><i class="fas fa-user-circle me-1"></i> Información Personal</h6>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-3 text-center">
                            <div class="border rounded p-2 bg-white">
                                <label class="form-label d-block fw-bold text-muted small mb-2">FOTOGRAFÍA</label>
                                <input type="file" class="form-control form-control-sm" name="foto" accept="image/*">
                                <div class="form-text small">Max: 2MB</div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">No. Sistema <i class="fas fa-lock text-muted small"></i></label>
                                    <input type="text" class="form-control bg-light" 
                                           name="numero_empleado" 
                                           placeholder="Automático (RMA-XXX)" 
                                           readonly 
                                           style="cursor: not-allowed;">
                                    <div class="form-text small text-muted">Se generará al guardar.</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Cód. Empresa <i class="fas fa-info-circle text-muted" title="Código interno usado por la empresa"></i></label>
                                    <input type="text" class="form-control" name="codigo_empresa" placeholder="Ej: REF-001">
                                </div>
                                <div class="col-md-4">
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
                            </div>
                        </div>
                    </div>

                    <h6 class="text-primary mb-3 border-top pt-3"><i class="fas fa-briefcase me-1"></i> Puesto y Ubicación</h6>
                    <div class="row g-3 mb-4">
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
                            <label class="form-label">Fecha de Ingreso</label>
                            <input type="date" class="form-control" name="fecha_ingreso">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Correo Principal (Sistema)</label>
                            <input type="email" class="form-control" name="correo" placeholder="usuario@empresa.com">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2 border-top pt-3">
                        <h6 class="text-primary mb-0"><i class="fas fa-address-book me-1"></i> Contactos Adicionales</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="agregarFilaContacto()">
                            <i class="fas fa-plus"></i> Agregar
                        </button>
                    </div>
                    <div class="alert alert-light border p-2 mb-2" style="font-size: 0.9em;">
                        <i class="fas fa-info-circle text-muted"></i> Seleccione el tipo de contacto para validar el formato (10 dígitos para teléfonos).
                    </div>
                    
                    <div id="contenedor-contactos">
                        </div>

                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary shadow-sm">Guardar Empleado</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function agregarFilaContacto() {
        const contenedor = document.getElementById('contenedor-contactos');
        const index = contenedor.children.length; 
        
        const html = `
            <div class="row g-2 mb-2 align-items-end fila-contacto">
                <div class="col-3">
                    <label class="form-label small text-muted mb-0">Tipo</label>
                    <select class="form-select form-select-sm" name="contactos[${index}][tipo]" required onchange="ajustarInputContacto(this)">
                        <option value="Telefono">Teléfono</option>
                        <option value="Celular">Celular</option>
                        <option value="Correo">Correo Alterno</option>
                        <option value="Contacto de Emergencia">Contacto de Emergencia</option>
                    </select>
                </div>
                <div class="col-4">
                    <label class="form-label small text-muted mb-0">Dato / Número</label>
                    <input type="text" class="form-control form-control-sm input-valor" 
                           name="contactos[${index}][valor]" required 
                           placeholder="10 dígitos"
                           maxlength="10" 
                           oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>
                <div class="col-4">
                    <label class="form-label small text-muted mb-0">Descripción</label>
                    <input type="text" class="form-control form-control-sm" name="contactos[${index}][descripcion]" placeholder="Ej: Personal, Mamá...">
                </div>
                <div class="col-1 text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger border-0" title="Eliminar este contacto" onclick="this.closest('.fila-contacto').remove()">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
        contenedor.insertAdjacentHTML('beforeend', html);
    }

    function ajustarInputContacto(select) {
        const row = select.closest('.fila-contacto');
        const input = row.querySelector('.input-valor');
        const tipo = select.value;

        // Resetear validaciones
        input.value = ''; 
        input.removeAttribute('maxlength');
        input.removeAttribute('oninput');
        input.type = 'text';

        if (tipo === 'Telefono' || tipo === 'Celular' || tipo === 'Contacto de Emergencia') {
            input.placeholder = '10 dígitos';
            input.setAttribute('maxlength', '10');
            // Regex para solo números
            input.setAttribute('oninput', "this.value = this.value.replace(/[^0-9]/g, '')");
        } 
        else if (tipo === 'Correo' || tipo === 'Correo Alterno') {
            input.placeholder = 'ejemplo@correo.com';
            input.type = 'email';
        } 
        else {
            input.placeholder = 'Dato...';
        }
    }
</script>