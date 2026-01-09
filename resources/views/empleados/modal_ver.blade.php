<div class="modal fade" id="modalVerEmpleado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <ul class="nav nav-tabs" id="empleadoTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold" id="perfil-tab" data-bs-toggle="tab" data-bs-target="#perfil" type="button" role="tab"><i class="bi bi-person-vcard me-2"></i>Perfil SIGMA</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="expediente-tab" data-bs-toggle="tab" data-bs-target="#expediente" type="button" role="tab"><i class="bi bi-folder2-open me-2"></i>Expediente Digital</button>
                    </li>
                </ul>
                <button type="button" class="btn-close mb-auto" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body bg-white">
                <div class="tab-content" id="empleadoTabsContent">
                    
                    <div class="tab-pane fade show active" id="perfil" role="tabpanel">
                        <div class="row mb-4 align-items-center mt-3">
                            <div class="col-auto">
                                <img id="verFoto" src="" class="rounded-circle border shadow-sm object-fit-cover d-none" style="width: 100px; height: 100px;">
                                <div id="verIconoDefault" class="rounded-circle bg-light d-flex align-items-center justify-content-center border" style="width: 100px; height: 100px;">
                                    <i class="bi bi-person-fill text-secondary fs-1"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h3 id="verNombreCompleto" class="mb-1 fw-bold text-dark"></h3>
                                <p id="verPuesto" class="text-muted mb-2 fs-5"></p>
                                <div id="verEstatus"></div>
                            </div>
                            <div class="col-auto">
                                <a id="btnHistorialPdf" href="#" target="_blank" class="btn btn-danger shadow-sm">
                                    <i class="bi bi-file-earmark-pdf me-2"></i>Descargar Historial
                                </a>
                            </div>
                        </div>

                        <div class="row g-3 bg-light p-3 rounded mb-4">
                            <div class="col-md-4"><small class="text-muted d-block fw-bold text-uppercase">No. Sistema</small><span id="verNumeroEmpleado"></span></div>
                            <div class="col-md-4"><small class="text-muted d-block fw-bold text-uppercase">Cód. Empresa</small><span id="verCodigoEmpresa" class="fw-bold text-dark"></span></div>
                            <div class="col-md-4"><small class="text-muted d-block fw-bold text-uppercase">Departamento</small><span id="verDepartamento"></span></div>
                            <div class="col-md-4"><small class="text-muted d-block fw-bold text-uppercase">Ubicación</small><span id="verUbicacion"></span></div>
                            <div class="col-md-4"><small class="text-muted d-block fw-bold text-uppercase">Fecha Ingreso</small><span id="verFechaIngreso"></span></div>
                            <div class="col-md-4"><small class="text-muted d-block fw-bold text-uppercase">Correo Sistema</small><span id="verCorreo"></span></div>
                        </div>
                        
                        <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary"><i class="bi bi-person-lines-fill me-2"></i>Información de Contacto</h6>
                        <div class="row mb-4" id="verListaContactos"></div>

                        <h5 class="fw-bold border-bottom pb-2 mb-3">Activos Asignados Actualmente</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless">
                                <thead class="text-muted small text-uppercase">
                                    <tr><th>Serie</th><th>Equipo</th><th>Modelo</th><th>Fecha Asignación</th></tr>
                                </thead>
                                <tbody id="tablaActivosAsignados"></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="expediente" role="tabpanel">
                        <div class="row mt-3">
                            <div class="col-md-4 border-end">
                                <h6 class="fw-bold text-primary mb-3">Subir Documento SIGMA</h6>
                                <form id="formSubirDocumento">
                                    <div class="mb-3">
                                        <label class="form-label small">Tipo de Documento</label>
                                        <select class="form-select form-select-sm" id="docTipo" required>
                                            <option value="">Seleccione...</option>
                                            <option value="Carta Responsiva">Carta Responsiva</option>
                                            <option value="Carta de Devolución">Carta de Devolución</option>
                                            <option value="Reporte de Incidencia">Reporte de Incidencia</option>
                                            <option value="Historial de Activos">Historial de Activos</option>
                                            <option value="Otro">Otro Documento SIGMA</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small">Archivo (PDF/IMG - Max 1MB)</label>
                                        <input type="file" class="form-control form-control-sm" id="docArchivo" accept=".pdf,.jpg,.jpeg,.png" required>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="bi bi-cloud-upload me-1"></i> Subir Archivo
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="col-md-8">
                                <h6 class="fw-bold text-dark mb-3">Documentos en Expediente</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Fecha Subida</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="listaDocumentos">
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>