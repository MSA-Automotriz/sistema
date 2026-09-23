<div class="modal fade" id="modalEliminarPlaca{{ $placa->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.ventas.cotizaciones.placas.destroy', [$cotizacion, $placa]) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        ¿Está seguro que desea eliminar esta placa?
                        <p class="mb-0 mt-2 fw-bold">{{ $placa->tipo_texto }} - {{ $placa->numero_placa ?? 'Sin número asignado' }}</p>
                        <p class="small mb-0 mt-2">Esta acción eliminará también todos los documentos asociados.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar Placa</button>
                </div>
            </form>
        </div>
    </div>
</div>