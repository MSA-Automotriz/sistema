@extends('admin.layouts.app')

@section('title', 'Reportes de Compras')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h3 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Reportes de Compras</h3>
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
                                    <option value="aprobada" {{ $estado == 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                                    <option value="recibida" {{ $estado == 'recibida' ? 'selected' : '' }}>Recibida</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-success w-100"><i class="fas fa-search me-1"></i>Filtrar</button>
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
                        <i class="fas fa-file-invoice fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="mb-1">Monto Total</h6><h3 class="mb-0">S/. {{ number_format($montoTotal, 2) }}</h3></div>
                        <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="mb-1">Pendientes</h6><h3 class="mb-0">{{ $ordenesPendientes }}</h3></div>
                        <i class="fas fa-clock fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="mb-1">Recibidas</h6><h3 class="mb-0">{{ $ordenesRecibidas }}</h3></div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Compras por Día (Últimos 30 días)</h5></div>
                <div class="card-body"><canvas id="comprasPorDiaChart" height="80"></canvas></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Compras por Estado</h5></div>
                <div class="card-body"><canvas id="comprasPorEstadoChart"></canvas></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-users me-2"></i>Top 10 Proveedores</h5></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr><th>#</th><th>Proveedor</th><th class="text-center">Total Órdenes</th><th class="text-end">Monto Total</th></tr>
                            </thead>
                            <tbody>
                                @forelse($topProveedores as $index => $proveedor)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $proveedor->nombre }}</td>
                                    <td class="text-center"><strong>{{ $proveedor->total_ordenes }}</strong></td>
                                    <td class="text-end">S/. {{ number_format($proveedor->total_monto, 2) }}</td>
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
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('comprasPorDiaChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: {!! json_encode($comprasPorDia->pluck('fecha')->map(fn($f) => \Carbon\Carbon::parse($f)->format('d/m'))) !!},
        datasets: [{
            label: 'Cantidad',
            data: {!! json_encode($comprasPorDia->pluck('cantidad')) !!},
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {responsive: true, maintainAspectRatio: true}
});

new Chart(document.getElementById('comprasPorEstadoChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($comprasPorEstado->pluck('estado')->map(fn($e) => ucfirst($e))) !!},
        datasets: [{
            data: {!! json_encode($comprasPorEstado->pluck('cantidad')) !!},
            backgroundColor: ['rgba(54, 162, 235, 0.8)', 'rgba(75, 192, 192, 0.8)', 'rgba(255, 206, 86, 0.8)', 'rgba(255, 99, 132, 0.8)']
        }]
    },
    options: {responsive: true, maintainAspectRatio: true, plugins: {legend: {position: 'bottom'}}}
});
</script>
@endpush
@endsection
