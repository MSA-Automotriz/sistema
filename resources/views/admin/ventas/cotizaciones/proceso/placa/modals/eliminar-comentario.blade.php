<div class="modal fade" id="modalEliminarComentario{{ $comentario->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3">
            <form action="{{ route('admin.ventas.cotizaciones.placas.comentarios.destroy', [$cotizacion->id, $placa->id, $comentario->id]) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-semibold">Eliminar Comentario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p>¿Está seguro de que desea eliminar este comentario?</p>
                    <p class="small text-muted">{{ $comentario->comentario }}</p>
                </div>
                <div class="modal-footer border-top py-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Eliminar</button>
                </div>
            </form>
        </div>
    </div>
</div>