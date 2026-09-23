<div class="card p-3 mb-4">
    <h5 class="mb-3">Agregar Vehículos</h5>
    
    <div class="vehiculos-row" id="vehiculos-row-0">
        <!-- Primera fila: Categoría, Marca, Modelo -->
        <div class="row g-2 mb-2">
            <div class="col-md-2">
                <label class="form-label small">Categoría</label>
                <select class="form-control form-control-sm categoria" name="vehiculos[0][categoria]" id="categoria-0">
                    <option value="menores">Menores</option>
                    <option value="livianos">Livianos</option>
                    <option value="pesados">Pesados</option>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label small">Marca</label><br>
                <select class="form-control form-control-sm select2-marca" name="vehiculos[0][marca_id]" id="marca-0" data-index="0"></select>
            </div>
            <div class="col-md-5">
                <label class="form-label small">Modelo</label>
                <select class="form-control form-control-sm select2-modelo" name="vehiculos[0][modelo_id]" id="modelo-0" data-index="0" disabled></select>
            </div>
        </div>
        
        <!-- Segunda fila: Versión, Año, Color -->
        <div class="row g-2 mb-2">
            <div class="col-md-4">
                <label class="form-label small">Versión</label>
                <select class="form-control form-control-sm select2-version" name="vehiculos[0][version_id]" id="version-0" data-index="0" disabled></select>
            </div>
            <div class="col-md-4">
                <label class="form-label small">Año</label>
                <select class="form-control form-control-sm select2-anio" name="vehiculos[0][anio_modelo_id]" id="anio-0" data-index="0" disabled></select>
            </div>
            <div class="col-md-4">
                <label class="form-label small">Color</label>
                <select class="form-control form-control-sm select2-color" name="vehiculos[0][color_id]" id="color-0" data-index="0"></select>
            </div>
        </div>
        
        <!-- Tercera fila: Cantidad, Precio -->
        <div class="row g-2">
            <div class="col-md-3">
                <label class="form-label small">Cantidad</label>
                <input type="number" class="form-control form-control-sm cantidad" name="vehiculos[0][cantidad]" min="1" value="1" required>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Precio Unitario</label>
                <input type="number" class="form-control form-control-sm precio" name="vehiculos[0][precio_unitario]" step="0.01" required>
            </div>
        </div>
    </div>
    
    <div id="vehiculos-container">
        <!-- Aquí se agregarán más filas de vehículos -->
    </div>
    
    <div class="d-flex justify-content-end mt-3">
        <button type="button" class="btn btn-sm btn-success add-vehiculo-btn">
            <i class="fas fa-cart-plus me-1"></i> Agregar a la cotización
        </button>
    </div>
</div>