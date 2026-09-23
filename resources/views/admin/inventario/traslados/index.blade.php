@extends('admin.layouts.app')

@section('title', 'Traslados de Inventario')

@section('header', 'Traslados de Inventario')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h4 fw-bold mb-0" :class="darkMode ? 'text-light' : 'text-dark'">
                        Gestión de Traslados
                    </h2>
                    <a href="{{ route('admin.inventario.traslados.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus me-2"></i> Nuevo Traslado
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Fecha</th>
                                <th scope="col">Almacén Origen</th>
                                <th scope="col">Almacén Destino</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Usuario</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($traslados as $traslado)
                                <tr>
                                    <td>{{ $traslado->id }}</td>
                                    <td>{{ $traslado->fecha_traslado->format('d/m/Y H:i') }}</td>
                                    <td>{{ $traslado->almacenOrigen->nombre }}</td>
                                    <td>{{ $traslado->almacenDestino->nombre }}</td>
                                    <td>
                                        @if($traslado->estado == 'pendiente')
                                            <span class="badge bg-warning">Pendiente</span>
                                        @elseif($traslado->estado == 'completado')
                                            <span class="badge bg-success">Completado</span>
                                        @elseif($traslado->estado == 'cancelado')
                                            <span class="badge bg-danger">Cancelado</span>
                                        @endif
                                    </td>
                                    <td>{{ $traslado->usuario->name }}</td>
                                    <td>
                                        <a href="{{ route('admin.inventario.traslados.show', $traslado) }}" class="btn btn-sm btn-info me-1">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        
                                        @if($traslado->estado == 'pendiente')
                                            <form method="POST" action="{{ route('admin.inventario.traslados.cambiar-estado', $traslado) }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="estado" value="completado">
                                                <button type="submit" class="btn btn-sm btn-success me-1" onclick="return confirm('¿Confirmar traslado como completado?')">
                                                    <i class="fa fa-check"></i>
                                                </button>
                                            </form>
                                            
                                            <form method="POST" action="{{ route('admin.inventario.traslados.cambiar-estado', $traslado) }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="estado" value="cancelado">
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Cancelar este traslado? Esta acción revertirá los movimientos de inventario.')">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No hay traslados registrados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $traslados->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection