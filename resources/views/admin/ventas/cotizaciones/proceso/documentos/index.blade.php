<div class="card border shadow-none">
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-semibold">
                <i class="fas fa-folder-open me-2 text-primary"></i> Documentos Adicionales
            </h5>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoDocumento">
                <i class="fas fa-plus me-1"></i> Nuevo Documento
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th scope="col" width="120">Fecha</th>
                        <th scope="col">Nombre del Documento</th>
                        <th scope="col" width="100">Archivo</th>
                        <th scope="col" width="100">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @if($cotizacion->documentos && $cotizacion->documentos->count() > 0)
                        @foreach($cotizacion->documentos as $documento)
                        <tr>
                            <td>{{ $documento->created_at->format('d/m/Y') }}</td>
                            <td>{{ $documento->nombre }}</td>
                            <td>
                                @if($documento->archivo)
                                <a href="{{ asset('storage/'.$documento->archivo) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-file"></i>
                                    <!-- Depuración: Muestra la URL completa -->
                                    <small class="text-muted d-none">{{ asset('storage/'.$documento->archivo) }}</small>
                                </a>
                                @else
                                <span class="text-muted">No</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditarDocumento{{ $documento->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEliminarDocumento{{ $documento->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">No hay documentos adicionales registrados</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para crear un nuevo documento -->
<div class="modal fade" id="modalNuevoDocumento" tabindex="-1" aria-labelledby="modalNuevoDocumentoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.ventas.cotizaciones.documentos.store', $cotizacion) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNuevoDocumentoLabel">Registrar Nuevo Documento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3 mb-3">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="nombre" class="form-label">Nombre del documento</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej: Contrato, Recibo, etc." value="{{ old('nombre') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="categoria" class="form-label">Categoría</label>
                            <select class="form-select" id="categoria" name="categoria" required>
                                <option value="Contrato" {{ old('categoria') === 'Contrato' ? 'selected' : '' }}>Contrato</option>
                                <option value="Recibo" {{ old('categoria') === 'Recibo' ? 'selected' : '' }}>Recibo</option>
                                <option value="Informe" {{ old('categoria') === 'Informe' ? 'selected' : '' }}>Informe</option>
                                <option value="Otro" {{ old('categoria') === 'Otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="fecha" class="form-label">Fecha del documento</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-12">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <label for="archivo" class="form-label">Archivo del documento</label>
                            <input type="file" class="form-control" id="archivo" name="archivo" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                            <div class="form-text">Formatos permitidos: PDF, imágenes, Word, Excel. Máx. 10MB</div>
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

<!-- Modales para editar y eliminar documentos -->
@if($cotizacion->documentos && $cotizacion->documentos->count() > 0)
    @foreach($cotizacion->documentos as $documento)
    <div class="modal fade" id="modalEditarDocumento{{ $documento->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.ventas.cotizaciones.documentos.update', [$cotizacion, $documento]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Documento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="alert alert-danger rounded-3 mb-3">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="nombre_edit" class="form-label">Nombre del documento</label>
                                <input type="text" class="form-control" id="nombre_edit" name="nombre" value="{{ old('nombre', $documento->nombre) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="categoria_edit" class="form-label">Categoría</label>
                                <select class="form-select" id="categoria_edit" name="categoria" required>
                                    <option value="Contrato" {{ old('categoria', $documento->categoria) === 'Contrato' ? 'selected' : '' }}>Contrato</option>
                                    <option value="Recibo" {{ old('categoria', $documento->categoria) === 'Recibo' ? 'selected' : '' }}>Recibo</option>
                                    <option value="Informe" {{ old('categoria', $documento->categoria) === 'Informe' ? 'selected' : '' }}>Informe</option>
                                    <option value="Otro" {{ old('categoria', $documento->categoria) === 'Otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="fecha_edit" class="form-label">Fecha del documento</label>
                                <input type="date" class="form-control" id="fecha_edit" name="fecha" value="{{ old('fecha', $documento->fecha->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-12">
                                <label for="descripcion_edit" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion_edit" name="descripcion" rows="3">{{ old('descripcion', $documento->descripcion) }}</textarea>
                            </div>
                            <div class="col-md-12">
                                <label for="archivo_edit" class="form-label">Archivo del documento</label>
                                <input type="file" class="form-control" id="archivo_edit" name="archivo" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                                <div class="form-text">Formatos permitidos: PDF, imágenes, Word, Excel. Máx. 10MB</div>
                                @if($documento->archivo)
                                <div class="d-flex align-items-center mt-2">
                                    <div class="form-check me-2">
                                        <input class="form-check-input" type="checkbox" id="mantener_archivo{{ $documento->id }}" name="mantener_archivo" value="1" checked>
                                        <label class="form-check-label" for="mantener_archivo{{ $documento->id }}">Mantener archivo actual</label>
                                    </div>
                                    <a href="{{ asset('storage/'.$documento->archivo) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-file me-1"></i> Ver
                                        <!-- Depuración: Muestra la URL completa -->
                                        <small class="text-muted d-none">{{ asset('storage/'.$documento->archivo) }}</small>
                                    </a>
                                </div>
                                @endif
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
    
    <div class="modal fade" id="modalEliminarDocumento{{ $documento->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <form action="{{ route('admin.ventas.cotizaciones.documentos.destroy', [$cotizacion, $documento]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar Eliminación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                        <p>¿Está seguro de eliminar este documento?</p>
                        <p class="mb-0 text-muted small">Esta acción no se puede deshacer.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
@endif