<div class="modal fade" id="modalEditarDocumentoPlaca{{ $documento->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.ventas.cotizaciones.placas.documentos.update', [$cotizacion, $placa, $documento]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar Documento para Placa {{ $placa->tipo_texto }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="nombre" class="form-label">Nombre del documento</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required value="{{ $documento->nombre }}">
                        </div>
                        <div class="col-md-6">
                            <label for="tipo" class="form-label">Tipo de documento</label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="">Seleccione...</option>
                                <option value="rotativa" {{ $documento->tipo == 'rotativa' ? 'selected' : '' }}>Placa rotativa</option>
                                <option value="definitiva" {{ $documento->tipo == 'definitiva' ? 'selected' : '' }}>Placa definitiva</option>
                                <option value="guia_remision" {{ $documento->tipo == 'guia_remision' ? 'selected' : '' }}>Guía de remisión</option>
                                <option value="otros" {{ $documento->tipo == 'otros' ? 'selected' : '' }}>Otros</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="fecha" class="form-label">Fecha del documento</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" required
                                value="{{ $documento->fecha->format('Y-m-d') }}">
                        </div>
                        <div class="col-12">
                            <label for="archivo" class="form-label">Archivo</label>
                            <input type="file" class="form-control" id="archivo" name="archivo"
                                accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">Formatos permitidos: PDF, JPG, PNG. Máx. 5MB</div>
                            @if($documento->archivo)
                                <div class="mt-2">
                                    <small>Archivo actual: <a href="{{ asset('storage/' . $documento->archivo) }}" target="_blank">Ver documento</a></small>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="mantener_archivo" name="mantener_archivo" value="1" checked>
                                        <label class="form-check-label" for="mantener_archivo">Mantener archivo actual si no se sube uno nuevo</label>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-12">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="2">{{ $documento->observaciones }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Documento</button>
                </div>
            </form>
        </div>
    </div>
</div>