<div class="modal fade" id="modalNuevoActivo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Registrar Nuevo Activo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formNuevoActivo">
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-12"><h6 class="text-muted text-uppercase small border-bottom pb-1">Datos Generales</h6></div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Número de Serie <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="numero_serie" required placeholder="S/N del fabricante">
                        </div>

                        </div>

                    <div class="row g-3">
                        <div class="col-12"><h6 class="text-muted text-uppercase small border-bottom pb-1 mt-2">Especificaciones Técnicas</h6></div>

                        <div class="col-md-6">
                            <label class="form-label">IMEI (Móviles)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                                <input type="text" class="form-control" name="imei" 
                                    maxlength="15" 
                                    pattern="\d{15}" 
                                    title="El IMEI debe tener exactamente 15 dígitos numéricos"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 15);"
                                    placeholder="15 dígitos numéricos">
                            </div>
                            <div class="form-text small">Opcional. Máximo 15 dígitos.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tamaño de Pantalla</label>
                            <select class="form-select" name="pantalla_tamano">
                                <option value="">Seleccione...</option>
                                <optgroup label="Celulares / Smartphones">
                                    <option value='4.7"'>4.7" (Compacto)</option>
                                    <option value='5.5"'>5.5" (Estándar)</option>
                                    <option value='6.1"'>6.1" (Iphone/Android)</option>
                                    <option value='6.5"'>6.5" (Grande)</option>
                                    <option value='6.7"'>6.7" (Max/Plus)</option>
                                </optgroup>
                                <optgroup label="Tablets / Laptops">
                                    <option value='10.2"'>10.2" (Tablet)</option>
                                    <option value='11"'>11" (Tablet Pro)</option>
                                    <option value='13"'>13" (Laptop Pequeña)</option>
                                    <option value='14"'>14" (Laptop Estándar)</option>
                                    <option value='15.6"'>15.6" (Laptop Grande)</option>
                                </optgroup>
                                <option value="Otro">Otro Tamaño</option>
                            </select>
                        </div>

                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Activo</button>
                </div>
            </form>
        </div>
    </div>
</div>