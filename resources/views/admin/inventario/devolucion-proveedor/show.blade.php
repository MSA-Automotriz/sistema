@extends('admin.layouts.app')

@section('title', 'Devoluciones a Proveedores')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Detalle de Devolución a Proveedor: {{ $devolucion->codigo }}</h5>
                    <div>
                        @if($devolucion->estado == 'PENDIENTE')
                            <a href="{{ route('admin.inventario.devoluciones.edit', $devolucion->id) }}" class="btn btn-sm btn-primary me-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                    <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                </svg>
                                Editar
                            </a>
                        @endif
                        <a href="{{ route('admin.inventario.devoluciones.index') }}" class="btn btn-sm btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left me-1" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                            </svg>
                            Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3">Información General</h6>
                            <table class="table table-bordered table-sm">
                                <tr>
                                    <th class="bg-light" style="width: 200px;">Código:</th>
                                    <td>{{ $devolucion->codigo }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Proveedor:</th>
                                    <td>{{ $devolucion->proveedor->nombre_completo }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Motivo:</th>
                                    <td>{{ $devolucion->motivo }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Fecha de Emisión:</th>
                                    <td>{{ $devolucion->fecha_emision ? $devolucion->fecha_emision->format('d/m/Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Almacén:</th>
                                    <td>{{ $devolucion->almacen->nombre }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Estado:</th>
                                    <td>
                                        @if($devolucion->estado == 'PENDIENTE')
                                            <span class="badge bg-warning">Pendiente</span>
                                        @elseif($devolucion->estado == 'PROCESADA')
                                            <span class="badge bg-success">Procesada</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $devolucion->estado }}</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3">Información Adicional</h6>
                            <table class="table table-bordered table-sm">
                                <tr>
                                    <th class="bg-light" style="width: 200px;">Creado por:</th>
                                    <td>{{ $devolucion->usuario->name }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Fecha de Creación:</th>
                                    <td>{{ $devolucion->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Última Actualización:</th>
                                    <td>{{ $devolucion->updated_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Observaciones:</th>
                                    <td>{{ $devolucion->observaciones ?? 'Sin observaciones' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <h6 class="border-bottom pb-2 mt-4 mb-3">Productos Devueltos</h6>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>Producto</th>
                                    <th>Código</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                    <th>Motivo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($devolucion->detalles as $index => $detalle)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $detalle->nombre }}</td>
                                    <td>{{ $detalle->codigo }}</td>
                                    <td>
                                        @if($detalle->tipo_item == 'parte')
                                            <span class="badge bg-info">Parte</span>
                                        @elseif($detalle->tipo_item == 'vehiculo')
                                            <span class="badge bg-primary">Vehículo</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $detalle->tipo_item }}</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($detalle->cantidad, 2) }}</td>
                                    <td>{{ $detalle->motivo_detalle ?? 'Sin especificar' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No hay productos en esta devolución</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($devolucion->estado == 'PENDIENTE')
                    <div class="mt-4 text-end">
                        <form action="{{ route('admin.inventario.devoluciones.confirmar', $devolucion->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-success" onclick="return confirm('¿Estás seguro de confirmar esta devolución? Esta acción no se puede deshacer.')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-lg me-1" viewBox="0 0 16 16">
                                    <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/>
                                </svg>
                                Confirmar Devolución
                            </button>
                        </form>
                        <form action="{{ route('admin.inventario.devoluciones.destroy', $devolucion->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger ms-2" onclick="return confirm('¿Estás seguro de eliminar esta devolución?')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash me-1" viewBox="0 0 16 16">
                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                </svg>
                                Eliminar
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection