@extends('admin.layouts.app')

@section('title', 'Detalle de Orden de Compra')

@section('content')
<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-file-invoice text-info me-2"></i> Detalle de Orden
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6 text-shadow-sm d-flex align-items-center">
                    Orden #{{ $orden->codigo }}
                </h2>
                <div class="d-flex align-items-center mt-2">
                    @if($orden->estado == 'en espera')
                        <span class="badge rounded-pill bg-warning text-dark px-3 py-2 fw-bold shadow-sm">
                            <i class="fas fa-clock me-1"></i> EN ESPERA
                        </span>
                    @elseif($orden->estado == 'aprobada')
                        <span class="badge rounded-pill bg-success px-3 py-2 fw-bold shadow-sm">
                            <i class="fas fa-check-circle me-1"></i> APROBADA
                        </span>
                    @elseif($orden->estado == 'rechazada')
                        <span class="badge rounded-pill bg-danger px-3 py-2 fw-bold shadow-sm">
                            <i class="fas fa-times-circle me-1"></i> RECHAZADA
                        </span>
                    @endif
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @if($orden->estado == 'en espera')
                    <form action="{{ route('admin.compras.ordenes.aprobar', $orden) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn bg-success text-white rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0" onclick="return confirm('¿Estás seguro de aprobar esta orden?')">
                            <i class="fas fa-check me-2"></i> Aprobar
                        </button>
                    </form>
                    <form action="{{ route('admin.compras.ordenes.rechazar', $orden) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn bg-danger text-white rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0" onclick="return confirm('¿Estás seguro de rechazar esta orden?')">
                            <i class="fas fa-times me-2"></i> Rechazar
                        </button>
                    </form>
                    <a href="{{ route('admin.compras.ordenes.edit', $orden) }}" class="btn bg-warning text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0">
                        <i class="fas fa-edit me-2"></i> Editar
                    </a>
                @endif
                <button onclick="window.print()" class="btn bg-info text-white rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0">
                    <i class="fas fa-print me-2"></i> Imprimir
                </button>
                <a href="{{ route('admin.compras.ordenes.index') }}" class="btn bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0">
                    <i class="fas fa-arrow-left text-primary me-2"></i> Volver
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-lg-4 position-relative" style="top: -3.5rem; z-index: 10;">
<div class="row">
    <div class="col-12">
        <div class="card dashboard-card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <!-- Información General -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3" :class="darkMode ? 'text-light' : 'text-dark'">Información de la Orden</h5>
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Código</dt>
                            <dd class="col-sm-8">{{ $orden->codigo }}</dd>
                            
                            <dt class="col-sm-4">Requerimiento</dt>
                            <dd class="col-sm-8">
                                <a href="{{ url('admin/compras/requerimientos/' . $orden->requerimiento_id) }}">
                                    #{{ $orden->requerimiento_id }}
                                </a>
                            </dd>
                            
                            <dt class="col-sm-4">Fecha de Creación</dt>
                            <dd class="col-sm-8">{{ $orden->created_at->format('d/m/Y H:i') }}</dd>
                            
                            <dt class="col-sm-4">Almacén Destino</dt>
                            <dd class="col-sm-8">{{ $orden->almacen->nombre ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-4">Requerido por</dt>
                            <dd class="col-sm-8">{{ $orden->usuario->name ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-4">Moneda</dt>
                            <dd class="col-sm-8">{{ $orden->moneda }}</dd>
                        </dl>
                    </div>
                    
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3" :class="darkMode ? 'text-light' : 'text-dark'">Información del Proveedor</h5>
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Proveedor</dt>
                            <dd class="col-sm-8">{{ $orden->proveedor->razon_social ?? 'No especificado' }}</dd>
                            
                            <dt class="col-sm-4">RUC/DNI</dt>
                            <dd class="col-sm-8">{{ $orden->proveedor->numero_documento ?? 'No especificado' }}</dd>
                            
                            @if($orden->estado != 'en espera')
                                <dt class="col-sm-4">Aprobado por</dt>
                                <dd class="col-sm-8">{{ $orden->aprobador->name ?? 'N/A' }}</dd>
                                
                                <dt class="col-sm-4">Fecha de Aprobación</dt>
                                <dd class="col-sm-8">{{ $orden->fecha_aprobacion ? date('d/m/Y', strtotime($orden->fecha_aprobacion)) : 'N/A' }}</dd>
                            @endif
                            
                            <dt class="col-sm-4">Observaciones</dt>
                            <dd class="col-sm-8">{{ $orden->observaciones ?: 'Sin observaciones' }}</dd>
                        </dl>
                    </div>
                </div>

                <!-- Detalles del Pedido -->
                <div class="mb-4">
                    <h5 class="fw-bold mb-3" :class="darkMode ? 'text-light' : 'text-dark'">Productos</h5>
                    <div class="table-responsive">
                        <table class="table table-hover" :class="darkMode ? 'table-dark' : ''">
                            <thead class="table-light" :class="darkMode ? 'table-dark' : ''">
                                <tr>
                                    <th class="small text-uppercase">Nro.</th>
                                    <th class="small text-uppercase">Código</th>
                                    <th class="small text-uppercase">Producto</th>
                                    <th class="small text-uppercase">Tipo</th>
                                    <th class="small text-uppercase">Cant. Requerida</th>
                                    <th class="small text-uppercase">Cant. Compra</th>
                                    <th class="small text-uppercase">Unidad</th>
                                    <th class="small text-uppercase">Precio</th>
                                    <th class="small text-uppercase">Descuento</th>
                                    <th class="small text-uppercase">Total</th>
                                    <th class="small text-uppercase">IGV</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orden->detalles as $index => $detalle)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $detalle->codigo }}</td>
                                        <td>{{ $detalle->nombre_producto }}</td>
                                        <td>{{ ucfirst($detalle->tipo_item) }}</td>
                                        <td>{{ $detalle->cantidad_requerida }}</td>
                                        <td>{{ $detalle->cantidad_en_compra }}</td>
                                        <td>{{ $detalle->unidad }}</td>
                                        <td>{{ $orden->moneda }} {{ number_format($detalle->precio_compra, 2) }}</td>
                                        <td>{{ $orden->moneda }} {{ number_format($detalle->descuento, 2) }}</td>
                                        <td>{{ $orden->moneda }} {{ number_format($detalle->total, 2) }}</td>
                                        <td>{{ $detalle->afecto_igv ? 'Sí' : 'No' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-3">No hay productos en esta orden.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="9" class="text-end"><strong>Total:</strong></td>
                                    <td colspan="2"><strong>{{ $orden->moneda }} {{ number_format($orden->total, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

    </div>
</div>
</div>
</div>
@endsection

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .card, .card * {
            visibility: visible;
        }
        .card {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .btn, .btn-group, form button {
            display: none !important;
        }
    }
</style>