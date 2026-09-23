{{-- resources/views/admin/ventas/pos/partials/cart/options-section.blade.php --}}
<div class="p-3 border-bottom">
    <h6 class="mb-3">
        <i class="fas fa-sliders-h me-2 text-primary"></i>Opciones Avanzadas
    </h6>
    
    <!-- Switch para generar requerimiento -->
    <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" id="generar-requerimiento">
        <label class="form-check-label small" for="generar-requerimiento">
            <strong>Permitir venta sin stock</strong><br>
            <small class="text-muted">Genera requerimiento automático para productos agotados</small>
        </label>
    </div>
    
    <!-- Control de porcentaje de abono -->
    <div class="mb-3">
        <label for="porcentaje-abono" class="form-label small fw-bold">
            Porcentaje de abono 
            <span class="badge bg-info" id="porcentaje-abono-valor">100%</span>
        </label>
        <input type="range" class="form-range" id="porcentaje-abono" 
               min="0" max="100" step="5" value="100">
        <div class="d-flex justify-content-between small text-muted">
            <span>0%</span>
            <span>25%</span>
            <span>50%</span>
            <span>75%</span>
            <span>100%</span>
        </div>
    </div>
</div>