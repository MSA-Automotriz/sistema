@extends('admin.layouts.app')

@section('title', 'Estándares de Mantenimiento')

@section('header', 'Gestión de Estándares de Mantenimiento')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Panel principal -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <!-- Header con botón de acción -->
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
                    <div class="mb-3 mb-sm-0">
                        <h2 class="h4 fw-bold mb-1" :class="darkMode ? 'text-light' : 'text-dark'">
                            Total de Estándares: {{ $totalEstandares }}
                        </h2>
                        <p class="text-muted small mb-0">Gestiona los estándares de mantenimiento desde aquí</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.configuracion.maestros.estandar_mantenimiento.create') }}" class="btn btn-primary d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="me-2" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Agregar Estándar
                        </a>
                    </div>
                </div>

                <!-- Mensaje de éxito (si existe) -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Tabla de estándares -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle" :class="darkMode ? 'table-dark' : ''">
                        <thead class="table-light" :class="darkMode ? 'table-dark' : ''">
                            <tr>
                                <th scope="col" class="text-uppercase small">#</th>
                                <th scope="col" class="text-uppercase small">Estándar de Mantenimiento</th>
                                <th scope="col" class="text-uppercase small text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($estandares as $index => $estandar)
                                <tr>
                                    <td>{{ $estandares->firstItem() + $index }}</td>
                                    <td class="fw-medium">{{ $estandar->estandar_mantenimiento }}</td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.configuracion.maestros.estandar_mantenimiento.edit', $estandar) }}" class="btn btn-outline-warning" title="Editar">
                                                <svg xmlns="http://www.w3.org/2000/svg" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('admin.configuracion.maestros.estandar_mantenimiento.destroy', $estandar) }}" method="POST" class="d-inline">
                                                @csrf 
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('¿Estás seguro de eliminar este estándar?')" class="btn btn-outline-danger" title="Eliminar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-5 text-center">
                                        <div class="d-flex flex-column align-items-center justify-content-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" style="height: 3rem; width: 3rem;" class="text-muted mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="text-muted mb-2">No hay estándares de mantenimiento registrados</p>
                                            <a href="{{ route('admin.configuracion.maestros.estandar_mantenimiento.create') }}" class="btn btn-sm btn-link text-decoration-none">
                                                Agregar un nuevo estándar
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="mt-4">
                    {{ $estandares->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection