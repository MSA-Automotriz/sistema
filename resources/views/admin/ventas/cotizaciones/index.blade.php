@extends('admin.layouts.app')

@section('title', 'Gestión de Cotizaciones')

@section('content')
    <!-- Hero Section -->
    <div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
        <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
        <div class="container-fluid position-relative z-1">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
                <div class="mb-3 mb-lg-0">
                    <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                        <i class="fas fa-file-invoice text-info me-2"></i> Módulo de Ventas
                    </div>
                    <h2 class="fw-bold mb-1 tracking-tight text-white display-6">Cotizaciones</h2>
                    <p class="text-white-50 mb-0">Gestión y seguimiento de propuestas comerciales ({{ $cotizaciones->total() }} registros en total)</p>
                </div>
                <div>
                    <a href="{{ route('admin.ventas.cotizaciones.create') }}" class="btn bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition" style="border: 1px solid rgba(255,255,255,0.8);">
                        <i class="fas fa-plus me-2 text-primary"></i> Nueva Cotización
                    </a>
                </div>
            </div>
        </div>
    </div>

<div class="container-fluid position-relative" style="top: -3.5rem; z-index: 10;">
    
    <!-- Filtros -->
    <div class="card dashboard-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.ventas.cotizaciones.index') }}" method="GET" class="row g-3 align-items-center">
                <!-- Fecha -->
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 border-light text-muted"><i class="fas fa-calendar-alt"></i></span>
                        <input type="date" class="form-control bg-light border-start-0 border-light shadow-none" placeholder="Fecha" name="fecha" value="{{ request('fecha') }}">
                    </div>
                </div>
                
                <!-- Búsqueda -->
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 border-light text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control bg-light border-start-0 border-light shadow-none" placeholder="Buscar cliente o doc..." name="busqueda" value="{{ request('busqueda') }}">
                    </div>
                </div>
                
                <!-- Filtro por estado -->
                <div class="col-md-3">
                    <select class="form-select bg-light border-light shadow-none" name="estado_id">
                        <option value="">Todos los estados</option>
                        @foreach(\App\Models\EstadoCotizacion::all() as $estado)
                            <option value="{{ $estado->id }}" {{ request('estado_id') == $estado->id ? 'selected' : '' }}>
                                {{ $estado->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Botones de acción -->
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-3 shadow-sm flex-grow-1">
                        Filtrar
                    </button>
                    <a href="{{ route('admin.ventas.cotizaciones.index') }}" class="btn btn-light rounded-pill px-3 text-secondary border shadow-sm" data-bs-toggle="tooltip" title="Limpiar Filtros">
                        <i class="fas fa-eraser"></i>
                    </a>
                    <a href="{{ route('admin.ventas.cotizaciones.index', ['export' => 'excel']) }}" class="btn btn-success rounded-pill px-3 shadow-sm" data-bs-toggle="tooltip" title="Exportar a Excel">
                        <i class="fas fa-file-excel"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="row g-4 mb-4">
        <div class="col-lg-12">
            <!-- Tabla de cotizaciones -->
            <div class="card dashboard-card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-borderless table-hover align-middle mb-0" id="tablaCotizaciones">
                        <thead class="bg-light bg-opacity-75" style="border-bottom: 1px solid #f1f5f9;">
                            <tr>
                                <th class="text-muted small fw-bold text-uppercase px-4 py-3">Cliente / Contacto</th>
                                <th class="text-muted small fw-bold text-uppercase py-3">Registro</th>
                                <th class="text-muted small fw-bold text-uppercase py-3">Detalles</th>
                                <th class="text-muted small fw-bold text-uppercase py-3">Estado</th>
                                <th class="text-muted small fw-bold text-uppercase py-3 text-center">Interacciones</th>
                                <th class="text-muted small fw-bold text-uppercase py-3 text-end px-4">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cotizaciones as $cotizacion)
                            <tr style="border-bottom: 1px solid #f8fafc;">
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-dark">
                                                @if($cotizacion->cliente->tipo_cliente === 'natural')
                                                    {{ $cotizacion->cliente->nombres }} {{ $cotizacion->cliente->apellido_paterno }}
                                                @else
                                                    {{ $cotizacion->cliente->razon_social }}
                                                @endif
                                            </span>
                                            <small class="text-muted fw-semibold">
                                                ID: {{ $cotizacion->cliente->documento_identidad }} | <i class="fas fa-phone-alt mx-1" style="font-size: 0.7rem;"></i>{{ $cotizacion->cliente->telefonos->first()->numero ?? 'N/A' }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold text-dark">{{ $cotizacion->almacen->nombre ?? 'Sede Principal' }}</span>
                                        <small class="text-muted"><i class="far fa-calendar-alt me-1"></i>{{ $cotizacion->created_at->format('d/m/Y') }} <span class="ms-1">{{ $cotizacion->created_at->format('H:i') }}</span></small>
                                    </div>
                                </td>
                                <td>
                                    @if($cotizacion->detalles && $cotizacion->detalles->first())
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold text-dark text-truncate" style="max-width: 180px;" data-bs-toggle="tooltip" title="{{ $cotizacion->detalles->first()->descripcion }}">
                                                {{ $cotizacion->detalles->first()->descripcion }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-muted small bg-light px-2 py-1 rounded">Sin Especificar</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        // Mapeo seguro de colores basado en estados
                                        $statusColor = 'secondary';
                                        $estadoNombre = strtolower($cotizacion->estado->nombre ?? '');
                                        if (strpos($estadoNombre, 'interesado') !== false) $statusColor = 'info';
                                        if (strpos($estadoNombre, 'ganado') !== false || strpos($estadoNombre, 'cerrado') !== false) $statusColor = 'success';
                                        if (strpos($estadoNombre, 'perdido') !== false) $statusColor = 'danger';
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }} rounded-pill px-3 py-2 fw-semibold">
                                        <span class="d-inline-block rounded-circle bg-{{ $statusColor }} me-1" style="width: 6px; height: 6px; vertical-align: middle;"></span>
                                        {{ $cotizacion->estado->nombre ?? 'Pendiente' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex justify-content-center align-items-center bg-light rounded-circle fw-bold text-dark border" style="width: 32px; height: 32px;">
                                        {{ $cotizacion->seguimientos->count() }}
                                    </div>
                                </td>
                                <td class="text-end px-4">
                                    <div class="d-flex justify-content-end align-items-center gap-1">
                                        <a href="{{ route('admin.ventas.cotizaciones.gestionar', $cotizacion) }}" 
                                           class="btn btn-sm btn-primary rounded-pill px-3 fw-bold shadow-sm">
                                            Gestionar
                                        </a>
                                        <a href="{{ route('admin.ventas.cotizaciones.edit', $cotizacion) }}" 
                                           class="btn btn-sm btn-light rounded-circle text-info shadow-sm" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.ventas.cotizaciones.show', ['cotizacion' => $cotizacion, 'format' => 'pdf']) }}" 
                                           class="btn btn-sm btn-light rounded-circle text-danger shadow-sm" title="Descargar PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        <form action="{{ route('admin.ventas.cotizaciones.destroy', $cotizacion) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light rounded-circle text-danger shadow-sm" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar esta cotización?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width: 80px; height: 80px;">
                                        <i class="fas fa-folder-open fa-2x text-muted opacity-50"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark">No hay registros encontrados</h6>
                                    <p class="text-muted small">Intenta ajustar los filtros de búsqueda o crea una nueva cotización.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                @if($cotizaciones->hasPages())
                <div class="card-footer bg-white border-top-0 d-flex justify-content-center mt-3 pb-4">
                    {{ $cotizaciones->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
        
    <!-- Resumen y estadísticas -->
    <div class="row g-4">
        <!-- Funnel -->
        <div class="col-lg-6">
            <div class="card dashboard-card border-0 shadow-sm h-100">
                <div class="card-header border-bottom-0 pt-4 pb-0 bg-transparent">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-lg me-3">
                            <i class="fas fa-filter text-primary"></i>
                        </div>
                        <h6 class="mb-0 fw-bold text-dark">Funnel de Gestión Comercial</h6>
                    </div>
                </div>
                <div class="card-body px-4 pt-4 pb-4">
                    <div class="d-flex flex-column align-items-center justify-content-center h-100">
                        <div class="funnel-item bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 py-3 rounded-4 mb-3 w-100 position-relative shadow-sm">
                            <div class="d-flex justify-content-between align-items-center px-4">
                                <span class="fw-bold text-uppercase tracking-wider">Gestionados</span>
                                <span class="fs-4 fw-bold">{{ isset($estadisticas) ? $estadisticas['porcentajeGestionados'] : '0' }}%</span>
                            </div>
                        </div>
                        <div class="funnel-item bg-info bg-opacity-10 text-info border border-info border-opacity-25 py-3 rounded-4 mb-3 position-relative shadow-sm" style="width: 85%;">
                            <div class="d-flex justify-content-between align-items-center px-4">
                                <span class="fw-bold text-uppercase tracking-wider">Contactables</span>
                                <span class="fs-4 fw-bold">{{ isset($estadisticas) ? $estadisticas['porcentajeContactables'] : '0' }}%</span>
                            </div>
                        </div>
                        <div class="funnel-item bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 py-3 rounded-4 position-relative shadow-sm" style="width: 70%;">
                            <div class="d-flex justify-content-between align-items-center px-4">
                                <span class="fw-bold text-uppercase tracking-wider text-dark">Interesados</span>
                                <span class="fs-4 fw-bold text-dark">{{ isset($estadisticas) ? $estadisticas['porcentajeInteresados'] : '0' }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Prioridad -->
        <div class="col-lg-6">
            <div class="card dashboard-card border-0 shadow-sm h-100">
                <div class="card-header border-bottom-0 pt-4 pb-0 bg-transparent">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger bg-opacity-10 p-2 rounded-lg me-3">
                            <i class="fas fa-fire text-danger"></i>
                        </div>
                        <h6 class="mb-0 fw-bold text-dark">Prioridades del Día</h6>
                    </div>
                </div>
                <div class="card-body px-4">
                    <div class="row text-center h-100 align-items-center">
                        <div class="col-4">
                            <div class="priority-circle rounded-circle mx-auto mb-3 bg-danger bg-opacity-10 shadow-sm" style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center; border: 3px solid rgba(220, 53, 69, 0.3);">
                                <div class="text-center text-danger">
                                    <div class="fw-bold" style="font-size: 2rem; line-height: 1;">{{ isset($estadisticas) ? $estadisticas['vencidos'] : '0' }}</div>
                                    <small class="fw-semibold">{{ isset($estadisticas) ? $estadisticas['porcentajeVencidos'] : '0' }}%</small>
                                </div>
                            </div>
                            <p class="mb-0 small fw-bold text-muted text-uppercase tracking-wider">Completamente <br> Vencido</p>
                        </div>
                        
                        <div class="col-4">
                            <div class="priority-circle rounded-circle mx-auto mb-3 bg-warning bg-opacity-10 shadow-sm" style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center; border: 3px solid rgba(255, 193, 7, 0.5);">
                                <div class="text-center text-dark">
                                    <div class="fw-bold" style="font-size: 2rem; line-height: 1;">{{ isset($estadisticas) ? $estadisticas['porVencer'] : '0' }}</div>
                                    <small class="fw-semibold">{{ isset($estadisticas) ? $estadisticas['porcentajePorVencer'] : '0' }}%</small>
                                </div>
                            </div>
                            <p class="mb-0 small fw-bold text-muted text-uppercase tracking-wider">A punto de <br> Vencer</p>
                        </div>
                        
                        <div class="col-4">
                            <div class="priority-circle rounded-circle mx-auto mb-3 bg-success bg-opacity-10 shadow-sm" style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center; border: 3px solid rgba(25, 135, 84, 0.3);">
                                <div class="text-center text-success">
                                    <div class="fw-bold" style="font-size: 2rem; line-height: 1;">{{ isset($estadisticas) ? $estadisticas['aTiempo'] : '0' }}</div>
                                    <small class="fw-semibold">{{ isset($estadisticas) ? $estadisticas['porcentajeATiempo'] : '0' }}%</small>
                                </div>
                            </div>
                            <p class="mb-0 small fw-bold text-muted text-uppercase tracking-wider">A Tiempo <br> (Sano)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Estilos para el funnel */
    .funnel-item {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .funnel-item:hover {
        transform: scale(1.02) translateY(-2px);
    }
    
    /* Estilos para los círculos de prioridad */
    .priority-circle {
        transition: all 0.3s ease;
    }
    
    .priority-circle:hover {
        transform: scale(1.05) translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    }
    
    /* Responsive */
    @media (max-width: 767.98px) {
        .funnel-item, .priority-circle {
            margin-bottom: 1rem !important;
        }
    }
    
    .tracking-wider {
        letter-spacing: 0.05em;
    }
    .tracking-tight {
        letter-spacing: -0.025em;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Inicializar tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush