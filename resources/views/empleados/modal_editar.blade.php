<div class="modal fade" id="modalEditarEmpleado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">Editar Empleado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarEmpleado" enctype="multipart/form-data">
                @method('PUT') <input type="hidden" id="editId" name="id">
                
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">No. Sistema</label>
                            <input type="text" class="form-control bg-light" id="editNumeroEmpleado" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Cód. Empresa</label>
                            <input type="text" class="form-control" id="editCodigoEmpresa" name="codigo_empresa">
                        </div>
                        
                        <div class="col-md-4">
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
                            <label class="form-label">Fotografía (Dejar vacío para mantener actual)</label>
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
                            <label class="form-label">Correo Principal (Sistema)</label>
                            <input type="email" class="form-control" id="editCorreo" name="correo">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha Ingreso</label>
                            <input type="date" class="form-control" id="editFechaIngreso" name="fecha_ingreso">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2 border-top pt-3 mt-3">
                        <h6 class="text-primary mb-0"><i class="fas fa-address-book me-1"></i> Contactos Adicionales</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="agregarFilaContactoEdit()">
                            <i class="fas fa-plus"></i> Agregar
                        </button>
                    </div>
                    
                    <div id="contenedor-contactos-edit">
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

<script>
    function agregarFilaContactoEdit(datos = null) {
        const contenedor = document.getElementById('contenedor-contactos-edit');
        const index = contenedor.children.length; 
        
        const tipo = datos ? datos.tipo : 'Telefono';
        const valor = datos ? datos.valor : '';
        const descripcion = datos ? (datos.descripcion || '') : '';

        // Determinamos atributos iniciales según el tipo cargado
        let atributos = '';
        let placeholder = 'Dato...';
        
        // Logica de validacion inicial (si ya viene con datos)
        if (tipo === 'Telefono' || tipo === 'Celular' || tipo === 'Contacto de Emergencia') {
            atributos = `maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')"`;
            placeholder = '10 dígitos';
        } else if (tipo === 'Correo' || tipo === 'Correo Alterno') {
            atributos = `type="email"`;
            placeholder = 'ejemplo@correo.com';
        }

        const html = `
            <div class="row g-2 mb-2 align-items-end fila-contacto-edit">
                <div class="col-3">
                    <select class="form-select form-select-sm" name="contactos[${index}][tipo]" required onchange="ajustarInputContactoEdit(this)">
                        <option value="Telefono" ${tipo === 'Telefono' ? 'selected' : ''}>Teléfono</option>
                        <option value="Celular" ${tipo === 'Celular' ? 'selected' : ''}>Celular</option>
                        <option value="Correo" ${tipo === 'Correo' ? 'selected' : ''}>Correo Alt.</option>
                        <option value="Contacto de Emergencia" ${tipo === 'Contacto de Emergencia' ? 'selected' : ''}>Contacto de Emergencia</option>
                    </select>
                </div>
                <div class="col-4">
                    <input type="text" class="form-control form-control-sm input-valor-edit" 
                           name="contactos[${index}][valor]" 
                           value="${valor}" required 
                           placeholder="${placeholder}"
                           ${atributos}>
                </div>
                <div class="col-4">
                    <input type="text" class="form-control form-control-sm" name="contactos[${index}][descripcion]" 
                           value="${descripcion}" placeholder="Descripción">
                </div>
                <div class="col-1 text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger border-0" title="Eliminar este contacto" onclick="this.closest('.fila-contacto-edit').remove()">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
        contenedor.insertAdjacentHTML('beforeend', html);
    }

    // Función para ajustar input dinámicamente en el modal de Edición
    function ajustarInputContactoEdit(select) {
        const row = select.closest('.fila-contacto-edit');
        const input = row.querySelector('.input-valor-edit');
        const tipo = select.value;

        input.value = ''; 
        input.removeAttribute('maxlength');
        input.removeAttribute('oninput');
        input.type = 'text';

        if (tipo === 'Telefono' || tipo === 'Celular' || tipo === 'Contacto de Emergencia') {
            input.placeholder = '10 dígitos';
            input.setAttribute('maxlength', '10');
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