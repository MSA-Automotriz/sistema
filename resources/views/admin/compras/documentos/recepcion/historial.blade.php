@extends('admin.layouts.app')
@section('title', 'Historial de Recepciones')

@section('content')
<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-history text-info me-2"></i> Auditoría
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6 text-shadow-sm d-flex align-items-center">
                    Historial de Recepciones
                </h2>
                <p class="text-white-50 mb-0">Registro completo de todas las recepciones realizadas.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.recepcion.index') }}" class="btn bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0">
                    <i class="fas fa-arrow-left text-primary me-2"></i> Volver a Recepciones
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-lg-4 position-relative" style="top: -3.5rem; z-index: 10;">
    <div class="card dashboard-card border-0 shadow-sm mb-4">
        <div class="card-body p-0">

    <!-- Main Content -->
    <div class="card border-0 shadow-lg" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px); border-radius: 24px;">
        <div class="card-header bg-transparent border-0 p-4">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0 text-primary fw-bold">
                    <i class="fas fa-clipboard-list me-3"></i>
                    Registro Detallado
                </h3>
                <span class="badge bg-primary px-3 py-2 fs-6">
                    Total: {{ $recepciones->count() }} registros
                </span>
            </div>
        </div>
        
        <div class="card-body p-0">
            @if($recepciones->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="py-4 px-4">
                                    <i class="fas fa-calendar-day me-2 text-primary"></i>Fecha
                                </th>
                                <th class="py-4 px-4">
                                    <i class="fas fa-file-invoice me-2 text-primary"></i>Orden
                                </th>
                                <th class="py-4 px-4">
                                    <i class="fas fa-cube me-2 text-primary"></i>Producto
                                </th>
                                <th class="py-4 px-4 text-center">
                                    <i class="fas fa-tag me-2 text-primary"></i>Tipo
                                </th>
                                <th class="py-4 px-4 text-center">
                                    <i class="fas fa-plus-circle me-2 text-primary"></i>Cantidad
                                </th>
                                <th class="py-4 px-4">
                                    <i class="fas fa-user me-2 text-primary"></i>Recibido Por
                                </th>
                                <th class="py-4 px-4">
                                    <i class="fas fa-comment-dots me-2 text-primary"></i>Observaciones
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recepciones as $recepcion)
                            <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                                <td class="py-4 px-4">
                                    <div class="card text-black text-center d-inline-block" 
                                         style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                                                border-radius: 16px; min-width: 120px; border: none;">
                                        <div class="card-body p-3">
                                            <div class="fs-4 fw-bold">{{ \Carbon\Carbon::parse($recepcion->fecha_recepcion)->format('d') }}</div>
                                            <div class="small opacity-75 text-uppercase">{{ \Carbon\Carbon::parse($recepcion->fecha_recepcion)->format('M Y') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="rounded-2 d-flex align-items-center justify-content-center text-dark fw-bold" 
                                                 style="width: 60px; height: 60px; background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                                                {{ strtoupper(substr($recepcion->detalleOrdenCompra->ordenCompra->codigo, 0, 2)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <h5 class="mb-1 text-primary fw-bold">
                                                #{{ $recepcion->detalleOrdenCompra->ordenCompra->codigo }}
                                            </h5>
                                            <small class="text-muted">
                                                {{ $recepcion->detalleOrdenCompra->ordenCompra->proveedor->nombre ?? 'Sin proveedor' }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="card border-0" style="background: rgba(255, 255, 255, 0.8); border-radius: 16px;">
                                        <div class="card-body p-3">
                                            <h6 class="mb-2 fw-bold text-dark">
                                                {{ $recepcion->detalleOrdenCompra->nombre_producto }}
                                            </h6>
                                            <small class="text-muted">
                                                <i class="fas fa-barcode me-1"></i>
                                                {{ $recepcion->detalleOrdenCompra->codigo }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center py-4 px-4">
                                    <span class="badge px-3 py-2 text-uppercase fw-bold" 
                                          style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); 
                                                 color: #8b4513; border-radius: 50px; font-size: 0.8rem;">
                                        {{ ucfirst($recepcion->detalleOrdenCompra->tipo_item) }}
                                    </span>
                                </td>
                                <td class="text-center py-4 px-4">
                                    <div class="card text-center d-inline-block border-0" 
                                         style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); 
                                                border-radius: 50px; min-width: 80px;">
                                        <div class="card-body p-2">
                                            <h4 class="mb-0 fw-bold" style="color: #2d5016;">
                                                {{ $recepcion->cantidad_recibida }}
                                            </h4>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="d-flex align-items-center card border-0 p-3" 
                                         style="background: rgba(255, 255, 255, 0.6); border-radius: 16px;">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-black fw-bold" 
                                                 style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                {{ strtoupper(substr($recepcion->recibidoPor->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-bold">{{ $recepcion->recibidoPor->name }}</h6>
                                            <small class="text-muted">{{ $recepcion->recibidoPor->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    @if($recepcion->observaciones)
                                        <div class="card border-0 p-3" 
                                             style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.7) 100%); 
                                                    border-radius: 12px; font-style: italic; color: #4a5568;">
                                            <i class="fas fa-quote-left me-2"></i>
                                            {{ $recepcion->observaciones }}
                                            <i class="fas fa-quote-right ms-2"></i>
                                        </div>
                                    @else
                                        <div class="text-center text-muted">
                                            <i class="fas fa-minus-circle fa-2x mb-2"></i>
                                            <small class="d-block">Sin observaciones</small>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5" 
                     style="background: linear-gradient(135deg, rgba(240, 147, 251, 0.1) 0%, rgba(245, 87, 108, 0.1) 100%); 
                            border-radius: 0 0 24px 24px;">
                    <i class="fas fa-inbox text-primary mb-4" style="font-size: 5rem; opacity: 0.6;"></i>
                    <h2 class="mb-3 text-primary">No hay recepciones registradas</h2>
                    <p class="fs-5 text-muted">Aún no se han realizado recepciones en el sistema</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    @if($recepciones->count() > 0)
    <div class="row mt-5">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 text-center h-100" 
                 style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(20px); 
                        border-radius: 20px; color: black;">
                <div class="card-body p-4">
                    <i class="fas fa-clipboard-check fa-3x mb-3 opacity-75"></i>
                    <h2 class="fw-bold mb-2" style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);">
                        {{ $recepciones->count() }}
                    </h2>
                    <p class="mb-0 text-uppercase fw-bold opacity-75">Total Recepciones</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 text-center h-100" 
                 style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(20px); 
                        border-radius: 20px; color: black;">
                <div class="card-body p-4">
                    <i class="fas fa-cubes fa-3x mb-3 opacity-75"></i>
                    <h2 class="fw-bold mb-2" style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);">
                        {{ $recepciones->sum('cantidad_recibida') }}
                    </h2>
                    <p class="mb-0 text-uppercase fw-bold opacity-75">Items Recibidos</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 text-center h-100" 
                 style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(20px); 
                        border-radius: 20px; color: black;">
                <div class="card-body p-4">
                    <i class="fas fa-file-alt fa-3x mb-3 opacity-75"></i>
                    <h2 class="fw-bold mb-2" style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);">
                        {{ $recepciones->pluck('detalleOrdenCompra.orden_compra_id')->unique()->count() }}
                    </h2>
                    <p class="mb-0 text-uppercase fw-bold opacity-75">Órdenes Procesadas</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 text-center h-100" 
                 style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(20px); 
                        border-radius: 20px; color: black;">
                <div class="card-body p-4">
                    <i class="fas fa-calendar-week fa-3x mb-3 opacity-75"></i>
                    <h2 class="fw-bold mb-2" style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);">
                        {{ $recepciones->where('fecha_recepcion', '>=', now()->startOfMonth())->count() }}
                    </h2>
                    <p class="mb-0 text-uppercase fw-bold opacity-75">Este Mes</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Trends Chart -->
    <div class="card border-0 mt-5" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px); border-radius: 24px;">
        <div class="card-header bg-transparent border-0 p-4">
            <h3 class="mb-0 text-primary fw-bold">
                <i class="fas fa-chart-line me-3"></i>
                Tendencia de Recepciones
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div style="width: 20px; height: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%;"></div>
                        </div>
                        <div>
                            <strong>Últimos 7 días:</strong>
                            <span class="badge bg-primary ms-2">{{ $recepciones->where('fecha_recepcion', '>=', now()->subDays(7))->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div style="width: 20px; height: 20px; background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); border-radius: 50%;"></div>
                        </div>
                        <div>
                            <strong>Últimos 30 días:</strong>
                            <span class="badge bg-success ms-2">{{ $recepciones->where('fecha_recepcion', '>=', now()->subDays(30))->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div style="width: 20px; height: 20px; background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); border-radius: 50%;"></div>
                        </div>
                        <div>
                            <strong>Promedio/día:</strong>
                            <span class="badge bg-warning ms-2">{{ $recepciones->count() > 0 ? round($recepciones->count() / max(1, now()->diffInDays($recepciones->first()->created_at ?? now())), 1) : 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Activity -->
    @if($recepciones->count() > 0)
    <div class="card border-0 mt-5" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px); border-radius: 24px;">
        <div class="card-header bg-transparent border-0 p-4">
            <h3 class="mb-0 text-primary fw-bold">
                <i class="fas fa-clock me-3"></i>
                Actividad Reciente
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($recepciones->take(3) as $recepcion)
                <div class="col-md-4 mb-3">
                    <div class="card border-0 h-100" style="background: rgba(255, 255, 255, 0.6); border-radius: 16px;">
                        <div class="card-body p-3 d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-black fw-bold" 
                                     style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    {{ strtoupper(substr($recepcion->recibidoPor->name, 0, 1)) }}
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold">{{ $recepcion->recibidoPor->name }}</h6>
                                <small class="text-muted d-block">
                                    Recibió {{ $recepcion->cantidad_recibida }} items
                                </small>
                                <small class="text-primary">
                                    {{ \Carbon\Carbon::parse($recepcion->fecha_recepcion)->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection