<div class="modal fade" id="modalVerEmpleado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-0">
                <div class="row mb-4 align-items-center">
                    <div class="col-auto">
                        <img id="verFoto" src="" class="rounded-circle border shadow-sm object-fit-cover"
                            style="width: 100px; height: 100px; display: none;"
                            onerror="this.style.display='none'; document.getElementById('verIconoDefault').style.display='flex';">

                        <div id="verIconoDefault"
                            class="rounded-circle bg-light d-flex align-items-center justify-content-center border"
                            style="width: 100px; height: 100px;">
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
                    <div class="col-md-4">
                        <small class="text-muted d-block fw-bold text-uppercase">No. Empleado</small>
                        <span id="verNumeroEmpleado" class="fs-6"></span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block fw-bold text-uppercase">Departamento</small>
                        <span id="verDepartamento"></span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block fw-bold text-uppercase">Ubicación</small>
                        <span id="verUbicacion"></span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block fw-bold text-uppercase">Correo</small>
                        <span id="verCorreo"></span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block fw-bold text-uppercase">Fecha Ingreso</small>
                        <span id="verFechaIngreso"></span>
                    </div>
                </div>

                <h5 class="fw-bold border-bottom pb-2 mb-3">Activos Asignados Actualmente</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-borderless">
                        <thead class="text-muted small text-uppercase">
                            <tr>
                                <th>Serie</th>
                                <th>Equipo</th>
                                <th>Modelo</th>
                                <th>Fecha Asignación</th>
                            </tr>
                        </thead>
                        <tbody id="tablaActivosAsignados">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>