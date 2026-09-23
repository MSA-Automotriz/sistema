<div class="card border shadow-none">
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-semibold">
                <i class="fas fa-file-contract me-2 text-primary"></i> Documentos SUNARP
            </h5>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoDocumentoSunarp">
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
                        <th scope="col" width="150">Tipo</th>
                        <th scope="col" width="100">Archivo</th>
                        <th scope="col" width="100">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @if($cotizacion->documentos_sunarp && $cotizacion->documentos_sunarp->count() > 0)
                        @foreach($cotizacion->documentos_sunarp as $documento)
                        <tr>
                            <td>{{ $documento->fecha->format('d/m/Y') }}</td>
                            <td>{{ $documento->nombre }}</td>
                            <td><span class="badge bg-light text-dark">{{ $documento->tipo }}</span></td>
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
                                        data-bs-target="#modalEditarDocumentoSunarp{{ $documento->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEliminarDocumentoSunarp{{ $documento->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <i class="fas fa-file-contract fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">No hay documentos SUNARP registrados</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para crear un nuevo documento SUNARP -->
<div class="modal fade" id="modalNuevoDocumentoSunarp" tabindex="-1" aria-labelledby="modalNuevoDocumentoSunarpLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.ventas.cotizaciones.sunarp.store', $cotizacion) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNuevoDocumentoSunarpLabel">Registrar Nuevo Documento SUNARP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="nombre" class="form-label">Nombre del documento</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej: Tarjeta de Propiedad, Contrato, etc." required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="fecha" class="form-label">Fecha del documento</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" value="{{ date('Y-m-d') }}" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="tipo" class="form-label">Tipo de documento</label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="Tarjeta de Propiedad">Tarjeta de Propiedad</option>
                                <option value="Contrato de Compra-Venta">Contrato de Compra-Venta</option>
                                <option value="Poder">Poder</option>
                                <option value="Partida Registral">Partida Registral</option>
                                <option value="Formulario">Formulario</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        
                        <div class="col-md-12">
                            <label for="archivo" class="form-label">Archivo del documento</label>
                            <input type="file" class="form-control" id="archivo" name="archivo" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">Formatos permitidos: PDF e imágenes. Máx. 5MB</div>
                        </div>
                        
                        <div class="col-md-12">
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

<!-- Modales para editar y eliminar documentos SUNARP -->
@if($cotizacion->documentos_sunarp && $cotizacion->documentos_sunarp->count() > 0)
    @foreach($cotizacion->documentos_sunarp as $documento)
    <div class="modal fade" id="modalEditarDocumentoSunarp{{ $documento->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.ventas.cotizaciones.sunarp.update', [$cotizacion, $documento]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Documento SUNARP</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="nombre_edit" class="form-label">Nombre del documento</label>
                                <input type="text" class="form-control" id="nombre_edit" name="nombre" value="{{ $documento->nombre }}" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="fecha_edit" class="form-label">Fecha del documento</label>
                                <input type="date" class="form-control" id="fecha_edit" name="fecha" value="{{ $documento->fecha->format('Y-m-d') }}" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="tipo_edit" class="form-label">Tipo de documento</label>
                                <select class="form-select" id="tipo_edit" name="tipo" required>
                                    <option value="Tarjeta de Propiedad" {{ $documento->tipo === 'Tarjeta de Propiedad' ? 'selected' : '' }}>Tarjeta de Propiedad</option>
                                    <option value="Contrato de Compra-Venta" {{ $documento->tipo === 'Contrato de Compra-Venta' ? 'selected' : '' }}>Contrato de Compra-Venta</option>
                                    <option value="Poder" {{ $documento->tipo === 'Poder' ? 'selected' : '' }}>Poder</option>
                                    <option value="Partida Registral" {{ $documento->tipo === 'Partida Registral' ? 'selected' : '' }}>Partida Registral</option>
                                    <option value="Formulario" {{ $documento->tipo === 'Formulario' ? 'selected' : '' }}>Formulario</option>
                                    <option value="Otro" {{ $documento->tipo === 'Otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                            </div>
                            
                            <div class="col-md-12">
                                <label for="archivo_edit" class="form-label">Archivo del documento</label>
                                <input type="file" class="form-control" id="archivo_edit" name="archivo" accept=".pdf,.jpg,.jpeg,.png">
                                <div class="form-text">Formatos permitidos: PDF e imágenes. Máx. 5MB</div>
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
                            
                            <div class="col-md-12">
                                <label for="observaciones_edit" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observaciones_edit" name="observaciones" rows="2">{{ $documento->observaciones }}</textarea>
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
    
    <div class="modal fade" id="modalEliminarDocumentoSunarp{{ $documento->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <form action="{{ route('admin.ventas.cotizaciones.sunarp.destroy', [$cotizacion, $documento]) }}" method="POST">
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