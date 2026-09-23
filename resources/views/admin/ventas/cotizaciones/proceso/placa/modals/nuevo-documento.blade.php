<div class="modal fade" id="modalNuevoDocumentoPlaca{{ $placa->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.ventas.cotizaciones.placas.documentos.store', [$cotizacion, $placa]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Documento para Placa {{ $placa->tipo_texto }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="nombre" class="form-label">Nombre del documento</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="col-md-6">
                            <label for="tipo" class="form-label">Tipo de documento</label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="">Seleccione...</option>
                                <option value="rotativa">Boleta</option>
                                <option value="definitiva">Factura</option>
                                <option value="guia_remision">Guía de remisión</option>
                                <option value="otros">Otros</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="fecha" class="form-label">Fecha del documento</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" required 
                                value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-12">
                            <label for="archivo" class="form-label">Archivo</label>
                            <input type="file" class="form-control" id="archivo" name="archivo" required 
                                accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">Formatos permitidos: PDF, JPG, PNG. Máx. 5MB</div>
                        </div>
                        <div class="col-12">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Documento</button>
                </div>
            </form>
        </div>
    </div>
</div>