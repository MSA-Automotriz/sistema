<div class="card border shadow-none">
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-semibold">
                <i class="fas fa-money-bill-wave me-2 text-primary"></i> Pagos Registrados
            </h5>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoPago">
                <i class="fas fa-plus me-1"></i> Nuevo Pago
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th scope="col" width="120">Fecha</th>
                        <th scope="col">Concepto</th>
                        <th scope="col" width="140">Monto</th>
                        <th scope="col" width="100">Tipo</th>
                        <th scope="col" width="100">Comprobante</th>
                        <th scope="col" width="100">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($cotizacion->pagos) && $cotizacion->pagos->count() > 0)
                        @foreach($cotizacion->pagos as $pago)
                        <tr>
                            <td>{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                            <td>{{ $pago->concepto }}</td>
                            <td class="text-end fw-medium">
                                {{ $pago->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}
                                {{ number_format($pago->monto, 2) }}
                            </td>
                            <td><span class="badge {{ $pago->tipo === 'Inicial' ? 'bg-success' : 'bg-primary' }}">{{ $pago->tipo }}</span></td>
                            <td>
                                @if($pago->comprobante)
                                <a href="{{ asset('storage/'.$pago->comprobante) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-file-image"></i>
                                </a>
                                @else
                                <span class="text-muted">No</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditarPago{{ $pago->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEliminarPago{{ $pago->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iODAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBkPSJNMTYgMkg4QzcuNDQ3NzIgMiA3IDIuNDQ3NzIgNyAzVjE2QzcgMTYuNTUyMyA3LjQ0NzcyIDE3IDggMTdIMjBDMjAuNTUyMyAxNyAyMSAxNi41NTIzIDIxIDE2VjdDMjEgNi40NDc3MiAyMC41NTIzIDYgMjAgNkgxN1YzQzE3IDIuNDQ3NzIgMTYuNTUyMyAyIDE2IDJaIiBzdHJva2U9IiM5OTkiIHN0cm9rZS13aWR0aD0iMiIvPjxwYXRoIGQ9Ik0xNiA2LjAxMDM3TDIxIDYuMDEwMzciIHN0cm9rZT0iIzk5OSIgc3Ryb2tlLXdpZHRoPSIyIi8+PHBhdGggZD0iTTMgOEgxN1YyMUgzVjhaIiBmaWxsPSIjRTdFN0U3IiBzdHJva2U9IiM5OTkiIHN0cm9rZS13aWR0aD0iMiIvPjwvc3ZnPg==" 
                                        width="80" height="80" alt="No hay pagos" class="opacity-50 mb-3">
                                    <p class="text-muted mb-0">No hay pagos registrados</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    @if(isset($cotizacion->pagos) && $cotizacion->pagos->count() > 0)
                    <tr class="table-light fw-bold">
                        <td colspan="2" class="text-end">Total:</td>
                        <td class="text-end">
                            {{ $cotizacion->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}
                            {{ number_format($cotizacion->pagos->sum('monto'), 2) }}
                        </td>
                        <td colspan="3"></td>
                    </tr>
                    <tr class="table-light">
                        <td colspan="2" class="text-end">Saldo pendiente:</td>
                        <td class="text-end {{ $cotizacion->total - $cotizacion->pagos->sum('monto') > 0 ? 'text-danger' : 'text-success' }} fw-bold">
                            {{ $cotizacion->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}
                            {{ number_format($cotizacion->total - $cotizacion->pagos->sum('monto'), 2) }}
                        </td>
                        <td colspan="3"></td>
                    </tr>
                    @endif
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Modal para crear un nuevo pago -->
<div class="modal fade" id="modalNuevoPago" tabindex="-1" aria-labelledby="modalNuevoPagoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.ventas.cotizaciones.pagos.store', $cotizacion) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNuevoPagoLabel">Registrar Nuevo Pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="concepto" class="form-label">Concepto</label>
                            <input type="text" class="form-control" id="concepto" name="concepto" placeholder="Ej: Pago inicial, cuota 1, etc." required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="fecha_pago" class="form-label">Fecha de pago</label>
                            <input type="date" class="form-control" id="fecha_pago" name="fecha_pago" value="{{ date('Y-m-d') }}" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="tipo" class="form-label">Tipo de pago</label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="Inicial">Inicial</option>
                                <option value="Parcial">Parcial</option>
                                <option value="Final">Final</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="moneda" class="form-label">Moneda</label>
                            <select class="form-select" id="moneda" name="moneda" required>
                                <option value="Soles" {{ $cotizacion->moneda === 'Soles' ? 'selected' : '' }}>Soles (S/)</option>
                                <option value="Dólares" {{ $cotizacion->moneda === 'Dólares' ? 'selected' : '' }}>Dólares (US$)</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="monto" class="form-label">Monto</label>
                            <div class="input-group">
                                <span class="input-group-text" id="simbolo-moneda">{{ $cotizacion->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}</span>
                                <input type="number" class="form-control" id="monto" name="monto" step="0.01" min="0" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="medio_pago" class="form-label">Medio de pago</label>
                            <select class="form-select" id="medio_pago" name="medio_pago" required>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Transferencia">Transferencia</option>
                                <option value="Tarjeta">Tarjeta de crédito/débito</option>
                                <option value="Yape">Yape</option>
                                <option value="Plin">Plin</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        
                        <div class="col-md-12">
                            <label for="comprobante" class="form-label">Comprobante de pago</label>
                            <input type="file" class="form-control" id="comprobante" name="comprobante" accept="image/*,.pdf">
                            <div class="form-text">Formatos permitidos: imágenes y PDF. Máx. 2MB</div>
                        </div>
                        
                        <div class="col-md-12">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Pago</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modales para editar pagos (se generan dinámicamente) -->
@if(isset($cotizacion->pagos) && $cotizacion->pagos->count() > 0)
    @foreach($cotizacion->pagos as $pago)
    <div class="modal fade" id="modalEditarPago{{ $pago->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.ventas.cotizaciones.pagos.update', [$cotizacion, $pago]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Pago</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="concepto_edit" class="form-label">Concepto</label>
                                <input type="text" class="form-control" id="concepto_edit" name="concepto" value="{{ $pago->concepto }}" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="fecha_pago_edit" class="form-label">Fecha de pago</label>
                                <input type="date" class="form-control" id="fecha_pago_edit" name="fecha_pago" value="{{ $pago->fecha_pago->format('Y-m-d') }}" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="tipo_edit" class="form-label">Tipo de pago</label>
                                <select class="form-select" id="tipo_edit" name="tipo" required>
                                    <option value="Inicial" {{ $pago->tipo === 'Inicial' ? 'selected' : '' }}>Inicial</option>
                                    <option value="Parcial" {{ $pago->tipo === 'Parcial' ? 'selected' : '' }}>Parcial</option>
                                    <option value="Final" {{ $pago->tipo === 'Final' ? 'selected' : '' }}>Final</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="moneda_edit{{ $pago->id }}" class="form-label">Moneda</label>
                                <select class="form-select moneda-select" id="moneda_edit{{ $pago->id }}" name="moneda" data-pago-id="{{ $pago->id }}" required>
                                    <option value="Soles" {{ $pago->moneda === 'Soles' ? 'selected' : '' }}>Soles (S/)</option>
                                    <option value="Dólares" {{ $pago->moneda === 'Dólares' ? 'selected' : '' }}>Dólares (US$)</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="monto_edit" class="form-label">Monto</label>
                                <div class="input-group">
                                    <span class="input-group-text simbolo-moneda-edit" id="simbolo-moneda-edit{{ $pago->id }}">{{ $pago->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}</span>
                                    <input type="number" class="form-control" id="monto_edit" name="monto" step="0.01" min="0" value="{{ $pago->monto }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="medio_pago_edit" class="form-label">Medio de pago</label>
                                <select class="form-select" id="medio_pago_edit" name="medio_pago" required>
                                    <option value="Efectivo" {{ $pago->medio_pago === 'Efectivo' ? 'selected' : '' }}>Efectivo</option>
                                    <option value="Transferencia" {{ $pago->medio_pago === 'Transferencia' ? 'selected' : '' }}>Transferencia</option>
                                    <option value="Tarjeta" {{ $pago->medio_pago === 'Tarjeta' ? 'selected' : '' }}>Tarjeta de crédito/débito</option>
                                    <option value="Yape" {{ $pago->medio_pago === 'Yape' ? 'selected' : '' }}>Yape</option>
                                    <option value="Plin" {{ $pago->medio_pago === 'Plin' ? 'selected' : '' }}>Plin</option>
                                    <option value="Otro" {{ $pago->medio_pago === 'Otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                            </div>
                            
                            <div class="col-md-12">
                                <label for="comprobante_edit" class="form-label">Comprobante de pago</label>
                                <input type="file" class="form-control" id="comprobante_edit" name="comprobante" accept="image/*,.pdf">
                                <div class="form-text">Formatos permitidos: imágenes y PDF. Máx. 2MB</div>
                                @if($pago->comprobante)
                                <div class="d-flex align-items-center mt-2">
                                    <div class="form-check me-2">
                                        <input class="form-check-input" type="checkbox" id="mantener_comprobante{{ $pago->id }}" name="mantener_comprobante" value="1" checked>
                                        <label class="form-check-label" for="mantener_comprobante{{ $pago->id }}">Mantener comprobante actual</label>
                                    </div>
                                    <a href="{{ asset('storage/'.$pago->comprobante) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-file-image me-1"></i> Ver
                                    </a>
                                </div>
                                @endif
                            </div>
                            
                            <div class="col-md-12">
                                <label for="observaciones_edit" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observaciones_edit" name="observaciones" rows="2">{{ $pago->observaciones }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar Pago</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal para eliminar pagos -->
    <div class="modal fade" id="modalEliminarPago{{ $pago->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <form action="{{ route('admin.ventas.cotizaciones.pagos.destroy', [$cotizacion, $pago]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar Eliminación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                        <p>¿Está seguro de eliminar este pago?</p>
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

<script>
    $(document).ready(function() {
        // Cambiar el símbolo de moneda al seleccionar otra moneda (formulario nuevo)
        $('#moneda').change(function() {
            let simbolo = $(this).val() === 'Soles' ? 'S/ ' : 'US$ ';
            $('#simbolo-moneda').text(simbolo);
        });
        
        // Cambiar el símbolo de moneda al seleccionar otra moneda (formularios de edición)
        $('.moneda-select').change(function() {
            let pagoId = $(this).data('pago-id');
            let simbolo = $(this).val() === 'Soles' ? 'S/ ' : 'US$ ';
            $('#simbolo-moneda-edit' + pagoId).text(simbolo);
        });
    });
</script>