@extends('admin.layouts.app')

@section('title', 'Devoluciones a Proveedores')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Devoluciones a Proveedores</h5>
                    <a href="{{ route('admin.inventario.devoluciones.create') }}" class="btn btn-sm btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                            <path d="M8 0a1 1 0 0 1 1 1v6h6a1 1 0 1 1 0 2H9v6a1 1 0 1 1-2 0V9H1a1 1 0 0 1 0-2h6V1a1 1 0 0 1 1-1z"/>
                        </svg>
                        Nueva Devolución
                    </a>
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

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Proveedor</th>
                                    <th>Fecha Emisión</th>
                                    <th>Motivo</th>
                                    <th>Almacén</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($devoluciones as $devolucion)
                                    <tr>
                                        <td>{{ $devolucion->codigo }}</td>
                                        <td>{{ $devolucion->proveedor->nombre_completo }}</td>
                                        <td>{{ $devolucion->fecha_emision ? $devolucion->fecha_emision->format('d/m/Y') : '-' }}</td>
                                        <td>{{ $devolucion->motivo }}</td>
                                        <td>{{ $devolucion->almacen->nombre }}</td>
                                        <td>
                                            @if($devolucion->estado == 'PENDIENTE')
                                                <span class="badge bg-warning">Pendiente</span>
                                            @elseif($devolucion->estado == 'PROCESADA')
                                                <span class="badge bg-success">Procesada</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $devolucion->estado }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group" aria-label="Acciones">
                                                <!-- Ver Detalles -->
                                                <a href="{{ route('admin.inventario.devoluciones.show', $devolucion->id) }}" 
                                                   class="btn btn-outline-info btn-sm" 
                                                   title="Ver Detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                @if($devolucion->estado == 'PENDIENTE')
                                                    <!-- Editar -->
                                                    <a href="{{ route('admin.inventario.devoluciones.edit', $devolucion->id) }}" 
                                                       class="btn btn-outline-warning btn-sm" 
                                                       title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <!-- Confirmar Devolución -->
                                                    <form action="{{ route('admin.inventario.devoluciones.confirmar', $devolucion->id) }}" 
                                                          method="POST" 
                                                          class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" 
                                                                class="btn btn-outline-success btn-sm" 
                                                                title="Confirmar Devolución"
                                                                onclick="return confirm('¿Estás seguro de confirmar esta devolución? Esta acción no se puede deshacer.')">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <!-- Eliminar -->
                                                    <form action="{{ route('admin.inventario.devoluciones.destroy', $devolucion->id) }}" 
                                                          method="POST" 
                                                          class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-outline-danger btn-sm" 
                                                                title="Eliminar"
                                                                onclick="return confirm('¿Estás seguro de eliminar esta devolución?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No hay devoluciones registradas</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-3">
                        {{ $devoluciones->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Estilos para los botones de acción */
.btn-group .btn {
    border-radius: 0.375rem;
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.btn-group .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
}

/* Colores específicos para cada acción */
.btn-outline-info:hover {
    background-color: #0dcaf0;
    border-color: #0dcaf0;
}

.btn-outline-warning:hover {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}

.btn-outline-success:hover {
    background-color: #198754;
    border-color: #198754;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
}
</style>
@endpush