@extends('admin.layouts.app')

@section('title', 'Reportes de Ventas')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Reportes de Ventas
                    </h3>
                    <a href="{{ route('admin.reportes.index') }}" class="btn btn-sm btn-light">
                        <i class="fas fa-arrow-left me-1"></i>Volver
                    </a>
                </div>

                <!-- Filtros -->
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.reportes.ventas') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Fecha Inicio</label>
                                <input type="date" name="fecha_inicio" class="form-control" value="{{ $fechaInicio }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Fecha Fin</label>
                                <input type="date" name="fecha_fin" class="form-control" value="{{ $fechaFin }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Estado</label>
                                <select name="estado" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="pendiente" {{ $estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="completada" {{ $estado == 'completada' ? 'selected' : '' }}>Completada</option>
                                    <option value="cancelada" {{ $estado == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i>Filtrar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de resumen -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Total Ventas</h6>
                            <h3 class="mb-0">{{ $totalVentas }}</h3>
                        </div>
                        <i class="fas fa-shopping-cart fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Monto Total</h6>
                            <h3 class="mb-0">S/. {{ number_format($montoTotal, 2) }}</h3>
                        </div>
                        <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Pendientes</h6>
                            <h3 class="mb-0">{{ $ventasPendientes }}</h3>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Completadas</h6>
                            <h3 class="mb-0">{{ $ventasCompletadas }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Ventas por Día (Últimos 30 días)</h5>
                </div>
                <div class="card-body">
                    <canvas id="ventasPorDiaChart" height="80"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Ventas por Estado</h5>
                </div>
                <div class="card-body">
                    <canvas id="ventasPorEstadoChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Productos -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-trophy me-2"></i>Top 10 Productos Más Vendidos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th class="text-center">Cantidad Vendida</th>
                                    <th class="text-end">Monto Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProductos as $index => $producto)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><span class="badge bg-secondary">{{ $producto->codigo }}</span></td>
                                    <td>{{ $producto->nombre }}</td>
                                    <td class="text-center"><strong>{{ $producto->total_vendido }}</strong></td>
                                    <td class="text-end">S/. {{ number_format($producto->total_monto, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No hay productos vendidos en este período</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de ventas por día
const ventasPorDiaCtx = document.getElementById('ventasPorDiaChart').getContext('2d');
new Chart(ventasPorDiaCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($ventasPorDia->pluck('fecha')->map(function($fecha) {
            return \Carbon\Carbon::parse($fecha)->format('d/m');
        })) !!},
        datasets: [{
            label: 'Cantidad de Ventas',
            data: {!! json_encode($ventasPorDia->pluck('cantidad')) !!},
            borderColor: 'rgb(54, 162, 235)',
            backgroundColor: 'rgba(54, 162, 235, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'Monto (S/.)',
            data: {!! json_encode($ventasPorDia->pluck('monto')) !!},
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.4,
            fill: true,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Cantidad'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Monto (S/.)'
                },
                grid: {
                    drawOnChartArea: false,
                }
            }
        }
    }
});

// Gráfico de ventas por estado
const ventasPorEstadoCtx = document.getElementById('ventasPorEstadoChart').getContext('2d');
new Chart(ventasPorEstadoCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($ventasPorEstado->pluck('estado')->map(function($estado) {
            return ucfirst($estado);
        })) !!},
        datasets: [{
            data: {!! json_encode($ventasPorEstado->pluck('cantidad')) !!},
            backgroundColor: [
                'rgba(54, 162, 235, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(255, 99, 132, 0.8)',
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});
</script>
@endpush
@endsection
