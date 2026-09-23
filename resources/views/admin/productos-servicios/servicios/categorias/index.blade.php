@extends('admin.layouts.app')

@section('title', 'Categorías de Servicios Tercerizados')

@section('content')
<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-tags text-info me-2"></i> Gestión de Servicios
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6 text-shadow-sm d-flex align-items-center">
                    Categorías de Servicios
                </h2>
                <p class="text-white-50 mb-0">Total registradas: {{ $totalCategorias }}</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.productos-servicios.servicios.index') }}" class="btn bg-white bg-opacity-10 text-white rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border border-white border-opacity-25 backdrop-blur me-2">
                    <i class="fas fa-list me-2"></i> Ver Servicios
                </a>
                <a href="{{ route('admin.productos-servicios.servicios.categorias.create') }}" class="btn bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0">
                    <i class="fas fa-plus text-primary me-2"></i> Agregar Categoría
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-lg-4 position-relative" style="top: -3.5rem; z-index: 10;">
    <div class="card dashboard-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            @if (session('success'))
                <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-4 border-0 text-uppercase small">#</th>
                            <th class="py-3 px-4 border-0 text-uppercase small">Nombre</th>
                            <th class="py-3 px-4 border-0 text-uppercase small text-center">Fecha Creación</th>
                            <th class="py-3 px-4 border-0 text-uppercase small text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categorias as $index => $categoria)
                            <tr>
                                <td class="px-4 py-3">{{ $categorias->firstItem() + $index }}</td>
                                <td class="px-4 py-3 fw-bold text-primary">{{ $categoria->nombre }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-muted small">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        {{ $categoria->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                                        <a href="{{ route('admin.productos-servicios.servicios.categorias.edit', $categoria) }}" class="btn btn-white btn-sm border-0 px-3 transition hover:bg-warning hover:text-white" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.productos-servicios.servicios.categorias.destroy', $categoria) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('¿Estás seguro de eliminar esta categoría?')" class="btn btn-white btn-sm border-0 px-3 transition hover:bg-danger hover:text-white" title="Eliminar">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-5 text-center">
                                    <div class="bg-light d-inline-flex p-4 rounded-circle mb-3">
                                        <i class="fas fa-tags text-muted fa-3x"></i>
                                    </div>
                                    <h5 class="text-dark fw-bold">No hay categorías registradas</h5>
                                    <p class="text-muted mb-0">Organiza tus servicios tercerizados mediante categorías</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $categorias->links() }}
            </div>
        </div>
    </div>
</div>
@endsection