@extends('admin.layouts.app')

@section('title', 'Reportes de Inventario')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h3 class="mb-0"><i class="fas fa-boxes me-2"></i>Reportes de Inventario</h3>
                    <a href="{{ route('admin.reportes.index') }}" class="btn btn-sm btn-dark">
                        <i class="fas fa-arrow-left me-1"></i>Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de resumen -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="mb-1">Stock Total</h6><h3 class="mb-0">{{ number_format($stockTotal) }}</h3></div>
                        <i class="fas fa-cubes fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="mb-1">Valor Inventario</h6><h3 class="mb-0">S/. {{ number_format($valorInventario, 2) }}</h3></div>
                        <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="mb-1">Stock Bajo</h6><h3 class="mb-0">{{ $productosStockBajo }}</h3></div>
                        <i class="fas fa-exclamation-triangle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white bg-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="mb-1">Sin Stock</h6><h3 class="mb-0">{{ $productosSinStock }}</h3></div>
                        <i class="fas fa-times-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos con stock bajo -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-circle me-2"></i>Productos con Stock Bajo</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover table-sm">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Almacén</th>
                                    <th class="text-center">Stock</th>
                                    <th class="text-center">Mínimo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($productosConStockBajo as $producto)
                                <tr>
                                    <td><span class="badge bg-secondary">{{ $producto->codigo }}</span></td>
                                    <td>{{ $producto->nombre }}</td>
                                    <td><small class="text-muted">{{ $producto->almacen }}</small></td>
                                    <td class="text-center">
                                        <span class="badge bg-danger">{{ $producto->stock_disponible }}</span>
                                    </td>
                                    <td class="text-center">{{ $producto->stock_minimo }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted">No hay productos con stock bajo</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Movimientos recientes -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>Movimientos Recientes</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover table-sm">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Producto</th>
                                    <th class="text-center">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($movimientosRecientes as $movimiento)
                                <tr>
                                    <td><small>{{ \Carbon\Carbon::parse($movimiento->fecha)->format('d/m H:i') }}</small></td>
                                    <td>
                                        @if(str_contains(strtolower($movimiento->tipo_movimiento), 'entrada'))
                                            <span class="badge bg-success">Entrada</span>
                                        @else
                                            <span class="badge bg-warning">Salida</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small><strong>{{ $movimiento->codigo }}</strong></small><br>
                                        <small class="text-muted">{{ Str::limit($movimiento->nombre, 30) }}</small>
                                    </td>
                                    <td class="text-center"><strong>{{ $movimiento->cantidad }}</strong></td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted">No hay movimientos recientes</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    @if($productosStockBajo > 0)
    <div class="row">
        <div class="col-12">
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                <div>
                    <strong>Atención:</strong> Hay {{ $productosStockBajo }} productos con stock por debajo del mínimo.
                    @if($productosSinStock > 0)
                        De los cuales, {{ $productosSinStock }} productos están completamente sin stock.
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
