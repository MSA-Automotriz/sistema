@extends('admin.layouts.app')

@section('title', 'Detalle de Venta')

@push('styles')
<style>
    .estado-badge {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 0.8rem;
    }

    .estado-pendiente { background: #ffeaa7; color: #2d3436; }
    .estado-pagado { background: #00b894; color: white; }
    .estado-pendiente-stock { background: #fdcb6e; color: #2d3436; }
    .estado-en-compra { background: #74b9ff; color: white; }
    .estado-listo-entrega { background: #55a3ff; color: white; }
    .estado-despachado { background: #00cec9; color: white; }
    .estado-no-pagado { background: #e17055; color: white; }
    .estado-cancelado { background: #636e72; color: white; }

    .action-btn {
        margin: 0.25rem;
    }

    .historial-estados {
        max-height: 300px;
        overflow-y: auto;
    }

    .historial-item {
        border-left: 3px solid #007bff;
        padding-left: 1rem;
        margin-bottom: 1rem;
        position: relative;
    }

    .historial-item::before {
        content: '';
        position: absolute;
        left: -6px;
        top: 0.5rem;
        width: 9px;
        height: 9px;
        background: #007bff;
        border-radius: 50%;
    }
</style>
@endpush

@section('content')
<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-receipt text-info me-2"></i> Detalle POS
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6 text-shadow-sm d-flex align-items-center">
                    Venta #{{ $venta->codigo }}
                </h2>
                <p class="text-white-50 mb-0">Revisión de productos, cobros y despachos.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.ventas.pos.ventas') }}" class="btn bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0">
                    <i class="fas fa-arrow-left text-primary me-2"></i> Volver al Listado
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-lg-4 position-relative" style="top: -3.5rem; z-index: 10;">

    <div class="row">
        <!-- Información General -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-receipt me-2"></i>Información de la Venta
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Datos Básicos -->
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>Código:</strong></td>
                                    <td>{{ $venta->codigo }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Fecha:</strong></td>
                                    <td>{{ $venta->fecha->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Cliente:</strong></td>
                                    <td>{{ $ventaData['cliente_nombre'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Documento:</strong></td>
                                    <td>{{ $ventaData['cliente_documento'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Usuario:</strong></td>
                                    <td>{{ $ventaData['usuario_nombre'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Almacén:</strong></td>
                                    <td>{{ $ventaData['almacen_nombre'] }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Estado y Pagos -->
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>Estado:</strong></td>
                                    <td>
                                        <span class="estado-badge estado-{{ strtolower(str_replace(' ', '-', $venta->estado)) }}">
                                            {{ $venta->estado }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Tipo de Pago:</strong></td>
                                    <td>{{ $venta->tipo_pago }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total:</strong></td>
                                    <td><strong>{{ $venta->moneda === 'Dólares' ? '$' : 'S/' }} {{ number_format($venta->total, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Abonado:</strong></td>
                                    <td>{{ $venta->moneda === 'Dólares' ? '$' : 'S/' }} {{ number_format($venta->monto_abonado, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Saldo Pendiente:</strong></td>
                                    <td>
                                        <span class="{{ $venta->saldo_pendiente > 0 ? 'text-danger' : 'text-success' }}">
                                            {{ $venta->moneda === 'Dólares' ? '$' : 'S/' }} {{ number_format($venta->saldo_pendiente, 2) }}
                                        </span>
                                    </td>
                                </tr>
                                @if($venta->fecha_despacho)
                                <tr>
                                    <td><strong>Fecha Despacho:</strong></td>
                                    <td>{{ $venta->fecha_despacho->format('d/m/Y') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    @if($venta->observaciones)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Observaciones:</h6>
                            <div class="alert alert-info">
                                {!! nl2br(e($venta->observaciones)) !!}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Detalles de Productos -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-box me-2"></i>Productos
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Descuento</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ventaData['detalles'] as $detalle)
                                <tr>
                                    <td>{{ $detalle['item_codigo'] }}</td>
                                    <td>{{ $detalle['item_nombre'] }}</td>
                                    <td>{{ $detalle['cantidad'] }}</td>
                                    <td>{{ $venta->moneda === 'Dólares' ? '$' : 'S/' }} {{ number_format($detalle['precio_unitario'], 2) }}</td>
                                    <td>{{ $detalle['descuento'] }}%</td>
                                    <td>{{ $venta->moneda === 'Dólares' ? '$' : 'S/' }} {{ number_format($detalle['total'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-end">Subtotal:</th>
                                    <th>{{ $venta->moneda === 'Dólares' ? '$' : 'S/' }} {{ number_format($venta->subtotal, 2) }}</th>
                                </tr>
                                <tr>
                                    <th colspan="5" class="text-end">IGV:</th>
                                    <th>{{ $venta->moneda === 'Dólares' ? '$' : 'S/' }} {{ number_format($venta->igv, 2) }}</th>
                                </tr>
                                <tr>
                                    <th colspan="5" class="text-end">Total:</th>
                                    <th>{{ $venta->moneda === 'Dólares' ? '$' : 'S/' }} {{ number_format($venta->total, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de Acciones y Estado -->
        <div class="col-md-4">
            <!-- Acciones de Estado -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>Acciones
                    </h5>
                </div>
                <div class="card-body">
                    @if($venta->puedeMarcarseListaEntrega())
                        <button type="button" class="btn btn-success btn-sm action-btn w-100"
                                onclick="marcarListaEntrega({{ $venta->id }})">
                            <i class="fas fa-check me-2"></i>Marcar Listo para Entrega
                        </button>
                    @endif

                    @if($venta->puedeDespacharse())
                        <button type="button" class="btn btn-primary btn-sm action-btn w-100"
                                onclick="marcarDespachada({{ $venta->id }})">
                            <i class="fas fa-truck me-2"></i>Marcar como Despachada
                        </button>
                    @endif

                    @if($venta->saldo_pendiente > 0)
                        <button type="button" class="btn btn-warning btn-sm action-btn w-100"
                                onclick="registrarPago({{ $venta->id }})">
                            <i class="fas fa-dollar-sign me-2"></i>Registrar Pago
                        </button>
                    @endif

                    <a href="{{ route('admin.ventas.pos.ventas.imprimir', $venta->id) }}"
                       class="btn btn-outline-secondary btn-sm action-btn w-100" target="_blank">
                        <i class="fas fa-print me-2"></i>Imprimir
                    </a>
                </div>
            </div>

            <!-- Historial de Estados -->
            @if($venta->detalle_estados)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>Historial de Estados
                    </h5>
                </div>
                <div class="card-body">
                    <div class="historial-estados">
                        @foreach(array_reverse($venta->detalle_estados) as $historial)
                        <div class="historial-item">
                            <div class="d-flex justify-content-between">
                                <strong>{{ ucfirst(str_replace('_', ' ', $historial['estado_nuevo'])) }}</strong>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($historial['fecha'])->format('d/m/Y H:i') }}
                                </small>
                            </div>
                            @if($historial['comentario'])
                                <small class="text-muted">{{ $historial['comentario'] }}</small>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modales para acciones -->
<div class="modal fade" id="pagoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="pagoForm">
                    <input type="hidden" id="venta_id" value="{{ $venta->id }}">
                    <div class="mb-3">
                        <label class="form-label">Monto a Pagar</label>
                        <input type="number" class="form-control" id="monto"
                               max="{{ $venta->saldo_pendiente }}" step="0.01" required>
                        <small class="text-muted">Saldo pendiente: {{ $venta->moneda === 'Dólares' ? '$' : 'S/' }} {{ number_format($venta->saldo_pendiente, 2) }}</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Referencia</label>
                        <input type="text" class="form-control" id="referencia" placeholder="Número de operación, etc.">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observaciones" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="procesarPago()">Registrar Pago</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="comentarioModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="comentarioModalTitle">Agregar Comentario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <textarea class="form-control" id="comentarioTexto" rows="3" placeholder="Ingrese un comentario..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmarAccion">Confirmar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let accionPendiente = null;

function marcarListaEntrega(ventaId) {
    accionPendiente = { tipo: 'lista_entrega', ventaId: ventaId };
    document.getElementById('comentarioModalTitle').textContent = 'Marcar como Listo para Entrega';
    new bootstrap.Modal(document.getElementById('comentarioModal')).show();
}

function marcarDespachada(ventaId) {
    accionPendiente = { tipo: 'despachada', ventaId: ventaId };
    document.getElementById('comentarioModalTitle').textContent = 'Marcar como Despachada';
    new bootstrap.Modal(document.getElementById('comentarioModal')).show();
}

function registrarPago(ventaId) {
    new bootstrap.Modal(document.getElementById('pagoModal')).show();
}

document.getElementById('confirmarAccion').addEventListener('click', function() {
    if (!accionPendiente) return;

    const comentario = document.getElementById('comentarioTexto').value;
    const { tipo, ventaId } = accionPendiente;

    let url = '';
    if (tipo === 'lista_entrega') {
        url = `{{ route('admin.ventas.pos.ventas.marcar-lista-entrega', '') }}/${ventaId}`;
    } else if (tipo === 'despachada') {
        url = `{{ route('admin.ventas.pos.ventas.marcar-despachada', '') }}/${ventaId}`;
    }

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ comentario: comentario })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
            setTimeout(() => window.location.reload(), 1000);
        } else {
            toastr.error(data.message);
        }
        bootstrap.Modal.getInstance(document.getElementById('comentarioModal')).hide();
    })
    .catch(error => {
        toastr.error('Error al procesar la solicitud');
        bootstrap.Modal.getInstance(document.getElementById('comentarioModal')).hide();
    });
});

function procesarPago() {
    const datos = {
        venta_id: document.getElementById('venta_id').value,
        monto: document.getElementById('monto').value,
        referencia: document.getElementById('referencia').value,
        observaciones: document.getElementById('observaciones').value
    };

    fetch('{{ route("admin.ventas.pos.ventas.registrar-pago") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(datos)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
            setTimeout(() => window.location.reload(), 1000);
        } else {
            toastr.error(data.message);
        }
        bootstrap.Modal.getInstance(document.getElementById('pagoModal')).hide();
    })
    .catch(error => {
        toastr.error('Error al registrar el pago');
        bootstrap.Modal.getInstance(document.getElementById('pagoModal')).hide();
    });
}

// Configurar toastr
toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "timeOut": "3000"
};
</script>
@endpush