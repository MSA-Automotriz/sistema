@extends('admin.layouts.app')

@section('title', 'Dashboard Principal')



@section('content')
    <!-- Hero Section -->
    <div class="dashboard-hero">
        <div class="hero-glow"></div>
        <div class="hero-glow-alt"></div>
        <div class="container-fluid position-relative z-1">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-4 border border-white border-opacity-25 backdrop-blur">
                        <span class="badge bg-primary rounded-pill me-2">V2.0</span> Panel de Control Técnico
                    </div>
                    <h1 class="display-5 fw-bold mb-3 tracking-tight">Potencia tu gestión con excelencia</h1>
                    <p class="fs-5 mb-0 text-white-50 max-w-lg">Sistema integral de administración automotriz. Monitorea el rendimiento, recursos y operaciones diarias.</p>
                </div>
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                    <div class="d-inline-flex align-items-center bg-white bg-opacity-10 p-3 rounded-4 border border-white border-opacity-10 backdrop-blur shadow-sm">
                        <div class="me-3 text-end">
                            <div class="text-white-50 small text-uppercase fw-bold letter-spacing-wide">Fecha Actual</div>
                            <div class="fs-5 fw-bold text-white">{{ \Carbon\Carbon::now()->translatedFormat('d M, Y') }}</div>
                        </div>
                        <div class="bg-blue-500 bg-opacity-20 p-3 rounded-3 border border-white border-opacity-10">
                            <i class="fas fa-calendar-alt fs-4 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas principales -->
    <div class="row g-4 mb-5">
        <!-- Usuarios -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1 tracking-wider">Total Usuarios</p>
                            <h2 class="mb-0 fw-bold text-dark">{{ $totalUsuarios ?? 0 }}</h2>
                        </div>
                        <div class="stat-icon-wrapper bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-t pt-3" style="border-top: 1px solid #f1f5f9;">
                        <span class="text-success small fw-semibold"><i class="fas fa-check-circle me-1"></i>Activos</span>
                        <a href="{{ route('admin.usuarios.usuarios.index') }}" class="text-primary text-decoration-none bg-primary bg-opacity-10 px-2 py-1 rounded small fw-semibold transition hover:bg-opacity-25">Administrar</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Ventas -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1 tracking-wider">Ventas del Mes</p>
                            <h2 class="mb-0 fw-bold text-dark">S/ {{ number_format($ventasMes ?? 0, 2) }}</h2>
                        </div>
                        <div class="stat-icon-wrapper bg-success bg-opacity-10 text-success">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-t pt-3" style="border-top: 1px solid #f1f5f9;">
                        <span class="text-muted small fw-semibold">Facturación actual</span>
                        <a href="{{ route('admin.ventas.index') }}" class="text-success text-decoration-none bg-success bg-opacity-10 px-2 py-1 rounded small fw-semibold transition hover:bg-opacity-25">Reportes</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Órdenes -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1 tracking-wider">Órdenes Activas</p>
                            <h2 class="mb-0 fw-bold text-dark">{{ $ordenesPendientes ?? 0 }}</h2>
                        </div>
                        <div class="stat-icon-wrapper bg-warning bg-opacity-10 text-warning">
                            <i class="fas fa-tools"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-t pt-3" style="border-top: 1px solid #f1f5f9;">
                        <span class="text-warning small fw-semibold"><i class="fas fa-clock me-1"></i>En taller</span>
                        <a href="{{ route('admin.mantenimiento.ordenes.index') }}" class="text-warning text-decoration-none bg-warning bg-opacity-10 px-2 py-1 rounded small fw-semibold transition hover:bg-opacity-25">Gestionar</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stock -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1 tracking-wider">Stock Crítico</p>
                            <h2 class="mb-0 fw-bold text-dark">{{ $stockCritico ?? 0 }}</h2>
                        </div>
                        <div class="stat-icon-wrapper bg-danger bg-opacity-10 text-danger">
                            <i class="fas fa-boxes"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-t pt-3" style="border-top: 1px solid #f1f5f9;">
                        <span class="text-danger small fw-semibold"><i class="fas fa-exclamation-triangle me-1"></i>Reponer</span>
                        <a href="{{ route('admin.inventario.index') }}" class="text-danger text-decoration-none bg-danger bg-opacity-10 px-2 py-1 rounded small fw-semibold transition hover:bg-opacity-25">Inventario</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="row g-4 mb-5">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-lg me-3">
                            <i class="fas fa-bolt text-primary"></i>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark">Acciones Rápidas</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ route('admin.ventas.cotizaciones.create') }}" class="quick-action-btn w-100 py-4 text-center">
                                <i class="fas fa-file-invoice fs-3 d-block mb-3 text-primary"></i>
                                <span class="fw-semibold small d-block">Cotización</span>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ route('admin.ventas.pos.index') }}" class="quick-action-btn w-100 py-4 text-center">
                                <i class="fas fa-cash-register fs-3 d-block mb-3 text-success"></i>
                                <span class="fw-semibold small d-block">Punto de Venta</span>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ route('admin.mantenimiento.citas.create') }}" class="quick-action-btn w-100 py-4 text-center">
                                <i class="fas fa-calendar-plus fs-3 d-block mb-3 text-info"></i>
                                <span class="fw-semibold small d-block">Nueva Cita</span>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ route('admin.clientes.create') }}" class="quick-action-btn w-100 py-4 text-center">
                                <i class="fas fa-user-plus fs-3 d-block mb-3 text-warning"></i>
                                <span class="fw-semibold small d-block">Alta Cliente</span>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ route('admin.compras.ordenes.create') }}" class="quick-action-btn w-100 py-4 text-center">
                                <i class="fas fa-shopping-cart fs-3 d-block mb-3 text-secondary"></i>
                                <span class="fw-semibold small d-block">Comprar Stock</span>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ route('admin.inventario.movimientos.create') }}" class="quick-action-btn w-100 py-4 text-center">
                                <i class="fas fa-exchange-alt fs-3 d-block mb-3 text-dark"></i>
                                <span class="fw-semibold small d-block">Movimiento</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actividad -->
    <div class="row g-4">
        <!-- Actividad Reciente -->
        <div class="col-lg-8">
            <div class="card dashboard-card h-100">
                <div class="card-header border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="bg-info bg-opacity-10 p-2 rounded-lg me-3">
                                <i class="fas fa-chart-bar text-info"></i>
                            </div>
                            <h5 class="mb-0 fw-bold text-dark">Actividad Reciente</h5>
                        </div>
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill shadow-sm">Últimos 30 días</span>
                    </div>
                </div>
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="row text-center w-100">
                        <div class="col-md-3 col-6 mb-4 mb-md-0">
                            <div class="p-3 bg-light rounded-4">
                                <i class="fas fa-file-contract text-primary mb-2 fs-4"></i>
                                <div class="activity-number fw-bold text-dark">{{ $actividadReciente['cotizaciones'] ?? 0 }}</div>
                                <div class="text-muted small fw-semibold text-uppercase mt-1">Cotizaciones</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-4 mb-md-0">
                            <div class="p-3 bg-light rounded-4">
                                <i class="fas fa-hand-holding-usd text-success mb-2 fs-4"></i>
                                <div class="activity-number fw-bold text-dark">{{ $actividadReciente['ventas'] ?? 0 }}</div>
                                <div class="text-muted small fw-semibold text-uppercase mt-1">Ventas Exitosas</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="p-3 bg-light rounded-4">
                                <i class="fas fa-clipboard-check text-warning mb-2 fs-4"></i>
                                <div class="activity-number fw-bold text-dark">{{ $actividadReciente['ordenes'] ?? 0 }}</div>
                                <div class="text-muted small fw-semibold text-uppercase mt-1">Órdenes</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="p-3 bg-light rounded-4">
                                <i class="fas fa-user-friends text-info mb-2 fs-4"></i>
                                <div class="activity-number fw-bold text-dark">{{ $actividadReciente['clientes'] ?? 0 }}</div>
                                <div class="text-muted small fw-semibold text-uppercase mt-1">Nuevos Clientes</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Notificaciones y Avisos -->
        <div class="col-lg-4">
            <div class="card dashboard-card h-100">
                <div class="card-header border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger bg-opacity-10 p-2 rounded-lg me-3">
                            <i class="fas fa-bell text-danger"></i>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark">Alertas del Día</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-start p-3 bg-light border-start border-danger border-4 rounded mb-3 shadow-sm transition hover:shadow-md cursor-pointer">
                        <div class="flex-shrink-0 mt-1">
                            <i class="fas fa-exclamation-circle text-danger fs-5"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small fw-bold text-dark">Stock Bajo o Crítico</div>
                            <div class="small text-muted mt-1">{{ $stockCritico ?? 0 }} artículos necesitan reposición en el sistema de inventario.</div>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start p-3 bg-light border-start border-info border-4 rounded mb-3 shadow-sm transition hover:shadow-md cursor-pointer">
                        <div class="flex-shrink-0 mt-1">
                            <i class="fas fa-calendar-check text-info fs-5"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small fw-bold text-dark">Citas Programadas</div>
                            <div class="small text-muted mt-1">{{ $citasPendientes ?? 0 }} vehículo(s) programados para ingresar al taller hoy.</div>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start p-3 bg-light border-start border-warning border-4 rounded shadow-sm transition hover:shadow-md cursor-pointer">
                        <div class="flex-shrink-0 mt-1">
                            <i class="fas fa-cog text-warning fs-5"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small fw-bold text-dark">Órdenes en Proceso</div>
                            <div class="small text-muted mt-1">{{ $ordenesPendientes ?? 0 }} orden(es) activas siendo trabajadas actualmente.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection