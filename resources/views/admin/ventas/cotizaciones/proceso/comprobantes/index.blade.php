<div class="card border shadow-none">
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-semibold">
                <i class="fas fa-file-invoice me-2 text-primary"></i> Comprobantes Registrados
            </h5>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoComprobante">
                <i class="fas fa-plus me-1"></i> Nuevo Comprobante
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th scope="col" width="120">Fecha</th>
                        <th scope="col" width="140">Tipo</th>
                        <th scope="col" width="140">Serie-Número</th>
                        <th scope="col">Detalles</th>
                        <th scope="col" width="120">Monto</th>
                        <th scope="col" width="100">Documento</th>
                        <th scope="col" width="100">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($cotizacion->comprobantes) && $cotizacion->comprobantes->count() > 0)
                        @foreach($cotizacion->comprobantes as $comprobante)
                        <tr>
                            <td>{{ $comprobante->fecha_emision->format('d/m/Y') }}</td>
                            <td><span class="badge {{ $comprobante->tipo === 'Factura' ? 'bg-primary' : 'bg-info' }}">{{ $comprobante->tipo }}</span></td>
                            <td>{{ $comprobante->serie }}-{{ $comprobante->numero }}</td>
                            <td>{{ $comprobante->detalle }}</td>
                            <td class="text-end fw-medium">
                                {{ $comprobante->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}
                                {{ number_format($comprobante->monto, 2) }}
                            </td>
                            <td>
                                @if($comprobante->archivo)
                                <a href="{{ asset('storage/'.$comprobante->archivo) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                @else
                                <span class="text-muted">No</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditarComprobante{{ $comprobante->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEliminarComprobante{{ $comprobante->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iODAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBkPSJNMTYgMkg4QzcuNDQ3NzIgMiA3IDIuNDQ3NzIgNyAzVjE2QzcgMTYuNTUyMyA3LjQ0NzcyIDE3IDggMTdIMjBDMjAuNTUyMyAxNyAyMSAxNi41NTIzIDIxIDE2VjdDMjEgNi40NDc3MiAyMC41NTIzIDYgMjAgNkgxN1YzQzE3IDIuNDQ3NzIgMTYuNTUyMyAyIDE2IDJaIiBzdHJva2U9IiM5OTkiIHN0cm9rZS13aWR0aD0iMiIvPjxwYXRoIGQ9Ik0xNiA2LjAxMDM3TDIxIDYuMDEwMzciIHN0cm9rZT0iIzk5OSIgc3Ryb2tlLXdpZHRoPSIyIi8+PHBhdGggZD0iTTMgOEgxN1YyMUgzVjhaIiBmaWxsPSIjRTdFN0U3IiBzdHJva2U9IiM5OTkiIHN0cm9rZS13aWR0aD0iMiIvPjwvc3ZnPg==" 
                                        width="80" height="80" alt="No hay comprobantes" class="opacity-50 mb-3">
                                    <p class="text-muted mb-0">No hay comprobantes registrados</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para crear un nuevo comprobante -->
<div class="modal fade" id="modalNuevoComprobante" tabindex="-1" aria-labelledby="modalNuevoComprobanteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.ventas.cotizaciones.comprobantes.store', $cotizacion) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNuevoComprobanteLabel">Registrar Nuevo Comprobante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="tipo" class="form-label">Tipo de comprobante</label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="Factura">Factura</option>
                                <option value="Boleta">Boleta</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="fecha_emision" class="form-label">Fecha de emisión</label>
                            <input type="date" class="form-control" id="fecha_emision" name="fecha_emision" value="{{ date('Y-m-d') }}" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="serie" class="form-label">Serie</label>
                            <input type="text" class="form-control" id="serie" name="serie" placeholder="Ej: F001, B001" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="numero" class="form-label">Número</label>
                            <input type="text" class="form-control" id="numero" name="numero" placeholder="Ej: 00012345" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="monto" class="form-label">Monto</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $cotizacion->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}</span>
                                <input type="number" class="form-control" id="monto" name="monto" step="0.01" min="0" value="{{ $cotizacion->total }}" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="moneda" class="form-label">Moneda</label>
                            <select class="form-select" id="moneda" name="moneda" required>
                                <option value="Soles" {{ $cotizacion->moneda === 'Soles' ? 'selected' : '' }}>Soles (S/)</option>
                                <option value="Dólares" {{ $cotizacion->moneda === 'Dólares' ? 'selected' : '' }}>Dólares (US$)</option>
                            </select>
                        </div>
                        
                        <div class="col-md-12">
                            <label for="detalle" class="form-label">Detalle</label>
                            <textarea class="form-control" id="detalle" name="detalle" rows="2" placeholder="Detalles adicionales del comprobante"></textarea>
                        </div>
                        
                        <div class="col-md-12">
                            <label for="archivo" class="form-label">Archivo del comprobante</label>
                            <input type="file" class="form-control" id="archivo" name="archivo" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">Formatos permitidos: PDF e imágenes. Máx. 2MB</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Comprobante</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modales para editar comprobantes (se generan dinámicamente) -->
@if(isset($cotizacion->comprobantes) && $cotizacion->comprobantes->count() > 0)
    @foreach($cotizacion->comprobantes as $comprobante)
    <div class="modal fade" id="modalEditarComprobante{{ $comprobante->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.ventas.cotizaciones.comprobantes.update', [$cotizacion, $comprobante]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Comprobante</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="tipo_edit" class="form-label">Tipo de comprobante</label>
                                <select class="form-select" id="tipo_edit" name="tipo" required>
                                    <option value="Factura" {{ $comprobante->tipo === 'Factura' ? 'selected' : '' }}>Factura</option>
                                    <option value="Boleta" {{ $comprobante->tipo === 'Boleta' ? 'selected' : '' }}>Boleta</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="fecha_emision_edit" class="form-label">Fecha de emisión</label>
                                <input type="date" class="form-control" id="fecha_emision_edit" name="fecha_emision" value="{{ $comprobante->fecha_emision->format('Y-m-d') }}" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="serie_edit" class="form-label">Serie</label>
                                <input type="text" class="form-control" id="serie_edit" name="serie" value="{{ $comprobante->serie }}" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="numero_edit" class="form-label">Número</label>
                                <input type="text" class="form-control" id="numero_edit" name="numero" value="{{ $comprobante->numero }}" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="monto_edit" class="form-label">Monto</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ $comprobante->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}</span>
                                    <input type="number" class="form-control" id="monto_edit" name="monto" step="0.01" min="0" value="{{ $comprobante->monto }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="moneda_edit" class="form-label">Moneda</label>
                                <select class="form-select" id="moneda_edit" name="moneda" required>
                                    <option value="Soles" {{ $comprobante->moneda === 'Soles' ? 'selected' : '' }}>Soles (S/)</option>
                                    <option value="Dólares" {{ $comprobante->moneda === 'Dólares' ? 'selected' : '' }}>Dólares (US$)</option>
                                </select>
                            </div>
                            
                            <div class="col-md-12">
                                <label for="detalle_edit" class="form-label">Detalle</label>
                                <textarea class="form-control" id="detalle_edit" name="detalle" rows="2">{{ $comprobante->detalle }}</textarea>
                            </div>
                            
                            <div class="col-md-12">
                                <label for="archivo_edit" class="form-label">Archivo del comprobante</label>
                                <input type="file" class="form-control" id="archivo_edit" name="archivo" accept=".pdf,.jpg,.jpeg,.png">
                                <div class="form-text">Formatos permitidos: PDF e imágenes. Máx. 2MB</div>
                                @if($comprobante->archivo)
                                <div class="d-flex align-items-center mt-2">
                                    <div class="form-check me-2">
                                        <input class="form-check-input" type="checkbox" id="mantener_archivo{{ $comprobante->id }}" name="mantener_archivo" value="1" checked>
                                        <label class="form-check-label" for="mantener_archivo{{ $comprobante->id }}">Mantener archivo actual</label>
                                    </div>
                                    <a href="{{ asset('storage/'.$comprobante->archivo) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-file-pdf me-1"></i> Ver
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar Comprobante</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal para eliminar comprobantes -->
    <div class="modal fade" id="modalEliminarComprobante{{ $comprobante->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <form action="{{ route('admin.ventas.cotizaciones.comprobantes.destroy', [$cotizacion, $comprobante]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar Eliminación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                        <p>¿Está seguro de eliminar este comprobante?</p>
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