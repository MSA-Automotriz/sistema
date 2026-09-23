@extends('admin.layouts.app')

@section('title', 'Órdenes de Compra')

@section('content')
<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-shopping-cart text-info me-2"></i> Compras
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6 text-shadow-sm d-flex align-items-center">
                    Órdenes de Compra
                </h2>
                <p class="text-white-50 mb-0">Total registradas: {{ $ordenes->total() }} órdenes de compra.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
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

                <!-- Filtros -->
                <div class="card mb-4 border-0 bg-light" :class="darkMode ? 'bg-dark-subtle text-light' : ''">
                    <div class="card-body p-3">
                        <h6 class="fw-semibold mb-3 small" :class="darkMode ? 'text-light' : 'text-dark'">
                            Filtros de búsqueda
                        </h6>
                        <form method="GET" action="{{ route('admin.compras.ordenes.index') }}">
                            <div class="row g-3">
                                <div class="col-md-6 col-lg-3">
                                    <label for="estado" class="form-label small text-muted mb-1">Estado</label>
                                    <select id="estado" name="estado" class="form-select form-select-sm" :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''">
                                        <option value="">Todos</option>
                                        <option value="en espera" {{ request('estado') == 'en espera' ? 'selected' : '' }}>En Espera</option>
                                        <option value="aprobada" {{ request('estado') == 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                                        <option value="rechazada" {{ request('estado') == 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <label for="proveedor_id" class="form-label small text-muted mb-1">Proveedor</label>
                                    <select id="proveedor_id" name="proveedor_id" class="form-select form-select-sm" :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''">
                                        <option value="">Todos</option>
                                        @foreach ($proveedores as $proveedor)
                                            <option value="{{ $proveedor->id }}" {{ request('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                                                {{ $proveedor->razon_social }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary btn-sm w-100 d-flex align-items-center justify-content-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="me-2" style="height: 0.875rem; width: 0.875rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                        </svg>
                                        Filtrar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Mensaje de éxito/error -->
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

                <!-- Tabla de órdenes -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle" :class="darkMode ? 'table-dark' : ''">
                        <thead class="table-light" :class="darkMode ? 'table-dark' : ''">
                            <tr>
                                <th scope="col" class="text-uppercase small">Nro.</th>
                                <th scope="col" class="text-uppercase small">Código</th>
                                <th scope="col" class="text-uppercase small">Requerimiento</th>
                                <th scope="col" class="text-uppercase small">Fecha</th>
                                <th scope="col" class="text-uppercase small">Proveedor</th>
                                <th scope="col" class="text-uppercase small">Almacén Destino</th>
                                <th scope="col" class="text-uppercase small">Total</th>
                                <th scope="col" class="text-uppercase small">Estado</th>
                                <th scope="col" class="text-uppercase small text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ordenes as $index => $orden)
                                <tr>
                                    <td>{{ $ordenes->firstItem() + $index }}</td>
                                    <td>{{ $orden->codigo }}</td>
                                    <td>{{ $orden->requerimiento->id ?? 'N/A' }}</td>
                                    <td>{{ $orden->created_at->format('d/m/Y') }}</td>
                                    <td>{{ $orden->proveedor->razon_social ?? 'Sin proveedor' }}</td>
                                    <td>{{ $orden->almacen->nombre ?? 'N/A' }}</td>
                                    <td>{{ $orden->moneda }} {{ number_format($orden->total, 2) }}</td>
                                    <td>
                                        @if($orden->estado == 'en espera')
                                            <span class="badge bg-warning text-dark">En Espera</span>
                                        @elseif($orden->estado == 'aprobada')
                                            <span class="badge bg-success">Aprobada</span>
                                        @elseif($orden->estado == 'rechazada')
                                            <span class="badge bg-danger">Rechazada</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.compras.ordenes.show', $orden) }}" class="btn btn-outline-primary" title="Ver">
                                                <svg xmlns="http://www.w3.org/2000/svg" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            @if($orden->estado == 'en espera')
                                                <a href="{{ route('admin.compras.ordenes.edit', $orden) }}" class="btn btn-outline-warning" title="Editar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <form action="{{ route('admin.compras.ordenes.destroy', $orden) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="return confirm('¿Estás seguro de eliminar esta orden?')" class="btn btn-outline-danger" title="Eliminar">
                                                        <svg xmlns="http://www.w3.org/2000/svg" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-5 text-center">
                                        <div class="d-flex flex-column align-items-center justify-content-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" style="height: 3rem; width: 3rem;" class="text-muted mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="text-muted mb-2">No hay órdenes de compra que coincidan con los filtros</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="mt-4">
                    {{ $ordenes->links() }}
                </div>
            </div>
        </div>
</div>
</div>
</div>
@endsection