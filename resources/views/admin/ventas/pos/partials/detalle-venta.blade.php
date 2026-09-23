{{-- resources/views/admin/ventas/pos/partials/detalle-venta.blade.php --}}
<div class="row">
    <!-- Información General -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle mr-1"></i> Información General</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="40%"><strong>Código:</strong></td>
                        <td>{{ $venta->codigo }}</td>
                    </tr>
                    <tr>
                        <td><strong>Fecha:</strong></td>
                        <td>{{ $venta->fecha->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Estado:</strong></td>
                        <td>
                            <span class="badge {{ $venta->estado == 'Completada' ? 'bg-success' : ($venta->estado == 'Parcial' ? 'bg-warning' : 'bg-info') }}">
                                {{ $venta->estado }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Moneda:</strong></td>
                        <td>{{ $venta->moneda }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tipo de Pago:</strong></td>
                        <td>{{ $venta->tipo_pago }}</td>
                    </tr>
                    @if($venta->cotizacion)
                    <tr>
                        <td><strong>Cotización:</strong></td>
                        <td>
                            <a href="{{ route('admin.ventas.cotizaciones.show', $venta->cotizacion->id) }}" target="_blank">
                                {{ $venta->cotizacion->codigo }}
                                <i class="fas fa-external-link-alt ml-1"></i>
                            </a>
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Información del Cliente -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-user mr-1"></i> Cliente</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="40%"><strong>Nombre:</strong></td>
                        <td>{{ $ventaData['cliente_nombre'] }}</td>
                    </tr>
                    <tr>
                        <td><strong>Documento:</strong></td>
                        <td>{{ $ventaData['cliente_documento'] }}</td>
                    </tr>
                    <tr>
                        <td><strong>Usuario:</strong></td>
                        <td>{{ $ventaData['usuario_nombre'] }}</td>
                    </tr>
                    <tr>
                        <td><strong>Almacén:</strong></td>
                        <td>{{ $ventaData['almacen_nombre'] }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Totales -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-calculator mr-1"></i> Totales</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h5>{{ $venta->moneda == 'Dólares' ? '$' : 'S/.' }} {{ number_format($venta->subtotal, 2) }}</h5>
                            <small class="text-muted">Subtotal</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h5>{{ $venta->moneda == 'Dólares' ? '$' : 'S/.' }} {{ number_format($venta->igv, 2) }}</h5>
                            <small class="text-muted">IGV (18%)</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h5 class="text-primary">{{ $venta->moneda == 'Dólares' ? '$' : 'S/.' }} {{ number_format($venta->total, 2) }}</h5>
                            <small class="text-muted">Total</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h5 class="text-success">{{ $venta->moneda == 'Dólares' ? '$' : 'S/.' }} {{ number_format($venta->monto_abonado, 2) }}</h5>
                            <small class="text-muted">Abonado ({{ number_format($ventaData['porcentaje_abonado'], 1) }}%)</small>
                        </div>
                    </div>
                </div>
                
                @if($venta->saldo_pendiente > 0)
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="alert alert-warning text-center mb-0">
                            <strong>Saldo Pendiente: {{ $venta->moneda == 'Dólares' ? '$' : 'S/.' }} {{ number_format($venta->saldo_pendiente, 2) }}</strong>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Detalle de Items -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-list mr-1"></i> Detalle de Items</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Descripción</th>
                                <th>Cantidad</th>
                                <th>Precio Unit.</th>
                                <th>Descuento</th>
                                <th>Subtotal</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ventaData['detalles'] as $detalle)
                            <tr>
                                <td>
                                    <code>{{ $detalle['item_codigo'] }}</code>
                                    <br><small class="text-muted">{{ ucfirst($detalle['tipo_item']) }}</small>
                                </td>
                                <td>
                                    <strong>{{ $detalle['item_nombre'] }}</strong>
                                    @if($detalle['descripcion'] && $detalle['descripcion'] != $detalle['item_nombre'])
                                        <br><small class="text-muted">{{ $detalle['descripcion'] }}</small>
                                    @endif
                                </td>
                                <td class="text-center">{{ number_format($detalle['cantidad'], 2) }}</td>
                                <td class="text-end">{{ $venta->moneda == 'Dólares' ? '$' : 'S/.' }} {{ number_format($detalle['precio_unitario'], 2) }}</td>
                                <td class="text-center">
                                    @if($detalle['descuento'] > 0)
                                        <span class="badge bg-info">{{ number_format($detalle['descuento'], 1) }}%</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-end">{{ $venta->moneda == 'Dólares' ? '$' : 'S/.' }} {{ number_format($detalle['subtotal'], 2) }}</td>
                                <td class="text-end"><strong>{{ $venta->moneda == 'Dólares' ? '$' : 'S/.' }} {{ number_format($detalle['total'], 2) }}</strong></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No hay items registrados</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Observaciones -->
@if($venta->observaciones)
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-comment mr-1"></i> Observaciones</h6>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $venta->observaciones }}</p>
            </div>
        </div>
    </div>
</div>
@endif