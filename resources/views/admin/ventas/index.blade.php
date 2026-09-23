{{-- resources/views/admin/ventas/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Gestión de Ventas')

@push('styles')
<style>
.estado-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.estado-pagado { background: linear-gradient(45deg, #10b981, #34d399); color: white; }
.estado-no_pagado { background: linear-gradient(45deg, #ef4444, #f87171); color: white; }
.estado-pendiente { background: linear-gradient(45deg, #f59e0b, #fbbf24); color: white; }
.estado-despachado { background: linear-gradient(45deg, #8b5cf6, #a78bfa); color: white; }
.estado-en_cotizacion { background: linear-gradient(45deg, #06b6d4, #22d3ee); color: white; }
.estado-para_importacion { background: linear-gradient(45deg, #ec4899, #f472b6); color: white; }
.estado-pedido_especial { background: linear-gradient(45deg, #6366f1, #818cf8); color: white; }
.estado-cancelado { background: linear-gradient(45deg, #64748b, #94a3b8); color: white; }

.priority-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.7rem;
}

.priority-alta { background: #fecaca; color: #dc2626; }
.priority-media { background: #fde68a; color: #d97706; }
.priority-baja { background: #d1fae5; color: #059669; }
.priority-urgente { background: #f3e8ff; color: #7c3aed; }

.card-stats {
    transition: transform 0.15s ease-in-out;
    cursor: pointer;
}

.card-stats:hover {
    transform: translateY(-2px);
}

.table-responsive {
    border-radius: 0.5rem;
    overflow: hidden;
}

.btn-action {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
    border-radius: 0.25rem;
}

.vencido {
    background: linear-gradient(45deg, #dc2626, #ef4444);
    color: white;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.filter-section {
    background: #f8f9fa;
    border-radius: 0.5rem;
    padding: 1.5rem;
    margin-bottom: 2rem;
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
                    <i class="fas fa-chart-line text-info me-2"></i> Módulo de Finanzas
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6">Gestión de Ventas</h2>
                <p class="text-white-50 mb-0">Dashboard general de comprobantes, recaudación y cuentas por cobrar</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.ventas.pos.index') }}" class="btn bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105" style="border: 1px solid rgba(255,255,255,0.8);">
                    <i class="fas fa-plus me-2 text-primary"></i> Nueva Venta
                </a>
                <button type="button" class="btn bg-white bg-opacity-10 text-white rounded-pill px-4 py-2 fw-bold border border-white border-opacity-25 backdrop-blur transition hover:scale-105" onclick="exportarVentas()">
                    <i class="fas fa-download me-2 text-success"></i> Exportar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-lg-4 position-relative" style="top: -3.5rem; z-index: 10;">

    <!-- Estadísticas Principales Flotantes -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card border-0 shadow-sm h-100 card-stats transition hover:scale-105 cursor-pointer" onclick="filtrarPorEstado('')">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted mb-1 small fw-bold tracking-wider">TOTAL VENTAS</div>
                            <div class="h3 mb-0 text-dark fw-bold" id="total-ventas">-</div>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                            <i class="fas fa-shopping-cart fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card border-0 shadow-sm h-100 card-stats transition hover:scale-105 cursor-pointer" onclick="filtrarPorEstado('pagado')">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted mb-1 small fw-bold tracking-wider">PAGADAS</div>
                            <div class="h3 mb-0 text-success fw-bold" id="ventas-pagadas">-</div>
                            <div class="small fw-semibold text-success bg-success bg-opacity-10 rounded-pill px-2 py-1 mt-1 d-inline-block" id="monto-pagadas">-</div>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                            <i class="fas fa-check-circle fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card border-0 shadow-sm h-100 card-stats transition hover:scale-105 cursor-pointer" onclick="filtrarPorEstado('no_pagado')">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted mb-1 small fw-bold tracking-wider">PENDIENTES</div>
                            <div class="h3 mb-0 text-warning fw-bold" id="ventas-pendientes">-</div>
                            <div class="small fw-semibold text-warning bg-warning bg-opacity-10 rounded-pill px-2 py-1 mt-1 d-inline-block" id="monto-pendientes">-</div>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning">
                            <i class="fas fa-clock fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card border-0 shadow-sm h-100 card-stats transition hover:scale-105 cursor-pointer" onclick="mostrarVencidas()">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted mb-1 small fw-bold tracking-wider">VENCIDAS</div>
                            <div class="h3 mb-0 text-danger fw-bold" id="ventas-vencidas">-</div>
                            <div class="small fw-semibold text-danger bg-danger bg-opacity-10 rounded-pill px-2 py-1 mt-1 d-inline-block" id="monto-vencidas">-</div>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded-circle text-danger">
                            <i class="fas fa-exclamation-triangle fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card dashboard-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-3">
                <i class="fas fa-filter text-primary me-2"></i>
                <h6 class="mb-0 fw-bold text-dark">Filtros de búsqueda</h6>
            </div>
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Estado</label>
                    <select class="form-select bg-light border-light shadow-none" id="filtro-estado">
                        <option value="">Todos los estados</option>
                        <option value="pagado">Pagado</option>
                        <option value="no_pagado">No Pagado</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="despachado">Despachado</option>
                        <option value="en_cotizacion">En Cotización</option>
                        <option value="para_importacion">Para Importación</option>
                        <option value="pedido_especial">Pedido Especial</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Cliente</label>
                    <select class="form-select bg-light border-light shadow-none" id="filtro-cliente">
                        <option value="">Todos los clientes</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombres }} {{ $cliente->apellido_paterno }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Desde</label>
                    <input type="date" class="form-control bg-light border-light shadow-none" id="fecha-desde">
                </div>

                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Hasta</label>
                    <input type="date" class="form-control bg-light border-light shadow-none" id="fecha-hasta">
                </div>

                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Moneda</label>
                    <select class="form-select bg-light border-light shadow-none" id="filtro-moneda">
                        <option value="">Todas</option>
                        <option value="Soles">Soles</option>
                        <option value="Dólares">Dólares</option>
                    </select>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="aplicarFiltros()">
                                Buscar
                            </button>
                            <button class="btn btn-light text-muted rounded-pill px-4" onclick="limpiarFiltros()">
                                Limpiar
                            </button>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <button type="button" class="btn btn-outline-info rounded-pill px-3 py-1 btn-sm fw-bold" onclick="mostrarCuentasPorCobrar()">
                                <i class="fas fa-money-check-alt me-1 text-info"></i> CxC
                            </button>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" id="auto-refresh">
                                <label class="form-check-label small text-muted" for="auto-refresh">
                                    Auto-actualizar
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Ventas -->
    <div class="card dashboard-card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom-0 pt-4 pb-2 px-4">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark">Registros de Venta</h6>
                <div class="d-flex align-items-center py-1 px-3 bg-light rounded-pill">
                    <span class="small text-muted fw-bold" id="total-registros">Cargando...</span>
                    <div class="spinner-border spinner-border-sm text-primary ms-2 d-none" id="loading-spinner" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-borderless table-hover align-middle mb-0" id="tabla-ventas">
                    <thead class="bg-light bg-opacity-75" style="border-bottom: 1px solid #f1f5f9;">
                        <tr>
                            <th class="px-3 py-3 text-muted small fw-bold text-uppercase">Comprobante</th>
                            <th class="px-3 py-3 text-muted small fw-bold text-uppercase">Cliente</th>
                            <th class="px-3 py-3 text-muted small fw-bold text-uppercase">Realización</th>
                            <th class="px-3 py-3 text-muted small fw-bold text-uppercase">Total</th>
                            <th class="px-3 py-3 text-muted small fw-bold text-uppercase">Estado</th>
                            <th class="px-3 py-3 text-muted small fw-bold text-uppercase">Liquidación</th>
                            <th class="px-3 py-3 text-muted small fw-bold text-uppercase">Vencimiento</th>
                            <th class="px-4 py-3 text-muted small fw-bold text-uppercase text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-ventas">
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <div class="mt-2 text-muted fw-semibold">Consultando ventas...</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white border-top-0 d-flex justify-content-center mt-3 pb-4">
            <nav id="pagination-container">
                <!-- Paginación se carga dinámicamente -->
            </nav>
        </div>
    </div>
</div>

<!-- Modal para Registrar Pago -->
<div class="modal fade" id="pagoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-money-bill-wave me-2"></i>Registrar Pago
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-pago">
                <div class="modal-body">
                    <!-- Información de la venta -->
                    <div class="alert alert-info mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Venta:</strong> <span id="pago-venta-codigo">-</span><br>
                                <strong>Cliente:</strong> <span id="pago-cliente">-</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Total:</strong> <span id="pago-total">-</span><br>
                                <strong>Saldo Pendiente:</strong> <span id="pago-saldo" class="fw-bold text-danger">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Monto del Pago</label>
                            <input type="number" class="form-control" name="monto" step="0.01" min="0.01" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Moneda</label>
                            <select class="form-select" name="moneda" required>
                                <option value="PEN">Soles (S/)</option>
                                <option value="USD">Dólares (US$)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Método de Pago</label>
                            <select class="form-select" name="metodo_pago" required>
                                <option value="efectivo">Efectivo</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="deposito">Depósito</option>
                                <option value="cheque">Cheque</option>
                                <option value="tarjeta_credito">Tarjeta de Crédito</option>
                                <option value="tarjeta_debito">Tarjeta de Débito</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Fecha del Pago</label>
                            <input type="date" class="form-control" name="fecha_pago" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Referencia</label>
                            <input type="text" class="form-control" name="referencia_pago" placeholder="Nro. de operación, cheque, etc.">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Banco</label>
                            <input type="text" class="form-control" name="banco" placeholder="Banco (opcional)">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Observaciones</label>
                            <textarea class="form-control" name="observaciones" rows="3" placeholder="Observaciones adicionales..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i>Registrar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Ver Pagos -->
<div class="modal fade" id="verPagosModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-list me-2"></i>Historial de Pagos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenido-pagos">
                <!-- Contenido se carga dinámicamente -->
            </div>
        </div>
    </div>
</div>

<!-- Container para notificaciones toast -->
<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3"></div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Variables globales
    let currentPage = 1;
    let currentFilters = {};
    let autoRefreshInterval = null;
    let selectedVentaId = null;

    // Inicializar
    initializePage();

    function initializePage() {
        // Establecer fecha por defecto (último mes)
        const fechaHasta = new Date().toISOString().split('T')[0];
        const fechaDesde = new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
        
        $('#fecha-desde').val(fechaDesde);
        $('#fecha-hasta').val(fechaHasta);
        
        // Cargar datos iniciales
        cargarEstadisticas();
        cargarVentas();
        cargarClientes();
        
        // Configurar auto-refresh
        $('#auto-refresh').on('change', function() {
            if ($(this).is(':checked')) {
                autoRefreshInterval = setInterval(() => {
                    cargarEstadisticas();
                    cargarVentas();
                }, 30000);
            } else {
                if (autoRefreshInterval) {
                    clearInterval(autoRefreshInterval);
                }
            }
        });
    }

    // Cargar estadísticas
    function cargarEstadisticas() {
        $.ajax({
            url: '{{ route("admin.ventas.index") }}',
            data: { ...currentFilters, estadisticas: true },
            success: function(response) {
                if (response.estadisticas) {
                    $('#total-ventas').text(response.estadisticas.total);
                    $('#ventas-pagadas').text(response.estadisticas.pagadas);
                    $('#monto-pagadas').text(response.estadisticas.monto_pagadas);
                    $('#ventas-pendientes').text(response.estadisticas.pendientes);
                    $('#monto-pendientes').text(response.estadisticas.monto_pendientes);
                    $('#ventas-vencidas').text(response.estadisticas.vencidas);
                    $('#monto-vencidas').text(response.estadisticas.monto_vencidas);
                }
            }
        });
    }

    // Cargar ventas
    function cargarVentas(page = 1) {
        $('#loading-spinner').removeClass('d-none');
        
        $.ajax({
            url: '{{ route("admin.ventas.index") }}',
            data: { ...currentFilters, page: page },
            success: function(response) {
                if (response.ventas) {
                    renderVentas(response.ventas.data);
                    renderPagination(response.ventas);
                    $('#total-registros').text(`${response.ventas.total} registros encontrados`);
                }
            },
            complete: function() {
                $('#loading-spinner').addClass('d-none');
            }
        });
    }

    // Renderizar tabla de ventas
    function renderVentas(ventas) {
        const tbody = $('#tbody-ventas');
        tbody.empty();

        if (ventas.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-search fa-3x mb-3"></i>
                            <div>No se encontraron ventas con los filtros aplicados</div>
                        </div>
                    </td>
                </tr>
            `);
            return;
        }

        ventas.forEach(venta => {
            const estadoClass = `estado-${venta.estado}`;
            const prioridadClass = `priority-${venta.prioridad}`;
            const esVencida = venta.esta_vencida ? 'vencido' : '';
            
            tbody.append(`
                <tr class="${esVencida}">
                    <td class="px-3 py-3">
                        <div class="fw-bold">${venta.codigo}</div>
                        ${venta.numero_factura ? `<small class="text-muted">F: ${venta.numero_factura}</small>` : ''}
                    </td>
                    <td class="px-3 py-3">
                        <div>${venta.cliente.nombres} ${venta.cliente.apellido_paterno || ''}</div>
                        <small class="text-muted">${venta.cliente.documento_identidad}</small>
                    </td>
                    <td class="px-3 py-3">
                        <div>${new Date(venta.fecha).toLocaleDateString('es-PE')}</div>
                        <small class="text-muted">${venta.usuario.name}</small>
                    </td>
                    <td class="px-3 py-3">
                        <div class="fw-bold">${venta.moneda === 'Dólares' ? 'US$' : 'S/'} ${parseFloat(venta.total).toFixed(2)}</div>
                        ${venta.tipo_cambio_usado ? `<small class="text-muted">TC: ${venta.tipo_cambio_usado}</small>` : ''}
                    </td>
                    <td class="px-3 py-3">
                        <span class="estado-badge ${estadoClass}">${getEstadoLabel(venta.estado)}</span>
                        <div class="mt-1">
                            <span class="priority-badge ${prioridadClass}">${venta.prioridad}</span>
                        </div>
                    </td>
                    <td class="px-3 py-3">
                        ${renderInfoPagos(venta)}
                    </td>
                    <td class="px-3 py-3">
                        ${renderInfoVencimiento(venta)}
                    </td>
                    <td class="px-3 py-3">
                        <div class="dropdown">
                            <button class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-cog"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="verDetalleVenta(${venta.id})">
                                    <i class="fas fa-eye me-2"></i>Ver Detalle
                                </a></li>
                                ${venta.saldo_pendiente > 0 ? `
                                <li><a class="dropdown-item" href="#" onclick="registrarPago(${venta.id})">
                                    <i class="fas fa-money-bill-wave me-2"></i>Registrar Pago
                                </a></li>` : ''}
                                <li><a class="dropdown-item" href="#" onclick="verPagos(${venta.id})">
                                    <i class="fas fa-list me-2"></i>Ver Pagos
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#" onclick="cambiarEstado(${venta.id})">
                                    <i class="fas fa-edit me-2"></i>Cambiar Estado
                                </a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            `);
        });
    }

    function renderInfoPagos(venta) {
        if (venta.tipo_pago === 'Contado') {
            return '<span class="badge bg-success">Contado</span>';
        } else {
            const porcentaje = venta.total > 0 ? ((venta.monto_abonado / venta.total) * 100).toFixed(1) : 0;
            return `
                <div class="small">
                    <div class="d-flex justify-content-between">
                        <span>Abonado:</span>
                        <span class="fw-bold">${venta.moneda === 'Dólares' ? 'US$' : 'S/'} ${parseFloat(venta.monto_abonado).toFixed(2)}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Saldo:</span>
                        <span class="fw-bold text-danger">${venta.moneda === 'Dólares' ? 'US$' : 'S/'} ${parseFloat(venta.saldo_pendiente).toFixed(2)}</span>
                    </div>
                    <div class="progress mt-1" style="height: 4px;">
                        <div class="progress-bar" style="width: ${porcentaje}%"></div>
                    </div>
                </div>
            `;
        }
    }

    function renderInfoVencimiento(venta) {
        if (!venta.fecha_vencimiento) {
            return '<span class="text-muted">Sin vencimiento</span>';
        }

        const fechaVenc = new Date(venta.fecha_vencimiento);
        const hoy = new Date();
        const diff = Math.ceil((fechaVenc - hoy) / (1000 * 60 * 60 * 24));

        let clase = 'text-muted';
        let texto = '';

        if (diff < 0) {
            clase = 'text-danger fw-bold';
            texto = `Vencida hace ${Math.abs(diff)} días`;
        } else if (diff <= 7) {
            clase = 'text-warning fw-bold';
            texto = `Vence en ${diff} días`;
        } else {
            texto = `Vence en ${diff} días`;
        }

        return `
            <div class="${clase}">
                <div>${fechaVenc.toLocaleDateString('es-PE')}</div>
                <small>${texto}</small>
            </div>
        `;
    }

    function getEstadoLabel(estado) {
        const labels = {
            'pagado': 'Pagado',
            'no_pagado': 'No Pagado',
            'pendiente': 'Pendiente',
            'despachado': 'Despachado',
            'en_cotizacion': 'En Cotización',
            'para_importacion': 'Para Importación',
            'pedido_especial': 'Pedido Especial',
            'cancelado': 'Cancelado'
        };
        return labels[estado] || estado;
    }

    // Aplicar filtros
    window.aplicarFiltros = function() {
        currentFilters = {
            estado: $('#filtro-estado').val(),
            cliente_id: $('#filtro-cliente').val(),
            fecha_desde: $('#fecha-desde').val(),
            fecha_hasta: $('#fecha-hasta').val(),
            moneda: $('#filtro-moneda').val()
        };
        
        currentPage = 1;
        cargarEstadisticas();
        cargarVentas();
    };

    // Limpiar filtros
    window.limpiarFiltros = function() {
        $('#filtro-estado').val('');
        $('#filtro-cliente').val('');
        $('#fecha-desde').val('');
        $('#fecha-hasta').val('');
        $('#filtro-moneda').val('');
        
        currentFilters = {};
        currentPage = 1;
        cargarEstadisticas();
        cargarVentas();
    };

    // Registrar pago
    window.registrarPago = function(ventaId) {
        selectedVentaId = ventaId;
        
        // Cargar datos de la venta
        $.ajax({
            url: `/admin/ventas/${ventaId}/pagos`,
            success: function(response) {
                if (response.success) {
                    const venta = response.venta;
                    $('#pago-venta-codigo').text(venta.codigo);
                    $('#pago-total').text(`${venta.moneda === 'Dólares' ? 'US$' : 'S/'} ${parseFloat(venta.total).toFixed(2)}`);
                    $('#pago-saldo').text(`${venta.moneda === 'Dólares' ? 'US$' : 'S/'} ${parseFloat(venta.saldo_pendiente).toFixed(2)}`);
                    
                    // Establecer fecha actual
                    $('input[name="fecha_pago"]').val(new Date().toISOString().split('T')[0]);
                    
                    // Establecer moneda por defecto
                    $('select[name="moneda"]').val(venta.moneda === 'Dólares' ? 'USD' : 'PEN');
                    
                    $('#pagoModal').modal('show');
                }
            }
        });
    };

    // Procesar formulario de pago
    $('#form-pago').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        $.ajax({
            url: `/admin/ventas/${selectedVentaId}/pagos`,
            method: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#pagoModal').modal('hide');
                    mostrarNotificacion('Pago registrado exitosamente', 'success');
                    cargarEstadisticas();
                    cargarVentas();
                    $('#form-pago')[0].reset();
                } else {
                    mostrarNotificacion(response.message || 'Error al registrar el pago', 'error');
                }
            },
            error: function(xhr) {
                let message = 'Error al registrar el pago';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                mostrarNotificacion(message, 'error');
            }
        });
    });

    // Funciones auxiliares
    function mostrarNotificacion(mensaje, tipo) {
        const alertClass = tipo === 'success' ? 'alert-success' : 'alert-danger';
        const icon = tipo === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
        
        const toast = $(`
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="${icon} me-2"></i>
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('#toast-container').append(toast);
        
        setTimeout(() => {
            toast.alert('close');
        }, 5000);
    }

    // Otras funciones (placeholder)
    window.filtrarPorEstado = function(estado) {
        $('#filtro-estado').val(estado);
        aplicarFiltros();
    };

    window.mostrarVencidas = function() {
        // Implementar filtro por vencidas
        currentFilters.vencidas = '1';
        cargarVentas();
    };

    window.mostrarCuentasPorCobrar = function() {
        // Implementar modal de cuentas por cobrar
        alert('Funcionalidad de cuentas por cobrar en desarrollo');
    };

    window.exportarVentas = function() {
        // Implementar exportación
        alert('Funcionalidad de exportación en desarrollo');
    };
});
</script>
@endpush