@extends('admin.layouts.app')

@section('title', 'Clasificación de Vehículos')

@section('header', 'Clasificación de Vehículos')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
                    <div class="mb-3 mb-sm-0">
                        <h2 class="h4 fw-bold mb-1">Total de Clasificaciones: {{ $clasificaciones->total() }}</h2>
                        <p class="text-muted small mb-0">Gestiona las clasificaciones de vehículos desde aquí</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.configuracion.maestros.clasificacion_vehiculos.create') }}" class="btn btn-primary d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="me-2" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Agregar Clasificación
                        </a>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($clasificaciones as $index => $clasificacion)
                                <tr>
                                    <td>{{ $clasificaciones->firstItem() + $index }}</td>
                                    <td>{{ $clasificacion->nombre }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.configuracion.maestros.clasificacion_vehiculos.edit', $clasificacion) }}" class="btn btn-outline-warning btn-sm">Editar</a>
                                        <form action="{{ route('admin.configuracion.maestros.clasificacion_vehiculos.destroy', $clasificacion) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('¿Estás seguro?')" class="btn btn-outline-danger btn-sm">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No hay clasificaciones registradas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $clasificaciones->links() }}
            </div>
        </div>
    </div>
</div>
@endsection