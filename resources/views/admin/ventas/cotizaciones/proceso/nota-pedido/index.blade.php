<div class="card border shadow-none">
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-semibold">
                <i class="fas fa-clipboard-list me-2 text-primary"></i> Nota de Pedido
            </h5>
            <div>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAgregarItems">
                    <i class="fas fa-plus me-1"></i> Agregar Items
                </button>
                
                @if(isset($cotizacion->nota_pedido) && $cotizacion->nota_pedido)
                <a href="{{ route('admin.ventas.cotizaciones.nota-pedido.pdf', $cotizacion) }}" target="_blank" class="btn btn-outline-danger btn-sm ms-2">
                    <i class="fas fa-file-pdf me-1"></i> Generar PDF
                </a>
                @endif
            </div>
        </div>
    </div>
    <div class="card-body">
        @if(isset($cotizacion->nota_pedido) && $cotizacion->nota_pedido)
            <div class="mb-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="fw-semibold mb-1">Nota de Pedido #{{ $cotizacion->nota_pedido->codigo }}</h6>
                        <p class="text-muted mb-0">Fecha: {{ $cotizacion->nota_pedido->created_at->format('d/m/Y') }}</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <span class="badge {{ $cotizacion->nota_pedido->estado === 'Pendiente' ? 'bg-warning' : ($cotizacion->nota_pedido->estado === 'Completada' ? 'bg-success' : 'bg-info') }} p-2">
                            {{ $cotizacion->nota_pedido->estado }}
                        </span>
                    </div>
                </div>
            </div>
        @endif
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="80">#</th>
                        <th>Descripción</th>
                        <th width="100">Tipo</th>
                        <th width="100">Cantidad</th>
                        <th width="130">Precio Unit.</th>
                        <th width="130">Subtotal</th>
                        <th width="100">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($cotizacion->nota_pedido) && $cotizacion->nota_pedido && $cotizacion->nota_pedido->items->count() > 0)
                        @foreach($cotizacion->nota_pedido->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-medium">{{ $item->descripcion }}</div>
                                @if($item->detalles)
                                <small class="text-muted">{{ $item->detalles }}</small>
                                @endif
                            </td>
                            <td><span class="badge bg-light text-dark">{{ $item->tipo }}</span></td>
                            <td class="text-center">{{ $item->cantidad }}</td>
                            <td class="text-end">
                                {{ $cotizacion->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}
                                {{ number_format($item->precio_unitario, 2) }}
                            </td>
                            <td class="text-end fw-medium">
                                {{ $cotizacion->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}
                                {{ number_format($item->cantidad * $item->precio_unitario, 2) }}
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditarItem{{ $item->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEliminarItem{{ $item->id }}">
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
                                        width="80" height="80" alt="No hay items" class="opacity-50 mb-3">
                                    <p class="text-muted mb-0">No hay items en la nota de pedido</p>
                                    <p class="text-muted small">Haga clic en "Agregar Items" para comenzar</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
                @if(isset($cotizacion->nota_pedido) && $cotizacion->nota_pedido && $cotizacion->nota_pedido->items->count() > 0)
                <tfoot class="table-light">
                    <tr>
                        <td colspan="5" class="text-end fw-medium">Subtotal:</td>
                        <td class="text-end fw-medium">
                            {{ $cotizacion->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}
                            {{ number_format($cotizacion->nota_pedido->items->sum(function($item) { return $item->cantidad * $item->precio_unitario; }), 2) }}
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-end fw-medium">IGV (18%):</td>
                        <td class="text-end fw-medium">
                            {{ $cotizacion->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}
                            {{ number_format($cotizacion->nota_pedido->items->sum(function($item) { return $item->cantidad * $item->precio_unitario; }) * 0.18, 2) }}
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-end fw-bold">Total:</td>
                        <td class="text-end fw-bold">
                            {{ $cotizacion->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}
                            {{ number_format($cotizacion->nota_pedido->items->sum(function($item) { return $item->cantidad * $item->precio_unitario; }) * 1.18, 2) }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        
        @if(isset($cotizacion->nota_pedido) && $cotizacion->nota_pedido)
        <div class="mt-4">
            <h6 class="fw-semibold mb-3">Observaciones</h6>
            <form action="{{ route('admin.ventas.cotizaciones.nota-pedido.update-observaciones', $cotizacion) }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-12">
                        <textarea class="form-control" name="observaciones" rows="3" placeholder="Observaciones adicionales para la nota de pedido...">{{ $cotizacion->nota_pedido->observaciones }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="estado_nota_pedido" class="form-label">Estado de la nota de pedido</label>
                        <select class="form-select" id="estado_nota_pedido" name="estado">
                            <option value="Pendiente" {{ $cotizacion->nota_pedido->estado === 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="En proceso" {{ $cotizacion->nota_pedido->estado === 'En proceso' ? 'selected' : '' }}>En proceso</option>
                            <option value="Completada" {{ $cotizacion->nota_pedido->estado === 'Completada' ? 'selected' : '' }}>Completada</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Actualizar Nota de Pedido
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @endif
    </div>
</div>

<!-- Modales para editar items (se generan dinámicamente) -->
@if(isset($cotizacion->nota_pedido) && $cotizacion->nota_pedido && $cotizacion->nota_pedido->items->count() > 0)
    @foreach($cotizacion->nota_pedido->items as $item)
    <div class="modal fade" id="modalEditarItem{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.ventas.cotizaciones.nota-pedido.update-item', [$cotizacion, $item]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="descripcion_edit{{ $item->id }}" class="form-label">Descripción</label>
                                <input type="text" class="form-control" id="descripcion_edit{{ $item->id }}" name="descripcion" value="{{ $item->descripcion }}" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="cantidad_edit{{ $item->id }}" class="form-label">Cantidad</label>
                                <input type="number" class="form-control" id="cantidad_edit{{ $item->id }}" name="cantidad" min="1" value="{{ $item->cantidad }}" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="precio_edit{{ $item->id }}" class="form-label">Precio Unitario</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ $cotizacion->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}</span>
                                    <input type="number" class="form-control" id="precio_edit{{ $item->id }}" name="precio_unitario" step="0.01" min="0" value="{{ $item->precio_unitario }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <label for="detalles_edit{{ $item->id }}" class="form-label">Detalles adicionales</label>
                                <textarea class="form-control" id="detalles_edit{{ $item->id }}" name="detalles" rows="2">{{ $item->detalles }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal para eliminar items -->
    <div class="modal fade" id="modalEliminarItem{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <form action="{{ route('admin.ventas.cotizaciones.nota-pedido.destroy-item', [$cotizacion, $item]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar Eliminación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                        <p>¿Está seguro de eliminar este item?</p>
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

<!-- Modal para agregar items -->
<div class="modal fade" id="modalAgregarItems" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Items a la Nota de Pedido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3" id="itemTypeTabs" role="tablist">
                    <!--li class="nav-item" role="presentation">
                        <button class="nav-link active" id="productos-tab" data-bs-toggle="tab" data-bs-target="#productos" 
                            type="button" role="tab">Vehículos</button>
                    </li-->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="servicios-tab" data-bs-toggle="tab" data-bs-target="#servicios" 
                            type="button" role="tab">Servicios</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="partes-tab" data-bs-toggle="tab" data-bs-target="#partes" 
                            type="button" role="tab">Partes/Repuestos</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="otros-tab" data-bs-toggle="tab" data-bs-target="#otros" 
                            type="button" role="tab">Otros Items</button>
                    </li>
                </ul>
                
                <div class="tab-content" id="itemTypeTabsContent">

                    <!-- Pestaña de Servicios -->
                    <div class="tab-pane fade" id="servicios" role="tabpanel">
                        <form action="{{ route('admin.ventas.cotizaciones.nota-pedido.store-item', $cotizacion) }}" method="POST">
                            @csrf
                            <input type="hidden" name="tipo" value="servicio">
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="servicio_id" class="form-label">Seleccionar Servicio</label>
                                    <select class="form-select" id="servicio_id" name="servicio_id" required>
                                        <option value="">Seleccione un servicio</option>
                                        @foreach(\App\Models\Servicio::with('categoria')->get() as $servicio)
                                            <option value="{{ $servicio->id }}" data-precio="{{ $servicio->precio ?? 0 }}">
                                                {{ $servicio->nombre }} - {{ $servicio->categoria->nombre ?? 'Sin categoría' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-3">
                                    <label for="cantidad_servicio" class="form-label">Cantidad</label>
                                    <input type="number" class="form-control" id="cantidad_servicio" name="cantidad" min="1" value="1" required>
                                </div>
                                
                                <div class="col-md-3">
                                    <label for="precio_servicio" class="form-label">Precio Unitario</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ $cotizacion->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}</span>
                                        <input type="number" class="form-control" id="precio_servicio" name="precio_unitario" step="0.01" min="0" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <label for="detalles_servicio" class="form-label">Detalles adicionales</label>
                                    <textarea class="form-control" id="detalles_servicio" name="detalles" rows="2"></textarea>
                                </div>
                                
                                <div class="col-md-12 text-end mt-4">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">Agregar a la Nota de Pedido</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Pestaña de Partes/Repuestos -->
                    <div class="tab-pane fade" id="partes" role="tabpanel">
                        <form action="{{ route('admin.ventas.cotizaciones.nota-pedido.store-item', $cotizacion) }}" method="POST">
                            @csrf
                            <input type="hidden" name="tipo" value="parte">
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="parte_id" class="form-label">Seleccionar Parte/Repuesto</label>
                                    <select class="form-select" id="parte_id" name="parte_id" required>
                                        <option value="">Seleccione una parte o repuesto</option>
                                        @foreach(\App\Models\Parte::with(['categoriaParte', 'fabricante'])->get() as $parte)
                                            <option value="{{ $parte->id }}" data-precio="{{ $parte->moneda_venta === 'Soles' ? $parte->precio_venta : $parte->precio_venta * 3.8 }}">
                                                {{ $parte->codigo }} - {{ $parte->nombre }} ({{ $parte->fabricante->nombre ?? 'Sin fabricante' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-3">
                                    <label for="cantidad_parte" class="form-label">Cantidad</label>
                                    <input type="number" class="form-control" id="cantidad_parte" name="cantidad" min="1" value="1" required>
                                </div>
                                
                                <div class="col-md-3">
                                    <label for="precio_parte" class="form-label">Precio Unitario</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ $cotizacion->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}</span>
                                        <input type="number" class="form-control" id="precio_parte" name="precio_unitario" step="0.01" min="0" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <label for="detalles_parte" class="form-label">Detalles adicionales</label>
                                    <textarea class="form-control" id="detalles_parte" name="detalles" rows="2"></textarea>
                                </div>
                                
                                <div class="col-md-12 text-end mt-4">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">Agregar a la Nota de Pedido</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Pestaña de Otros Items -->
                    <div class="tab-pane fade" id="otros" role="tabpanel">
                        <form action="{{ route('admin.ventas.cotizaciones.nota-pedido.store-item', $cotizacion) }}" method="POST">
                            @csrf
                            <input type="hidden" name="tipo" value="otro">
                            
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="descripcion_otro" class="form-label">Descripción del Item</label>
                                    <input type="text" class="form-control" id="descripcion_otro" name="descripcion" required>
                                </div>
                                
                                <div class="col-md-3">
                                    <label for="cantidad_otro" class="form-label">Cantidad</label>
                                    <input type="number" class="form-control" id="cantidad_otro" name="cantidad" min="1" value="1" required>
                                </div>
                                
                                <div class="col-md-3">
                                    <label for="precio_otro" class="form-label">Precio Unitario</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ $cotizacion->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}</span>
                                        <input type="number" class="form-control" id="precio_otro" name="precio_unitario" step="0.01" min="0" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="subtipo_otro" class="form-label">Subtipo</label>
                                    <select class="form-select" id="subtipo_otro" name="subtipo">
                                        <option value="Regalo">Regalo/Obsequio</option>
                                        <option value="Accesorio">Accesorio</option>
                                        <option value="Consumible">Consumible</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-12">
                                    <label for="detalles_otro" class="form-label">Detalles adicionales</label>
                                    <textarea class="form-control" id="detalles_otro" name="detalles" rows="2"></textarea>
                                </div>
                                
                                <div class="col-md-12 text-end mt-4">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">Agregar a la Nota de Pedido</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Actualizar precio al seleccionar un vehículo
    document.getElementById('vehiculo_id')?.addEventListener('change', function() {
        const precio = this.options[this.selectedIndex].getAttribute('data-precio');
        document.getElementById('precio_vehiculo').value = precio || '';
    });
    
    // Actualizar precio al seleccionar un servicio
    document.getElementById('servicio_id')?.addEventListener('change', function() {
        const precio = this.options[this.selectedIndex].getAttribute('data-precio');
        document.getElementById('precio_servicio').value = precio || '';
    });
    
    // Actualizar precio al seleccionar una parte
    document.getElementById('parte_id')?.addEventListener('change', function() {
        const precio = this.options[this.selectedIndex].getAttribute('data-precio');
        document.getElementById('precio_parte').value = precio || '';
    });
});
</script>