@extends('admin.layouts.app')

@section('title', 'Detalle de Vale de Devolución')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Detalle de Vale de Devolución</h5>
                    <div>
                        @if($devolucion->estado === 'pendiente')
                            <a href="{{ route('admin.devoluciones.edit', $devolucion->id) }}" class="btn btn-sm btn-primary me-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                    <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                </svg>
                                Editar
                            </a>
                        @endif
                        <a href="{{ route('admin.devoluciones.index') }}" class="btn btn-sm btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left me-1" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                            </svg>
                            Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        $estado_classes = [
                            'pendiente' => 'bg-warning',
                            'aprobado' => 'bg-success',
                            'rechazado' => 'bg-danger',
                            'procesado' => 'bg-info'
                        ];
                        $estado_class = $estado_classes[$devolucion->estado] ?? 'bg-secondary';
                    @endphp

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3">Información General</h6>
                            <table class="table table-bordered table-sm">
                                <tr>
                                    <th class="bg-light" style="width: 200px;">Número:</th>
                                    <td>{{ $devolucion->numero }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Fecha:</th>
                                    <td>{{ \Carbon\Carbon::parse($devolucion->fecha)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Proveedor:</th>
                                    <td>{{ $devolucion->proveedor->razon_social ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Motivo:</th>
                                    <td>{{ $devolucion->motivo }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Estado:</th>
                                    <td>
                                        <span class="badge {{ $estado_class }}">{{ ucfirst($devolucion->estado) }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3">Información Adicional</h6>
                            <table class="table table-bordered table-sm">
                                <tr>
                                    <th class="bg-light" style="width: 200px;">Creado por:</th>
                                    <td>{{ $devolucion->usuario->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Fecha de Creación:</th>
                                    <td>{{ $devolucion->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Total Items:</th>
                                    <td>{{ $devolucion->detalles->count() }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Observaciones:</th>
                                    <td>{{ $devolucion->observaciones ?: 'Sin observaciones' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <h6 class="border-bottom pb-2 mt-4 mb-3">Productos a Devolver</h6>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>Producto</th>
                                    <th>Código</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Subtotal</th>
                                    <th>Motivo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($devolucion->detalles->count() > 0)
                                    @foreach($devolucion->detalles as $index => $detalle)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $detalle->nombre_producto }}</td>
                                            <td>{{ $detalle->codigo_producto }}</td>
                                            <td>
                                                <span class="badge bg-primary">{{ ucfirst($detalle->tipo_producto) }}</span>
                                            </td>
                                            <td>{{ number_format($detalle->cantidad, 2) }}</td>
                                            <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                                            <td>${{ number_format($detalle->subtotal, 2) }}</td>
                                            <td>{{ $detalle->motivo_detalle ?: '-' }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="bg-light">
                                        <td colspan="6" class="text-end"><strong>TOTAL:</strong></td>
                                        <td><strong>${{ number_format($devolucion->total, 2) }}</strong></td>
                                        <td></td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <i class="fas fa-box-open text-muted fa-2x mb-3"></i>
                                            <h6 class="text-muted">No hay productos en esta devolución</h6>
                                            <p class="text-muted mb-0">Agregue productos para continuar</p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection