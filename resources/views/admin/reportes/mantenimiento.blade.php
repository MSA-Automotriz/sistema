@extends('admin.layouts.app')

@section('title', 'Reportes de Mantenimiento')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h3 class="mb-0"><i class="fas fa-tools me-2"></i>Reportes de Mantenimiento</h3>
                    <a href="{{ route('admin.reportes.index') }}" class="btn btn-sm btn-light">
                        <i class="fas fa-arrow-left me-1"></i>Volver
                    </a>
                </div>
                <div class="card-body">
                    <form method="GET">
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
                                    <option value="en_progreso" {{ $estado == 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                                    <option value="completado" {{ $estado == 'completado' ? 'selected' : '' }}>Completado</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-info w-100"><i class="fas fa-search me-1"></i>Filtrar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="mb-1">Total Órdenes</h6><h3 class="mb-0">{{ $totalOrdenes }}</h3></div>
                        <i class="fas fa-clipboard-list fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="mb-1">Pendientes</h6><h3 class="mb-0">{{ $ordenesPendientes }}</h3></div>
                        <i class="fas fa-hourglass-half fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="mb-1">En Proceso</h6><h3 class="mb-0">{{ $ordenesEnProceso }}</h3></div>
                        <i class="fas fa-wrench fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="mb-1">Completadas</h6><h3 class="mb-0">{{ $ordenesCompletadas }}</h3></div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-chart-area me-2"></i>Órdenes por Día (Últimos 30 días)</h5></div>
                <div class="card-body"><canvas id="ordenesPorDiaChart" height="80"></canvas></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Órdenes por Estado</h5></div>
                <div class="card-body"><canvas id="ordenesPorEstadoChart"></canvas></div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-user-cog me-2"></i>Top Técnicos</h5></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="table-light">
                                <tr><th>Técnico</th><th class="text-center">Total</th><th class="text-center">Completadas</th><th class="text-center">% Éxito</th></tr>
                            </thead>
                            <tbody>
                                @forelse($topTecnicos as $tecnico)
                                <tr>
                                    <td>{{ $tecnico->name }}</td>
                                    <td class="text-center"><strong>{{ $tecnico->total_ordenes }}</strong></td>
                                    <td class="text-center">{{ $tecnico->completadas }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ $tecnico->total_ordenes > 0 ? round(($tecnico->completadas / $tecnico->total_ordenes) * 100, 1) : 0 }}%</span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted">No hay datos disponibles</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-clock me-2"></i>Tiempo de Servicio</h5></div>
                <div class="card-body">
                    <div class="text-center py-4">
                        <i class="fas fa-stopwatch fa-3x text-info mb-3"></i>
                        <h2 class="mb-2">{{ $tiempoPromedio && $tiempoPromedio->promedio_horas ? round($tiempoPromedio->promedio_horas, 1) : 0 }} horas</h2>
                        <p class="text-muted">Tiempo Promedio de Servicio</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('ordenesPorDiaChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($ordenesPorDia->pluck('fecha')->map(fn($f) => \Carbon\Carbon::parse($f)->format('d/m'))) !!},
        datasets: [{
            label: 'Órdenes',
            data: {!! json_encode($ordenesPorDia->pluck('cantidad')) !!},
            backgroundColor: 'rgba(23, 162, 184, 0.6)',
            borderColor: 'rgb(23, 162, 184)',
            borderWidth: 1
        }]
    },
    options: {responsive: true, maintainAspectRatio: true}
});

new Chart(document.getElementById('ordenesPorEstadoChart').getContext('2d'), {
    type: 'pie',
    data: {
        labels: {!! json_encode($ordenesPorEstado->pluck('estado')->map(fn($e) => ucfirst(str_replace('_', ' ', $e)))) !!},
        datasets: [{
            data: {!! json_encode($ordenesPorEstado->pluck('cantidad')) !!},
            backgroundColor: ['rgba(255, 193, 7, 0.8)', 'rgba(23, 162, 184, 0.8)', 'rgba(40, 167, 69, 0.8)', 'rgba(220, 53, 69, 0.8)']
        }]
    },
    options: {responsive: true, maintainAspectRatio: true, plugins: {legend: {position: 'bottom'}}}
});
</script>
@endpush
@endsection
