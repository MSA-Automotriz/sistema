@extends('admin.layouts.app')
@section('title', 'Recepción de Órdenes')

@section('content')
<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-truck-loading text-info me-2"></i> Documentos
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6 text-shadow-sm d-flex align-items-center">
                    Recepción de Órdenes
                </h2>
                <p class="text-white-50 mb-0">Gestiona y recepciona las órdenes de compra</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.inventario.kardex.consulta') }}" class="btn bg-info text-white rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0">
                    <i class="fas fa-search me-2"></i> Consultar Kardex
                </a>
                <a href="{{ route('admin.recepcion.historial') }}" class="btn bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0">
                    <i class="fas fa-history text-primary me-2"></i> Historial
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-lg-4 position-relative" style="top: -3.5rem; z-index: 10;">
    <!-- Tabs Navigation -->
    <div class="card dashboard-card border-0 shadow-sm mb-4">
        <div class="card-body p-0">

            <ul class="nav nav-tabs nav-fill" id="recepcionTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold px-4 py-3" 
                            id="pendientes-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#pendientes" 
                            type="button" 
                            role="tab">
                        <i class="fas fa-clock me-2"></i>
                        Pendientes
                        <span class="badge bg-warning text-dark ms-2">{{ $ordenes->where('estado_recepcion', '!=', 'completo')->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold px-4 py-3" 
                            id="parciales-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#parciales" 
                            type="button" 
                            role="tab">
                        <i class="fas fa-pause-circle me-2"></i>
                        Parciales
                        <span class="badge bg-info ms-2">{{ $ordenes->where('estado_recepcion', 'parcial')->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold px-4 py-3" 
                            id="completas-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#completas" 
                            type="button" 
                            role="tab">
                        <i class="fas fa-check-circle me-2"></i>
                        Completas
                        <span class="badge bg-success ms-2">{{ $ordenes->where('estado_recepcion', 'completo')->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold px-4 py-3" 
                            id="todas-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#todas" 
                            type="button" 
                            role="tab">
                        <i class="fas fa-list me-2"></i>
                        Todas
                        <span class="badge bg-primary ms-2">{{ $ordenes->count() }}</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content" id="recepcionTabsContent">
        <!-- Pendientes Tab -->
        <div class="tab-pane fade show active" id="pendientes" role="tabpanel">
            @include('admin.compras.documentos.recepcion.partials.table', [
                'ordenes' => $ordenes->where('estado_recepcion', '!=', 'completo'),
                'titulo' => 'Órdenes Pendientes de Recepción',
                'mostrarAcciones' => true
            ])
        </div>

        <!-- Parciales Tab -->
        <div class="tab-pane fade" id="parciales" role="tabpanel">
            @include('admin.compras.documentos.recepcion.partials.table', [
                'ordenes' => $ordenes->where('estado_recepcion', 'parcial'),
                'titulo' => 'Órdenes con Recepción Parcial',
                'mostrarAcciones' => true
            ])
        </div>

        <!-- Completas Tab -->
        <div class="tab-pane fade" id="completas" role="tabpanel">
            @include('admin.compras.documentos.recepcion.partials.table', [
                'ordenes' => $ordenes->where('estado_recepcion', 'completo'),
                'titulo' => 'Órdenes Completamente Recibidas',
                'mostrarAcciones' => false
            ])
        </div>

        <!-- Todas Tab -->
        <div class="tab-pane fade" id="todas" role="tabpanel">
            @include('admin.compras.documentos.recepcion.partials.table', [
                'ordenes' => $ordenes,
                'titulo' => 'Todas las Órdenes',
                'mostrarAcciones' => true
            ])
        </div>
    </div>

    <!-- Statistics Cards -->
    @if($ordenes->count() > 0)
    <div class="row mt-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card dashboard-card border-0 text-center h-100 shadow-sm">
                <div class="card-body p-4">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                        <i class="fas fa-shopping-cart text-primary fa-2x"></i>
                    </div>
                    <h2 class="fw-bold text-primary mb-2">{{ $ordenes->count() }}</h2>
                    <p class="text-muted mb-0 small text-uppercase fw-bold">Total Órdenes</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card dashboard-card border-0 text-center h-100 shadow-sm">
                <div class="card-body p-4">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                        <i class="fas fa-clock text-warning fa-2x"></i>
                    </div>
                    <h2 class="fw-bold text-warning mb-2">{{ $ordenes->where('estado_recepcion', 'parcial')->count() }}</h2>
                    <p class="text-muted mb-0 small text-uppercase fw-bold">Recepciones Parciales</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card dashboard-card border-0 text-center h-100 shadow-sm">
                <div class="card-body p-4">
                    <div class="bg-info bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                        <i class="fas fa-boxes text-info fa-2x"></i>
                    </div>
                    <h2 class="fw-bold text-info mb-2">{{ $ordenes->sum(function($orden) { return $orden->detalles->sum('cantidad_en_compra'); }) }}</h2>
                    <p class="text-muted mb-0 small text-uppercase fw-bold">Items Totales</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card dashboard-card border-0 text-center h-100 shadow-sm">
                <div class="card-body p-4">
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                        <i class="fas fa-check-double text-success fa-2x"></i>
                    </div>
                    <h2 class="fw-bold text-success mb-2">{{ $ordenes->sum(function($orden) { return $orden->detalles->sum('cantidad_recibida'); }) }}</h2>
                    <p class="text-muted mb-0 small text-uppercase fw-bold">Items Recibidos</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Efecto hover para las tarjetas de estadísticas
    const statCards = document.querySelectorAll('.col-lg-3 .card');
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(0px)';
            this.style.transition = 'all 0.3s ease';
            this.style.boxShadow = '0 8px 32px rgba(0, 0, 0, 0.15)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 4px 16px rgba(0, 0, 0, 0.08)';
        });
    });
    
    // Animación de entrada para las estadísticas
    const animateCounters = () => {
        const counters = document.querySelectorAll('.col-lg-3 h2');
        counters.forEach(counter => {
            const target = parseInt(counter.textContent);
            const duration = 1500;
            const step = target / (duration / 16);
            let current = 0;
            
            const updateCounter = () => {
                current += step;
                if (current < target) {
                    counter.textContent = Math.floor(current);
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target;
                }
            };
            
            updateCounter();
        });
    };
    
    // Observador para activar animación cuando las estadísticas sean visibles
    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                statsObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    
    const statsSection = document.querySelector('.row.mt-4');
    if (statsSection) {
        statsObserver.observe(statsSection);
    }
});
</script>
@endpush
@endsection