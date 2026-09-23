@extends('admin.layouts.app')

@section('title', 'Reglas de Vencimiento de Cotizaciones')

@section('header', 'Reglas de Vencimiento de Cotizaciones')

@section('content')
<div class="container-fluid px-3 px-lg-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h1 class="h3 mb-1 text-dark fw-semibold">Reglas de Vencimiento</h1>
                    <p class="text-muted mb-0">
                        <i class="fas fa-clock me-1"></i> Gestión de reglas automáticas de vencimiento para cotizaciones
                    </p>
                </div>
                <div class="mt-3 mt-md-0">
                    <a href="{{ route('admin.configuracion.reglas-vencimiento-cotizaciones.create') }}" 
                       class="btn btn-primary d-flex align-items-center">
                        <i class="fas fa-plus me-2"></i> Nueva Regla
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-semibold">
                    <i class="fas fa-list me-2 text-primary"></i> Lista de Reglas
                </h5>
                <span class="badge bg-primary rounded-pill">
                    {{ $reglas->total() }}
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            @if($reglas->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 ps-3">Nombre</th>
                                <th class="border-0">Vencimiento</th>
                                <th class="border-0">Alerta</th>
                                <th class="border-0">Estado Vencido</th>
                                <th class="border-0 text-center">Reasignación</th>
                                <th class="border-0 text-center">Estado</th>
                                <th class="border-0 text-center pe-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reglas as $regla)
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium">{{ $regla->nombre }}</span>
                                        @if($regla->descripcion)
                                        <small class="text-muted">{{ $regla->descripcion }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info rounded-pill">
                                        {{ $regla->dias_vencimiento }} días
                                    </span>
                                </td>
                                <td>
                                    @if($regla->dias_alerta > 0)
                                        <span class="badge bg-warning rounded-pill">
                                            {{ $regla->dias_alerta }} días
                                        </span>
                                    @else
                                        <span class="text-muted">Sin alerta</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $regla->estadoVencido->color ?? 'secondary' }} rounded-pill">
                                        {{ $regla->estadoVencido->nombre ?? 'No definido' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($regla->permite_reasignacion)
                                        <i class="fas fa-check text-success" title="Permite reasignación"></i>
                                    @else
                                        <i class="fas fa-times text-danger" title="No permite reasignación"></i>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('admin.configuracion.reglas-vencimiento-cotizaciones.toggle-activo', $regla) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm border-0 p-0">
                                            @if($regla->activo)
                                                <span class="badge bg-success rounded-pill">
                                                    <i class="fas fa-check me-1"></i>Activa
                                                </span>
                                            @else
                                                <span class="badge bg-secondary rounded-pill">
                                                    <i class="fas fa-pause me-1"></i>Inactiva
                                                </span>
                                            @endif
                                        </button>
                                    </form>
                                </td>
                                <td class="text-center pe-3">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                type="button" id="dropdownAcciones{{ $regla->id }}"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" 
                                                   href="{{ route('admin.configuracion.reglas-vencimiento-cotizaciones.edit', $regla) }}">
                                                    <i class="fas fa-edit me-2"></i> Editar
                                                </a>
                                            </li>
                                            @if($regla->cotizaciones()->count() == 0)
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('admin.configuracion.reglas-vencimiento-cotizaciones.destroy', $regla) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('¿Estás seguro de eliminar esta regla?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-trash me-2"></i> Eliminar
                                                    </button>
                                                </form>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if($reglas->hasPages())
                <div class="px-3 py-2 border-top">
                    {{ $reglas->links() }}
                </div>
                @endif
            @else
                <div class="d-flex flex-column align-items-center justify-content-center py-5">
                    <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iODAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBkPSJNMTIgMkM2LjUgMiAyIDYuNSAyIDEyUzYuNSAyMiAxMiAyMlMyMiAxNy41IDIyIDEyUzE3LjUgMiAxMiAyWk0xMyAxN0gxMVYxNUgxM1YxN1pNMTMgMTNIMTFWN0gxM1YxM1oiIGZpbGw9IiM5OTkiLz48L3N2Zz4=" 
                         width="80" height="80" alt="Sin reglas" class="opacity-50 mb-3">
                    <h5 class="text-muted">No hay reglas configuradas</h5>
                    <p class="text-muted mb-3">Crea la primera regla de vencimiento para comenzar</p>
                    <a href="{{ route('admin.configuracion.reglas-vencimiento-cotizaciones.create') }}" 
                       class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Crear Primera Regla
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th {
        font-weight: 600;
        color: #6c757d;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .dropdown-toggle::after {
        display: none;
    }
    
    .badge {
        font-size: 0.75rem;
    }
    
    .card {
        border: none !important;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }
</style>
@endpush