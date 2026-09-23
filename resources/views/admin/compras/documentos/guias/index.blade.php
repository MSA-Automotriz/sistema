@extends('admin.layouts.app')

@section('title', 'Guías de Entrega')

@section('content')
<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-truck text-info me-2"></i> Documentos de Compra
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6 text-shadow-sm d-flex align-items-center">
                    Guías de Entrega
                </h2>
                <p class="text-white-50 mb-0">Gestiona las guías de remisión y entrega de proveedores</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.guias.create') }}" class="btn bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0">
                    <i class="fas fa-plus text-primary me-2"></i> Nueva Guía
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-lg-4 position-relative" style="top: -3.5rem; z-index: 10;">
    <div class="card dashboard-card border-0 shadow-sm">
        <div class="card-body p-4">
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="alert alert-info border-0 shadow-sm rounded-4 mb-4 bg-info bg-opacity-10 text-info">
                <div class="d-flex align-items-center">
                    <div class="bg-info bg-opacity-20 p-2 rounded-circle me-3">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div>
                        <strong class="d-block">Módulo en desarrollo</strong>
                        <span class="small">La funcionalidad de guías de entrega está siendo implementada.</span>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-4 border-0">Número</th>
                            <th class="py-3 px-4 border-0">Fecha</th>
                            <th class="py-3 px-4 border-0">Proveedor</th>
                            <th class="py-3 px-4 border-0">Estado</th>
                            <th class="py-3 px-4 border-0">Total</th>
                            <th class="py-3 px-4 border-0 text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="bg-light d-inline-flex p-4 rounded-circle mb-3">
                                    <i class="fas fa-clipboard-list text-muted fa-3x"></i>
                                </div>
                                <h5 class="text-dark fw-bold">No hay guías de entrega registradas</h5>
                                <p class="text-muted mb-0">Este módulo está en proceso de implementación</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.btn-group .btn {
    border-radius: 0.375rem;
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.btn-group .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
}
</style>
@endpush