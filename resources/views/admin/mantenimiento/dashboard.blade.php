<!-- resources/views/admin/mantenimiento/dashboard.blade.php -->
@extends('admin.layouts.app')

@section('title', 'Dashboard de Mantenimiento')

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    }
    
    .stat-icon {
        font-size: 2.25rem;
        opacity: 0.9;
    }
    
    .dashboard-card {
        border: none;
        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        border-radius: 0.5rem;
    }
    
    .dashboard-card .card-header {
        background-color: transparent;
        border-bottom: 1px solid #e5e7eb;
        padding: 1.25rem 1.5rem 1rem 1.5rem;
    }
    
    .dashboard-card .card-body {
        padding: 1.5rem;
    }
    
    .table-responsive {
        max-height: 350px;
        overflow-y: auto;
        border-radius: 0.375rem;
    }
    
    .table-responsive::-webkit-scrollbar {
        width: 4px;
    }
    
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    
    .table-responsive::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 2px;
    }
    
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    .chart-container {
        position: relative;
        height: 300px;
    }
    
    .chart-container canvas {
        max-height: 300px !important;
    }
    
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin: -1.5rem -1.5rem 2rem -1.5rem;
        border-radius: 0 0 1rem 1rem;
    }
    
    .page-header h1 {
        margin-bottom: 0;
        font-weight: 700;
    }
    
    .btn-refresh {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        backdrop-filter: blur(10px);
    }
    
    .btn-refresh:hover {
        background: rgba(255, 255, 255, 0.3);
        color: white;
    }
    
    .dropdown-toggle-period {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        backdrop-filter: blur(10px);
    }
    
    .dropdown-toggle-period:hover {
        background: rgba(255, 255, 255, 0.3);
        color: white;
    }
    
    @media (max-width: 768px) {
        .page-header {
            padding: 1.5rem 0;
            margin: -1rem -1rem 1.5rem -1rem;
        }
        
        .stat-card .card-body {
            padding: 1rem;
        }
        
        .stat-icon {
            font-size: 1.75rem;
        }
        
        .dashboard-card .card-body {
            padding: 1rem;
        }
        
        .chart-container {
            height: 250px;
        }
        
        .table-responsive {
            max-height: 250px;
        }
    }
</style>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                <div>
                    <h1 class="h2 mb-1">Dashboard de Mantenimiento</h1>
                    <p class="mb-0 opacity-75">Monitoreo en tiempo real de operaciones del taller</p>
                </div>
                <div class="btn-toolbar">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-refresh btn-sm" id="refreshData">
                            <i class="fas fa-sync-alt me-1"></i>
                            <span class="d-none d-sm-inline">Actualizar</span>
                        </button>
                    </div>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle-period btn-sm dropdown-toggle" type="button" id="periodDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-calendar me-1"></i>
                            <span class="d-none d-sm-inline">Esta semana</span>
                            <span class="d-sm-none">Semana</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="periodDropdown">
                            <li><a class="dropdown-item" href="#" data-period="day">Hoy</a></li>
                            <li><a class="dropdown-item active" href="#" data-period="week">Esta semana</a></li>
                            <li><a class="dropdown-item" href="#" data-period="month">Este mes</a></li>
                            <li><a class="dropdown-item" href="#" data-period="year">Este año</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="card-title text-uppercase text-white-50 mb-2">Citas Agendadas</h6>
                            <h2 class="mb-0 fw-bold" id="citasCount">{{ $estadisticas['citas_pendientes'] }}</h2>
                            <p class="small mb-0 text-white-50">Pendientes de atención</p>
                        </div>
                        <div class="col-auto">
                            <div class="stat-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 d-flex align-items-center justify-content-between">
                    <a href="{{ route('admin.mantenimiento.citas.index') }}" class="text-white text-decoration-none small">
                        <i class="fas fa-external-link-alt me-1"></i>Ver detalles
                    </a>
                    <div class="small text-white-50"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="card-title text-uppercase text-white-50 mb-2">Órdenes en Proceso</h6>
                            <h2 class="mb-0 fw-bold" id="ordenesProcesoCount">{{ $estadisticas['ordenes_en_proceso'] }}</h2>
                            <p class="small mb-0 text-white-50">En diagnóstico o trabajo</p>
                        </div>
                        <div class="col-auto">
                            <div class="stat-icon">
                                <i class="fas fa-tools"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 d-flex align-items-center justify-content-between">
                    <a href="{{ route('admin.mantenimiento.ordenes.index', ['estado' => 'en_proceso']) }}" class="text-white text-decoration-none small">
                        <i class="fas fa-external-link-alt me-1"></i>Ver detalles
                    </a>
                    <div class="small text-white-50"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-success text-white h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="card-title text-uppercase text-white-50 mb-2">Órdenes Completadas</h6>
                            <h2 class="mb-0 fw-bold" id="ordenesCompletadasCount">{{ $estadisticas['ordenes_completadas'] }}</h2>
                            <p class="small mb-0 text-white-50">En el período actual</p>
                        </div>
                        <div class="col-auto">
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 d-flex align-items-center justify-content-between">
                    <a href="{{ route('admin.mantenimiento.ordenes.index', ['estado' => 'completado']) }}" class="text-white text-decoration-none small">
                        <i class="fas fa-external-link-alt me-1"></i>Ver detalles
                    </a>
                    <div class="small text-white-50"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-info text-white h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="card-title text-uppercase text-white-50 mb-2">Facturación</h6>
                            <h2 class="mb-0 fw-bold" id="facturacionTotal">S/ {{ number_format($estadisticas['facturacion_total'], 2) }}</h2>
                            <p class="small mb-0 text-white-50">En el período actual</p>
                        </div>
                        <div class="col-auto">
                            <div class="stat-icon">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 d-flex align-items-center justify-content-between">
                    <a href="{{ route('admin.mantenimiento.ordenes.index', ['estado' => 'facturado']) }}" class="text-white text-decoration-none small">
                        <i class="fas fa-external-link-alt me-1"></i>Ver detalles
                    </a>
                    <div class="small text-white-50"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos de análisis -->
    <div class="row g-4 mb-4">
        <!-- Gráfico de Órdenes de Trabajo por Estado -->
        <div class="col-lg-6">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-chart-pie text-primary me-2"></i>
                        <h5 class="mb-0">Órdenes por Estado</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="estadoOrdenesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Gráfico de Facturación Mensual -->
        <div class="col-lg-6">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-chart-line text-success me-2"></i>
                        <h5 class="mb-0">Facturación Mensual</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="facturacionMensualChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tablas de actividad reciente -->
    <div class="row g-4 mb-4">
        <!-- Citas Próximas -->
        <div class="col-xl-6">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-check text-info me-2"></i>
                            <h5 class="mb-0">Próximas Citas</h5>
                        </div>
                        <span class="badge bg-info">{{ count($proximasCitas) }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-semibold">Fecha</th>
                                    <th class="fw-semibold">Cliente</th>
                                    <th class="fw-semibold d-none d-md-table-cell">Vehículo</th>
                                    <th class="fw-semibold d-none d-lg-table-cell">Motivo</th>
                                    <th class="fw-semibold text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($proximasCitas as $cita)
                                    <tr>
                                        <td class="small">
                                            <div class="fw-semibold">{{ \Carbon\Carbon::parse($cita->fecha_hora_cita)->format('d/m') }}</div>
                                            <div class="text-muted">{{ \Carbon\Carbon::parse($cita->fecha_hora_cita)->format('H:i') }}</div>
                                        </td>
                                        <td class="small">
                                            <div class="fw-semibold">
                                                @if($cita->cliente->tipo_cliente == 'persona')
                                                    {{ $cita->cliente->nombres }} {{ $cita->cliente->apellido_paterno }}
                                                @else
                                                    {{ $cita->cliente->razon_social }}
                                                @endif
                                            </div>
                                        </td>
                                        <td class="small fw-semibold d-none d-md-table-cell">{{ $cita->vehiculo->nro_placa }}</td>
                                        <td class="small text-muted d-none d-lg-table-cell">{{ Str::limit($cita->motivo_visita, 25) }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.mantenimiento.citas.show', $cita) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-calendar-times fa-2x mb-2"></i>
                                            <div>No hay citas próximas</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Órdenes en Progreso -->
        <div class="col-xl-6">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-cogs text-warning me-2"></i>
                            <h5 class="mb-0">Órdenes en Progreso</h5>
                        </div>
                        <span class="badge bg-warning">{{ count($ordenesEnProgreso) }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-semibold">Código</th>
                                    <th class="fw-semibold">Cliente</th>
                                    <th class="fw-semibold d-none d-md-table-cell">Vehículo</th>
                                    <th class="fw-semibold d-none d-lg-table-cell">Estado</th>
                                    <th class="fw-semibold d-none d-xl-table-cell">Técnico</th>
                                    <th class="fw-semibold text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ordenesEnProgreso as $orden)
                                    <tr>
                                        <td class="small">
                                            <span class="fw-semibold text-primary">{{ $orden->codigo_orden }}</span>
                                        </td>
                                        <td class="small">
                                            <div class="fw-semibold">
                                                @if($orden->cliente->tipo_cliente == 'persona')
                                                    {{ $orden->cliente->nombres }} {{ $orden->cliente->apellido_paterno }}
                                                @else
                                                    {{ $orden->cliente->razon_social }}
                                                @endif
                                            </div>
                                        </td>
                                        <td class="small fw-semibold d-none d-md-table-cell">{{ $orden->vehiculo->nro_placa }}</td>
                                        <td class="small d-none d-lg-table-cell">
                                            @switch($orden->estado)
                                                @case('diagnostico')
                                                    <span class="badge bg-info">Diagnóstico</span>
                                                    @break
                                                @case('espera_aprobacion')
                                                    <span class="badge bg-warning">Esperando</span>
                                                    @break
                                                @case('en_progreso')
                                                    <span class="badge bg-primary">En Progreso</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td class="small text-muted d-none d-xl-table-cell">{{ $orden->tecnico->name ?? 'Sin asignar' }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.mantenimiento.ordenes.show', $orden) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-tools fa-2x mb-2"></i>
                                            <div>No hay órdenes en progreso</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rankings y estadísticas detalladas -->
    <div class="row g-4 mb-4">
        <!-- Top Servicios -->
        <div class="col-xl-6">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-trophy text-success me-2"></i>
                        <h5 class="mb-0">Servicios Más Solicitados</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-semibold text-center" width="60">#</th>
                                    <th class="fw-semibold">Servicio</th>
                                    <th class="fw-semibold text-center">Cantidad</th>
                                    <th class="fw-semibold text-end">Ingresos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topServicios as $index => $servicio)
                                    <tr>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $index < 3 ? ($index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'warning')) : 'light text-dark' }} rounded-pill">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td class="small">
                                            <div class="fw-semibold">{{ Str::limit($servicio->descripcion, 35) }}</div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ $servicio->cantidad }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-semibold text-success">S/ {{ number_format($servicio->total, 2) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                            <div>No hay datos disponibles</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Top Repuestos -->
        <div class="col-xl-6">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-cog text-primary me-2"></i>
                        <h5 class="mb-0">Repuestos Más Utilizados</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-semibold text-center" width="60">#</th>
                                    <th class="fw-semibold">Repuesto</th>
                                    <th class="fw-semibold text-center">Cantidad</th>
                                    <th class="fw-semibold text-end">Ingresos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topRepuestos as $index => $repuesto)
                                    <tr>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $index < 3 ? ($index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'warning')) : 'light text-dark' }} rounded-pill">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td class="small">
                                            <div class="fw-semibold">{{ Str::limit($repuesto->descripcion, 35) }}</div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $repuesto->cantidad }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-semibold text-success">S/ {{ number_format($repuesto->total, 2) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="fas fa-wrench fa-2x mb-2"></i>
                                            <div>No hay datos disponibles</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Datos para los gráficos
            const estadoOrdenesData = {
                labels: ['Diagnóstico', 'Esperando Aprobación', 'En Progreso', 'Finalizado', 'Facturado', 'Entregado'],
                datasets: [{
                    label: 'Órdenes de Trabajo',
                    data: [
                        {{ $estadisticas['ordenes_diagnostico'] }}, 
                        {{ $estadisticas['ordenes_espera_aprobacion'] }}, 
                        {{ $estadisticas['ordenes_en_progreso'] }}, 
                        {{ $estadisticas['ordenes_finalizadas'] }}, 
                        {{ $estadisticas['ordenes_facturadas'] }}, 
                        {{ $estadisticas['ordenes_entregadas'] }}
                    ],
                    backgroundColor: [
                        'rgba(23, 162, 184, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(0, 123, 255, 0.7)',
                        'rgba(40, 167, 69, 0.7)',
                        'rgba(108, 117, 125, 0.7)',
                        'rgba(52, 58, 64, 0.7)'
                    ],
                    borderColor: [
                        'rgba(23, 162, 184, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(0, 123, 255, 1)',
                        'rgba(40, 167, 69, 1)',
                        'rgba(108, 117, 125, 1)',
                        'rgba(52, 58, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            };
            
            const facturacionMensualData = {
                labels: {!! json_encode($estadisticas['facturacion_mensual_labels']) !!},
                datasets: [{
                    label: 'Facturación Mensual (S/)',
                    data: {!! json_encode($estadisticas['facturacion_mensual_valores']) !!},
                    backgroundColor: 'rgba(0, 123, 255, 0.3)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1,
                    tension: 0.4,
                    fill: true
                }]
            };
            
            // Configuración de los gráficos
            const estadoOrdenesConfig = {
                type: 'pie',
                data: estadoOrdenesData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            };
            
            const facturacionMensualConfig = {
                type: 'line',
                data: facturacionMensualData,
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `S/ ${context.raw.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'S/ ' + value.toLocaleString('es-PE');
                                }
                            }
                        }
                    }
                }
            };
            
            // Inicializar los gráficos
            const estadoOrdenesChart = new Chart(
                document.getElementById('estadoOrdenesChart'),
                estadoOrdenesConfig
            );
            
            const facturacionMensualChart = new Chart(
                document.getElementById('facturacionMensualChart'),
                facturacionMensualConfig
            );
            
            // Cambiar período
            const periodButtons = document.querySelectorAll('[data-period]');
            periodButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Actualizar estado activo
                    periodButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Actualizar texto del botón
                    const periodText = this.textContent;
                    document.getElementById('periodDropdown').innerHTML = `<i class="fas fa-calendar"></i> ${periodText}`;
                    
                    // Aquí iría la lógica para actualizar los datos según el período seleccionado
                    // mediante una petición AJAX
                    const period = this.getAttribute('data-period');
                    actualizarDatosPorPeriodo(period);
                });
            });
            
            // Función para actualizar datos
            function actualizarDatosPorPeriodo(period) {
                fetch(`/admin/mantenimiento/dashboard/datos?period=${period}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Actualizar tarjetas de estadísticas
                    document.getElementById('citasCount').textContent = data.citas_pendientes;
                    document.getElementById('ordenesProcesoCount').textContent = data.ordenes_en_proceso;
                    document.getElementById('ordenesCompletadasCount').textContent = data.ordenes_completadas;
                    document.getElementById('facturacionTotal').textContent = `S/ ${parseFloat(data.facturacion_total).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                    
                    // Actualizar gráficos
                    estadoOrdenesChart.data.datasets[0].data = [
                        data.ordenes_diagnostico,
                        data.ordenes_espera_aprobacion,
                        data.ordenes_en_progreso,
                        data.ordenes_finalizadas,
                        data.ordenes_facturadas,
                        data.ordenes_entregadas
                    ];
                    estadoOrdenesChart.update();
                    
                    facturacionMensualChart.data.labels = data.facturacion_mensual_labels;
                    facturacionMensualChart.data.datasets[0].data = data.facturacion_mensual_valores;
                    facturacionMensualChart.update();
                })
                .catch(error => console.error('Error al actualizar datos:', error));
            }
            
            // Botón de actualización
            document.getElementById('refreshData').addEventListener('click', function() {
                const activeButton = document.querySelector('[data-period].active');
                const period = activeButton ? activeButton.getAttribute('data-period') : 'week';
                actualizarDatosPorPeriodo(period);
            });
        });
    </script>
@endpush