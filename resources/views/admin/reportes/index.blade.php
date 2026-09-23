@extends('admin.layouts.app')

@section('title', 'Reportes')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Centro de Reportes
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Seleccione el tipo de reporte que desea generar:</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Reporte de Ventas -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 hover-shadow">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-chart-line fa-3x text-primary"></i>
                    </div>
                    <h5 class="card-title">Ventas</h5>
                    <p class="card-text text-muted small">Reportes de ventas, cotizaciones y facturación</p>
                    <a href="{{ route('admin.reportes.ventas') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye me-1"></i>
                        Ver Reporte
                    </a>
                </div>
            </div>
        </div>

        <!-- Reporte de Compras -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 hover-shadow">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-shopping-cart fa-3x text-success"></i>
                    </div>
                    <h5 class="card-title">Compras</h5>
                    <p class="card-text text-muted small">Reportes de órdenes de compra y proveedores</p>
                    <a href="{{ route('admin.reportes.compras') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-eye me-1"></i>
                        Ver Reporte
                    </a>
                </div>
            </div>
        </div>

        <!-- Reporte de Inventario -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 hover-shadow">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-boxes fa-3x text-warning"></i>
                    </div>
                    <h5 class="card-title">Inventario</h5>
                    <p class="card-text text-muted small">Reportes de stock, kardex y movimientos</p>
                    <a href="{{ route('admin.reportes.inventario') }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-eye me-1"></i>
                        Ver Reporte
                    </a>
                </div>
            </div>
        </div>

        <!-- Reporte de Mantenimiento -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 hover-shadow">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-tools fa-3x text-info"></i>
                    </div>
                    <h5 class="card-title">Mantenimiento</h5>
                    <p class="card-text text-muted small">Reportes de órdenes y servicios de mantenimiento</p>
                    <a href="{{ route('admin.reportes.mantenimiento') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-eye me-1"></i>
                        Ver Reporte
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .hover-shadow {
        transition: all 0.3s ease;
    }
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
</style>
@endpush
@endsection
