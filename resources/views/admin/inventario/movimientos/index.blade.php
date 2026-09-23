@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="fw-bold">Movimientos de Almacén</h2>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Listado de Movimientos</h5>
            <a href="{{ route('admin.inventario.movimientos.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nuevo Movimiento
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Tipo</th>
                            <th>Parte</th>
                            <th>Almacén</th>
                            <th>Cantidad</th>
                            <th>Documento</th>
                            <th>Fecha</th>
                            <th>Usuario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($movimientos as $movimiento)
                        <tr>
                            <td>{{ $movimiento->id }}</td>
                            <td>{{ $movimiento->tipoMovimiento->nombre }}</td>
                            <td>{{ $movimiento->parte->nombre }}</td>
                            <td>{{ $movimiento->almacen->nombre }}</td>
                            <td>{{ $movimiento->cantidad }}</td>
                            <td>{{ $movimiento->documento_referencia ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($movimiento->fecha_movimiento)->format('d/m/Y H:i') }}</td>
                            <td>{{ $movimiento->usuario->name }}</td>
                            <td>
                                <a href="{{ route('admin.inventario.movimientos.edit', $movimiento) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.inventario.movimientos.destroy', $movimiento) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este movimiento?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $movimientos->links() }}
            </div>
        </div>
    </div>
</div>
@endsection