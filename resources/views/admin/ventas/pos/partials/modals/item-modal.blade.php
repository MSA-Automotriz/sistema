{{-- resources/views/admin/ventas/pos/partials/modals/item-modal.blade.php --}}
<div class="modal fade" id="agregarItemModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modal-item-title">
                    <i class="fas fa-plus me-2"></i>Agregar Ítem
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-agregar-item">
                    <input type="hidden" id="item-id" value="">
                    <input type="hidden" id="item-tipo" value="">
                    
                    <div class="mb-3">
                        <label for="item-nombre" class="form-label fw-bold">Producto/Servicio</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-tag text-primary"></i>
                            </span>
                            <input type="text" class="form-control" id="item-nombre" readonly>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="item-codigo" class="form-label fw-bold">Código</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-barcode text-primary"></i>
                            </span>
                            <input type="text" class="form-control" id="item-codigo" readonly>
                        </div>
                    </div>
                    
                    <!-- Container de stock (solo para partes) -->
                    <div class="row mb-3" id="stock-container">
                        <div class="col-md-12">
                            <label for="item-stock" class="form-label fw-bold">Stock Disponible</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-boxes text-primary"></i>
                                </span>
                                <input type="text" class="form-control" id="item-stock" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="item-cantidad" class="form-label fw-bold">Cantidad</label>
                            <div class="input-group">
                                <button class="btn btn-outline-secondary" type="button" onclick="cambiarCantidad(-1)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" class="form-control text-center" id="item-cantidad" min="1" value="1" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="cambiarCantidad(1)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="item-precio" class="form-label fw-bold">Precio Unitario</label>
                            <div class="input-group">
                                <span class="input-group-text" id="span-moneda">S/</span>
                                <input type="number" class="form-control" id="item-precio" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="item-descuento" class="form-label fw-bold">
                            Descuento 
                            <span class="badge bg-secondary" id="descuento-valor">0%</span>
                        </label>
                        <input type="range" class="form-range" id="item-descuento" min="0" max="50" step="1" value="0">
                        <div class="d-flex justify-content-between small text-muted">
                            <span>0%</span>
                            <span>25%</span>
                            <span>50%</span>
                        </div>
                    </div>
                    
                    <!-- Resumen del item -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-calculator me-2"></i>Resumen</h6>
                        <div class="d-flex justify-content-between">
                            <span>Subtotal:</span>
                            <span id="modal-subtotal">S/ 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Descuento:</span>
                            <span id="modal-descuento">S/ 0.00</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total:</span>
                            <span id="modal-total">S/ 0.00</span>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-cart-plus me-2"></i>Agregar a la Venta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>