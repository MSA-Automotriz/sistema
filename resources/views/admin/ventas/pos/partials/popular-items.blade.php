{{-- resources/views/admin/ventas/pos/partials/popular-items.blade.php --}}
<div class="card border-0 shadow-sm mb-4 card-hover-effect">
    <div class="card-header bg-white border-bottom-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-fire text-warning me-2"></i>
                Productos Populares
            </h5>
            <button class="btn btn-sm btn-outline-primary" onclick="cargarItemsPopulares()">
                <i class="fas fa-sync-alt me-1"></i>Actualizar
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="row row-cols-2 row-cols-md-4 g-3" id="popular-items">
            <!-- Se cargará mediante JavaScript -->
            <div class="col">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-3">
                        <div class="spinner-border text-primary mb-2" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <small class="text-muted">Cargando populares...</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>