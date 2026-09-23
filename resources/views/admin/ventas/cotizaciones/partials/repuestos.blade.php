<div class="card shadow-sm p-4 mb-4 border-0">
    <h5 class="mb-4 text-primary fw-semibold">Agregar Repuestos</h5>
    
    <div class="repuestos-row mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-medium text-muted">Código o Nombre</label>
                <select class="form-select form-select-sm select2-repuestos shadow-sm" name="repuestos[0][repuesto_id]" style="border-radius: 0.375rem;">
                    <option value="">Seleccione un repuesto</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-medium text-muted">Unidad</label>
                <input type="text" class="form-control form-control-sm shadow-sm" name="repuestos[0][unidad]" readonly style="border-radius: 0.375rem; background-color: #f8f9fa;">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-medium text-muted">Cantidad</label>
                <input type="number" class="form-control form-control-sm cantidad shadow-sm" name="repuestos[0][cantidad]" min="1" value="1" required style="border-radius: 0.375rem;">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-medium text-muted">Precio Unit.</label>
                <input type="number" class="form-control form-control-sm precio shadow-sm" name="repuestos[0][precio_unitario]" step="0.01" required style="border-radius: 0.375rem;">
            </div>
        </div>
    </div>
    
    <div id="repuestos-container">
        <!-- Aquí se agregarán más filas de repuestos -->
    </div>
    
    <div class="d-flex justify-content-end mt-4">
        <button type="button" class="btn btn-sm btn-primary add-repuesto-btn px-3" style="border-radius: 0.375rem;">
            <i class="fas fa-plus-circle me-2"></i> Agregar a la cotización
        </button>
    </div>
</div>

<style>
.card {
    background-color: #fff;
    border-radius: 0.5rem;
    transition: box-shadow 0.3s ease;
}
.card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
.form-control, .form-select {
    border: 1px solid #ced4da;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}
.form-control:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
.form-label {
    margin-bottom: 0.25rem;
}
.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
    transition: background-color 0.3s ease, transform 0.2s ease;
}
.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
    transform: translateY(-1px);
}
.btn-outline-danger {
    transition: background-color 0.3s ease, color 0.3s ease;
}
.btn-outline-danger:hover {
    background-color: #dc3545;
    color: #fff;
}
</style>