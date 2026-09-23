{{-- resources/views/admin/ventas/pos/partials/modals/success-modal.blade.php --}}
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>¡Venta Procesada!
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-4">
                    <i class="fas fa-check-circle fa-5x text-success mb-3"></i>
                    <h4>¡Operación exitosa!</h4>
                    <p class="text-muted">La venta ha sido registrada correctamente en el sistema.</p>
                </div>
                
                <div class="alert alert-success">
                    <div class="row text-center">
                        <div class="col-6">
                            <strong>Cotización</strong><br>
                            <span id="cotizacion-codigo" class="badge bg-success fs-6">COT-20240001</span>
                        </div>
                        <div class="col-6">
                            <strong>Fecha</strong><br>
                            <span class="badge bg-info fs-6">{{ date('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Información de requerimientos (se muestra dinámicamente) -->
                <div id="requerimientos-info"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cerrar
                </button>
                <a href="#" class="btn btn-primary" id="btn-ver-cotizacion">
                    <i class="fas fa-eye me-2"></i>Ver Cotización
                </a>
                <button type="button" class="btn btn-success" id="btn-nueva-venta">
                    <i class="fas fa-plus me-2"></i>Nueva Venta
                </button>
            </div>
        </div>
    </div>
</div>