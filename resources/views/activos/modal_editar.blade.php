<div class="modal fade" id="modalEditarActivo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Editar Activo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarActivo">
                @method('PUT')
                <input type="hidden" id="editId" name="id">
                
                <div class="modal-body">
                    <div class="row g-3">
                         <div class="col-md-6">
                            <label class="form-label">IMEI (Móviles)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                                <input type="text" class="form-control" id="editImei" name="imei" 
                                    maxlength="15" 
                                    pattern="\d{15}" 
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 15);">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tamaño de Pantalla</label>
                            <select class="form-select" id="editPantalla" name="pantalla_tamano">
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
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>