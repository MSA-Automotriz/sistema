@extends('admin.layouts.app')

@section('title', 'Categorías de Clientes')

@section('content')
<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-tags text-info me-2"></i> Gestión de Clientes
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6 text-shadow-sm d-flex align-items-center">
                    Categorías de Clientes
                </h2>
                <p class="text-white-50 mb-0">Total registradas: {{ $categorias->total() }} categorías.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.clientes.categorias.create') }}" class="btn bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0">
                    <i class="fas fa-plus text-primary me-2"></i> Nueva Categoría
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-lg-4 position-relative" style="top: -3.5rem; z-index: 10;">
<div class="row">
    <div class="col-12">
        <!-- Panel principal -->
        <div class="card dashboard-card border-0 shadow-sm mb-4">
            <div class="card-body p-4">

                <!-- Mensaje de éxito o error -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Tabla de categorías o mensaje si está vacía -->
                @if ($categorias->isEmpty())
                    <div class="py-5 text-center">
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <svg xmlns="http://www.w3.org/2000/svg" style="height: 3rem; width: 3rem;" class="text-muted mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-muted mb-2">No hay categorías registradas</p>
                            <a href="{{ route('admin.clientes.categorias.create') }}" class="btn btn-sm btn-link text-decoration-none">
                                Agregar una nueva categoría
                            </a>
                        </div>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" :class="darkMode ? 'table-dark' : ''">
                            <thead class="table-light" :class="darkMode ? 'table-dark' : ''">
                                <tr>
                                    <th scope="col" class="text-uppercase small">ID</th>
                                    <th scope="col" class="text-uppercase small">Nombre</th>
                                    <th scope="col" class="text-uppercase small">Clientes Asociados</th>
                                    <th scope="col" class="text-uppercase small text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categorias as $categoria)
                                    <tr>
                                        <td>{{ $categoria->id }}</td>
                                        <td class="fw-medium">{{ $categoria->nombre }}</td>
                                        <td>{{ $categoria->clientes->count() }}</td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.clientes.categorias.edit', $categoria) }}" class="btn btn-outline-warning" title="Editar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <form action="{{ route('admin.clientes.categorias.destroy', $categoria) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="return confirm('¿Estás seguro de eliminar esta categoría?')" class="btn btn-outline-danger" title="Eliminar">
                                                        <svg xmlns="http://www.w3.org/2000/svg" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- Paginación -->
                    <div class="mt-4">
                        {{ $categorias->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <!-- No se necesitan scripts adicionales específicos aquí -->
@endpush