@extends('admin.layouts.app')

@section('title', 'Categorías de Proveedor')

@section('content')
<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-tags text-info me-2"></i> Adquisiciones
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6 text-shadow-sm d-flex align-items-center">
                    Categorías de Proveedor
                </h2>
                <p class="text-white-50 mb-0">Total registradas: {{ $categorias->total() }} categorías.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.compras.proveedores.categorias.create') }}" class="btn bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0">
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
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if($categorias->count())
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nombre de la Categoría</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categorias as $categoria)
                                    <tr>
                                        <td>{{ $categoria->id }}</td>
                                        <td>{{ $categoria->nombre_categoria_proveedor }}</td>
                                        <td>
                                            <a href="{{ route('admin.compras.proveedores.categorias.edit', $categoria) }}" class="btn btn-outline-secondary btn-sm">
                                                Editar
                                            </a>
                                            <form action="{{ route('admin.compras.proveedores.categorias.destroy', $categoria) }}" method="POST" class="d-inline-block" onsubmit="return confirm('¿Está seguro de eliminar esta categoría?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $categorias->links() }}
                @else
                    <p class="text-center">No se encontraron categorías de proveedor.</p>
                @endif
            </div>
        </div>
    </div>
</div>
</div>
@endsection
