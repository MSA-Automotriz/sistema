@extends('admin.layouts.app')

@section('title', 'Reporte Kardex Vehículos')

@section('header', 'Reporte Kardex Vehículos')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3>Reporte Kardex Vehículos</h3>
                <div>
                    <button class="btn btn-primary" onclick="window.print()">Imprimir</button>
                    <a href="{{ route('admin.inventario.kardex.form') }}" class="btn btn-secondary">Volver</a>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Información del vehículo -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h4>Información del Vehículo</h4>
                    <table class="table table-sm">
                        <tr>
                            <th>Marca:</th>
                            <td>{{ $vehiculo->marca->nombre ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Modelo:</th>
                            <td>{{ $vehiculo->modelo->nombre ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Versión:</th>
                            <td>{{ $vehiculo->version->nombre ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Año Modelo:</th>
                            <td>{{ $vehiculo->anioModelo->nombre ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h4>Stock Actual</h4>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Almacén</th>
                                <th>Disponible</th>
                                <th>Reservado</th>
                                <th>Stock Mínimo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stockActual as $stock)
                            <tr>
                                <td>{{ $stock->almacen->nombre }}</td>
                                <td class="text-right">{{ number_format($stock->stock_disponible, 2) }}</td>
                                <td class="text-right">{{ number_format($stock->stock_reservado, 2) }}</td>
                                <td class="text-right">{{ number_format($stock->stock_minimo, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Tabla de movimientos -->
            <h4>Movimientos {{ $almacen ? 'en '.$almacen->nombre : 'en todos los almacenes' }}</h4>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Documento</th>
                            <th>Almacén</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th>Existencia</th>
                            <th>Costo Unit.</th>
                            <th>Valor</th>
                            <th>Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($movimientos as $movimiento)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($movimiento->fecha_movimiento)->format('d/m/Y H:i') }}</td>
                            <td>{{ $movimiento->tipoMovimiento->nombre }}</td>
                            <td>{{ $movimiento->documento_referencia }}</td>
                            <td>{{ $movimiento->almacen->nombre }}</td>
                            <td class="text-right">
                                {{ $movimiento->tipoMovimiento->afecta_stock > 0 ? number_format($movimiento->cantidad, 2) : '' }}
                            </td>
                            <td class="text-right">
                                {{ $movimiento->tipoMovimiento->afecta_stock < 0 ? number_format($movimiento->cantidad, 2) : '' }}
                            </td>
                            <td class="text-right">{{ number_format($movimiento->stock_resultante, 2) }}</td>
                            <td class="text-right">{{ number_format($movimiento->costo_unitario, 2) }}</td>
                            <td class="text-right">{{ number_format($movimiento->cantidad * $movimiento->costo_unitario, 2) }}</td>
                            <td>{{ $movimiento->usuario->name }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection