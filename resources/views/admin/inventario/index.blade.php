@extends('admin.layouts.app')
@section('title', 'Gestión de Inventario')
@section('content')

<!-- Hero Section -->
<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-boxes text-info me-2"></i> Módulo de Logística
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6">Inventario General</h2>
                <p class="text-white-50 mb-0">Control en tiempo real de vehículos, partes y stock ({{ $inventarios->total() }} registros)</p>
            </div>
            <div>
                <a href="{{ route('admin.inventario.kardex.form') }}" class="btn bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105" style="border: 1px solid rgba(255,255,255,0.8);">
                    <i class="fas fa-file-alt me-2 text-primary"></i> Reporte Kardex
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-lg-4 position-relative" style="top: -3.5rem; z-index: 10;">
    <!-- Panel Filtros -->
    <div class="card dashboard-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-3">
                <i class="fas fa-filter text-primary me-2"></i>
                <h6 class="mb-0 fw-bold text-dark">Filtros de búsqueda</h6>
            </div>
            <form id="filterForm" action="{{ route('admin.inventario.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-3">
                    <select class="form-select bg-light border-light shadow-none" id="tipo" name="tipo" onchange="this.form.submit()">
                        <option value="" {{ !request('tipo') ? 'selected' : '' }}>Todos los Tipos</option>
                        <option value="partes" {{ request('tipo') == 'partes' ? 'selected' : '' }}>Solo Partes / Repuestos</option>
                        <option value="vehiculos" {{ request('tipo') == 'vehiculos' ? 'selected' : '' }}>Solo Vehículos</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select bg-light border-light shadow-none" id="almacen_id" name="almacen_id" onchange="this.form.submit()">
                        <option value="">Todos los Almacenes</option>
                        @foreach($almacenes as $almacen)
                            <option value="{{ $almacen->id }}" {{ request('almacen_id') == $almacen->id ? 'selected' : '' }}>
                                {{ $almacen->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select bg-light border-light shadow-none" id="centro_costo_id" name="centro_costo_id" onchange="this.form.submit()">
                        <option value="">Todos los Centros de Costo</option>
                        @foreach($centrosCostos as $centro)
                            <option value="{{ $centro->id }}" {{ request('centro_costo_id') == $centro->id ? 'selected' : '' }}>
                                {{ $centro->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 border-light text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control bg-light border-start-0 border-light shadow-none" id="search" name="search" 
                               placeholder="Buscar código o nombre..." value="{{ request('search') }}">
                        <button class="btn btn-primary shadow-sm" style="border-radius: 0 0.5rem 0.5rem 0;" type="submit">
                            Buscar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Inventario -->
    <div class="row">
        <div class="col-12">
            <div class="card dashboard-card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-borderless table-hover align-middle mb-0">
                        <thead class="bg-light bg-opacity-75" style="border-bottom: 1px solid #f1f5f9;">
                            <tr>
                                <th class="text-muted small fw-bold text-uppercase px-4 py-3">Item / Artículo</th>
                                <th class="text-muted small fw-bold text-uppercase py-3">Código</th>
                                <th class="text-muted small fw-bold text-uppercase py-3">Tipo</th>
                                <th class="text-muted small fw-bold text-uppercase py-3">Ubicación</th>
                                <th class="text-muted small fw-bold text-uppercase py-3 text-end">Disp.</th>
                                <th class="text-muted small fw-bold text-uppercase py-3 text-end">Rsv.</th>
                                <th class="text-muted small fw-bold text-uppercase py-3 text-end">Min.</th>
                                <th class="text-muted small fw-bold text-uppercase py-3 text-end px-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventarios as $inventario)
                            <tr style="border-bottom: 1px solid #f8fafc;">
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 
                                            {{ isset($inventario->vehiculo_id) ? 'bg-primary bg-opacity-10 text-primary' : 'bg-info bg-opacity-10 text-info' }}" 
                                            style="width: 40px; height: 40px;">
                                            <i class="fas {{ isset($inventario->vehiculo_id) ? 'fa-car' : 'fa-cogs' }}"></i>
                                        </div>
                                        <div class="fw-bold text-dark">
                                            @if(isset($inventario->parte_id))
                                                {{ $inventario->parte->nombre ?? 'N/A' }}
                                            @elseif(isset($inventario->vehiculo_id))
                                                {{ $inventario->vehiculo->marca->nombre ?? 'N/A' }} 
                                                {{ $inventario->vehiculo->modelo->nombre ?? '' }}
                                                {{ $inventario->vehiculo->version->nombre ?? '' }}
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted fw-semibold">
                                        @if(isset($inventario->parte_id))
                                            {{ $inventario->parte->codigo ?? 'N/A' }}
                                        @elseif(isset($inventario->vehiculo_id))
                                            {{ $inventario->vehiculo->codigo ?? 'N/A' }}
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    @if(isset($inventario->parte_id))
                                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-1 fw-semibold">
                                            <span class="d-inline-block rounded-circle bg-info me-1" style="width: 6px; height: 6px;"></span> Parte
                                        </span>
                                    @elseif(isset($inventario->vehiculo_id))
                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 fw-semibold">
                                            <span class="d-inline-block rounded-circle bg-primary me-1" style="width: 6px; height: 6px;"></span> Vehículo
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold text-dark">{{ $inventario->almacen->nombre ?? 'N/A' }}</span>
                                        <small class="text-muted"><i class="fas fa-building me-1" style="font-size:0.7rem;"></i>{{ $inventario->almacen->centroCosto->nombre ?? 'N/A' }}</small>
                                    </div>
                                </td>
                                <td class="text-end fw-bold text-success">{{ number_format($inventario->stock_disponible, 2) }}</td>
                                <td class="text-end fw-bold text-warning">{{ number_format($inventario->stock_reservado, 2) }}</td>
                                <td class="text-end fw-semibold text-muted">{{ number_format($inventario->stock_minimo, 2) }}</td>
                                <td class="text-end px-4">
                                    <a href="{{ route('admin.inventario.kardex', $inventario) }}" class="btn btn-sm btn-light rounded-pill shadow-sm text-primary fw-bold" data-bs-toggle="tooltip" title="Ver Historial (Kardex)">
                                        <i class="fas fa-history me-1"></i> Kardex
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width: 80px; height: 80px;">
                                        <i class="fas fa-box-open fa-2x text-muted opacity-50"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark">Inventario vacío</h6>
                                    <p class="text-muted small">No hay productos o vehículos que coincidan con la búsqueda actual.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                @if($inventarios->hasPages())
                <div class="card-footer bg-white border-top-0 d-flex justify-content-center mt-3 pb-4">
                    {{ $inventarios->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar selects con clases para mejor UI
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('#tipo, #almacen_id, #centro_costo_id').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        }
        
        // Para formularios sin AJAX
        // Los selects ya tienen el atributo onchange para enviar automáticamente
        
        // Para búsqueda con Enter
        document.getElementById('search').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('filterForm').submit();
            }
        });
    });
</script>
@endpush