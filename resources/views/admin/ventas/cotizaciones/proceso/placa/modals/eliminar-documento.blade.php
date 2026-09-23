<div class="modal fade" id="modalEliminarDocumentoPlaca{{ $documento->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.ventas.cotizaciones.placas.documentos.destroy', [$cotizacion, $placa, $documento]) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar Documento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="bx bx-error-circle text-danger" style="font-size: 6rem;"></i>
                    </div>
                    <p class="text-center mb-1">¿Está seguro que desea eliminar este documento?</p>
                    <p class="text-center mb-0"><strong>{{ $documento->nombre }}</strong></p>
                    <p class="text-center text-muted small">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar Documento</button>
                </div>
            </form>
        </div>
    </div>
</div>