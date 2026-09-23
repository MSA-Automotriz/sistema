@extends('admin.layouts.app')

@section('title', 'Punto de Venta')

@push('styles')
    @include('admin.ventas.pos.partials.styles')

    <style>
    /* ELIMINACIÓN TOTAL DE BACKDROP EN POS */
    .modal-backdrop,
    .modal-backdrop.fade,
    .modal-backdrop.show,
    div[class*="backdrop"] {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        z-index: -9999 !important;
    }

    body.modal-open {
        overflow: auto !important;
        padding-right: 0 !important;
    }
    </style>
@endpush

@section('content')
<div class="dashboard-hero" style="padding: 1rem 1rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 gap-sm-0">
            <div class="mb-2 mb-sm-0 flex-grow-1">
                <div class="d-inline-flex align-items-center px-2 px-sm-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 fs-sm-6 mb-2 mb-sm-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-desktop text-info me-2"></i> <span class="d-none d-sm-inline">Terminal POS</span><span class="d-sm-none">POS</span>
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6 display-sm-5 text-shadow-sm" style="font-size: clamp(1.5rem, 5vw, 2.5rem);">Punto de Venta</h2>
                <p class="text-white-50 mb-0 fs-7 fs-sm-6">Atención en mostrador y facturación rápida.</p>
            </div>
            <div class="d-flex flex-wrap gap-2 justify-content-start justify-content-sm-end">
                <a href="{{ route('admin.ventas.pos.ventas') }}" class="btn bg-white text-dark rounded-pill px-3 px-sm-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0 fs-7 fs-sm-6 text-nowrap">
                    <i class="fas fa-history text-primary me-1 me-sm-2"></i> <span class="d-none d-sm-inline">Ver Historial de Ventas</span><span class="d-sm-none">Historial</span>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-2 px-sm-3 px-lg-4 position-relative" style="top: -3.5rem; z-index: 10;">
    <div class="row g-3 g-md-4">
        <!-- Panel izquierdo - Búsqueda y productos -->
        <div class="col-12 col-lg-8 order-2 order-lg-1">
            @include('admin.ventas.pos.partials.search-panel')
        </div>

        <!-- Panel derecho - Carrito de venta -->
        <div class="col-12 col-lg-8 col-xl-4 order-1 order-lg-2">
            @include('admin.ventas.pos.partials.cart-panel')
        </div>
    </div>
</div>

<!-- Modales -->
@include('admin.ventas.pos.partials.modals.cliente-modal')
@include('admin.ventas.pos.partials.modals.item-modal')
@include('admin.ventas.pos.partials.modals.success-modal')

<!-- Container para notificaciones toast -->
<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3"></div>

@endsection

@push('scripts')
    @include('admin.ventas.pos.partials.scripts')
@endpush