<div class="card p-3 mb-4">
    <h5>Agregar Servicios</h5>
    <div class="form-row servicios-row">
        <div class="col-md-6">
            <label>Código o Nombre</label>
            <select class="form-control select2-servicios" name="servicios[0][servicio_id]" data-url="{{ route('admin.ventas.cotizaciones.buscarServicios') }}">
                <option value="">Seleccione un servicio</option>
            </select>
        </div>
        <div class="col-md-2">
            <label>Cantidad</label>
            <input type="number" class="form-control" name="servicios[0][cantidad]" min="1" required>
        </div>
        <div class="col-md-2">
            <label>Precio Unitario</label>
            <input type="number" class="form-control" name="servicios[0][precio_unitario]" step="0.01" required>
        </div>
    </div>
    <button type="button" class="btn btn-sm btn-primary mt-2 add-servicio">Agregar otro servicio</button>
    <button type="button" class="btn btn-sm btn-success mt-2 add-servicio-btn">Agregar a la cotización</button>
</div>