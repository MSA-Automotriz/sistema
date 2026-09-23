@extends('admin.layouts.app')

@section('title', 'Ventas POS')

@push('styles')
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
<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-cash-register text-info me-2"></i> Ventas del Punto de Venta
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6 text-shadow-sm">Historial de Ventas</h2>
                <p class="text-white-50 mb-0">Gestión, cobro y consulta de ventas realizadas en mostrador.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.ventas.pos.index') }}" class="btn bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0">
                    <i class="fas fa-plus text-primary me-2"></i> Nueva Venta
                </a>
                <button type="button" class="btn bg-white bg-opacity-25 text-white rounded-pill px-4 py-2 fw-bold shadow-sm backdrop-blur transition hover:bg-opacity-30 border border-white border-opacity-25" onclick="exportarVentas()">
                    <i class="fas fa-download me-2"></i> Exportar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-lg-4 position-relative" style="top: -3.5rem; z-index: 10;">

    <!-- Filtros -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="filtros-form" class="row g-3">
                        <div class="col-md-3">
                            <label for="fecha_desde" class="form-label">Fecha Desde</label>
                            <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" value="{{ date('Y-m-01') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="fecha_hasta" class="form-label">Fecha Hasta</label>
                            <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" id="estado" name="estado">
                                <option value="">Todos los estados</option>
                                <option value="Completada">Completada</option>
                                <option value="Parcial">Parcial</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="Anulada">Anulada</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="almacen_id" class="form-label">Almacén</label>
                            <select class="form-select" id="almacen_id" name="almacen_id">
                                <option value="">Todos los almacenes</option>
                                @foreach($almacenes as $almacen)
                                    <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="buscar" class="form-label">Buscar</label>
                            <input type="text" class="form-control" id="buscar" name="buscar" placeholder="Código, cliente, usuario...">
                        </div>
                        <div class="col-md-3">
                            <label for="moneda" class="form-label">Moneda</label>
                            <select class="form-select" id="moneda" name="moneda">
                                <option value="">Todas</option>
                                <option value="Soles">Soles</option>
                                <option value="Dólares">Dólares</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search mr-1"></i>
                                Filtrar
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="limpiarFiltros()">
                                <i class="fas fa-times mr-1"></i>
                                Limpiar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen -->
    <div class="row mb-3" id="resumen-ventas">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="total-ventas">0</h4>
                            <small>Total Ventas</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="monto-total">S/. 0.00</h4>
                            <small>Monto Total</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="ventas-parciales">0</h4>
                            <small>Ventas Parciales</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="saldo-pendiente">S/. 0.00</h4>
                            <small>Saldo Pendiente</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Ventas -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tabla-ventas">
                            <thead class="table-dark">
                                <tr>
                                    <th>Código</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Usuario</th>
                                    <th>Almacén</th>
                                    <th>Total</th>
                                    <th>Abonado</th>
                                    <th>Saldo</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="ventas-tbody">
                                <!-- Contenido cargado dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginación -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div id="info-paginacion">
                            Mostrando 0 de 0 resultados
                        </div>
                        <nav aria-label="Paginación">
                            <ul class="pagination mb-0" id="paginacion">
                                <!-- Botones de paginación -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ver Detalle -->
<div class="modal fade" id="modalDetalle" tabindex="-1" aria-labelledby="modalDetalleLabel" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetalleLabel">Detalle de Venta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detalle-content">
                <!-- Contenido cargado dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="imprimirVenta()">
                    <i class="fas fa-print mr-1"></i>
                    Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Registrar Pago -->
<div class="modal fade" id="modalPago" tabindex="-1" aria-labelledby="modalPagoLabel" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPagoLabel">Registrar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-pago">
                <div class="modal-body">
                    <input type="hidden" id="venta-id-pago" name="venta_id">
                    
                    <div class="mb-3">
                        <label for="monto-pago" class="form-label">Monto a Pagar</label>
                        <input type="number" class="form-control" id="monto-pago" name="monto" step="0.01" min="0.01" required>
                        <div class="form-text">Saldo pendiente: <span id="saldo-disponible">S/. 0.00</span></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="referencia-pago" class="form-label">Referencia</label>
                        <input type="text" class="form-control" id="referencia-pago" name="referencia" placeholder="Número de operación, voucher, etc.">
                    </div>
                    
                    <div class="mb-3">
                        <label for="comentario-pago" class="form-label">Comentario</label>
                        <textarea class="form-control" id="comentario-pago" name="comentario" rows="2" placeholder="Comentario adicional (opcional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-money-bill-wave mr-1"></i>
                        Registrar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
console.log('🔍 FRONTEND: Script cargado, verificando jQuery...');

if (typeof jQuery === 'undefined') {
    console.error('🔍 FRONTEND: ERROR - jQuery no está disponible!');
    alert('Error: jQuery no está cargado');
} else {
    console.log('🔍 FRONTEND: jQuery disponible, versión:', jQuery.fn.jquery);
}

let currentPage = 1;
let ventaActual = null;

$(document).ready(function() {
    console.log('🔍 FRONTEND: DOM ready, iniciando carga de ventas...');
    console.log('🔍 FRONTEND: Verificando elementos del DOM...');
    console.log('🔍 FRONTEND: #ventas-tbody existe?', $('#ventas-tbody').length > 0);
    console.log('🔍 FRONTEND: #fecha_desde existe?', $('#fecha_desde').length > 0);

    cargarVentas();

    // Eventos
    $('#filtros-form').on('submit', function(e) {
        e.preventDefault();
        currentPage = 1;
        cargarVentas();
    });
    
    $('#form-pago').on('submit', function(e) {
        e.preventDefault();
        registrarPago();
    });
});

function cargarVentas(page = 1) {
    console.log('🔍 FRONTEND: cargarVentas iniciado, página:', page);
    currentPage = page;

    const filtros = {
        page: page,
        fecha_desde: $('#fecha_desde').val(),
        fecha_hasta: $('#fecha_hasta').val(),
        estado: $('#estado').val(),
        almacen_id: $('#almacen_id').val(),
        buscar: $('#buscar').val(),
        moneda: $('#moneda').val()
    };

    console.log('🔍 FRONTEND: Filtros enviados:', filtros);

    $.ajax({
        url: '{{ route("admin.ventas.pos.ventas") }}',
        method: 'GET',
        data: filtros,
        beforeSend: function() {
            console.log('🔍 FRONTEND: Enviando petición AJAX...');
            $('#ventas-tbody').html('<tr><td colspan="10" class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando...</td></tr>');
        },
        success: function(response) {
            console.log('🔍 FRONTEND: Respuesta recibida exitosamente:', response);
            renderizarVentas(response.ventas);
            renderizarPaginacion(response.pagination);
            actualizarResumen(response.resumen);
            actualizarInfoPaginacion(response.pagination);
        },
        error: function(xhr, status, error) {
            console.error('🔍 FRONTEND: Error en petición AJAX:', {xhr, status, error});
            console.error('🔍 FRONTEND: Response text:', xhr.responseText);
            $('#ventas-tbody').html('<tr><td colspan="10" class="text-center text-danger">Error al cargar las ventas</td></tr>');
        }
    });
}

function renderizarVentas(ventas) {
    console.log('🔍 FRONTEND: renderizarVentas iniciado, ventas recibidas:', ventas);
    let html = '';

    if (!ventas || ventas.length === 0) {
        console.log('🔍 FRONTEND: No hay ventas para mostrar');
        html = '<tr><td colspan="10" class="text-center">No se encontraron ventas</td></tr>';
    } else {
        console.log('🔍 FRONTEND: Renderizando', ventas.length, 'ventas');
        ventas.forEach(function(venta) {
            const estadoClass = getEstadoClass(venta.estado);
            const monedaSymbol = venta.moneda === 'Dólares' ? '$' : 'S/.';
            
            html += `
                <tr>
                    <td>
                        <strong>${venta.codigo}</strong>
                        ${venta.cotizacion_codigo ? `<br><small class="text-muted">Cot: ${venta.cotizacion_codigo}</small>` : ''}
                    </td>
                    <td>
                        ${formatearFecha(venta.fecha)}
                        <br><small class="text-muted">${formatearHora(venta.fecha)}</small>
                    </td>
                    <td>
                        <strong>${venta.cliente_nombre}</strong>
                        <br><small class="text-muted">${venta.cliente_documento}</small>
                    </td>
                    <td>${venta.usuario_nombre}</td>
                    <td>${venta.almacen_nombre}</td>
                    <td>
                        <strong>${monedaSymbol} ${parseFloat(venta.total).toFixed(2)}</strong>
                    </td>
                    <td>
                        ${monedaSymbol} ${parseFloat(venta.monto_abonado).toFixed(2)}
                        ${venta.porcentaje_abonado < 100 ? `<br><small class="text-muted">${venta.porcentaje_abonado}%</small>` : ''}
                    </td>
                    <td>
                        ${parseFloat(venta.saldo_pendiente) > 0 ? 
                            `<span class="text-danger">${monedaSymbol} ${parseFloat(venta.saldo_pendiente).toFixed(2)}</span>` : 
                            `<span class="text-success">${monedaSymbol} 0.00</span>`
                        }
                    </td>
                    <td><span class="badge ${estadoClass}">${venta.estado}</span></td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('admin.ventas.pos.ventas.show', '') }}/${venta.id}" class="btn btn-outline-primary" title="Ver detalle" target="_blank">
                                <i class="fas fa-eye"></i>
                            </a>
                            ${parseFloat(venta.saldo_pendiente) > 0 ?
                                `<button type="button" class="btn btn-outline-success" onclick="registrarPagoModal(${venta.id})" title="Registrar pago">
                                    <i class="fas fa-money-bill-wave"></i>
                                </button>` : ''
                            }
                            <button type="button" class="btn btn-outline-info" title="Imprimir" onclick="imprimirVentaDirecta(${venta.id})">
                                <i class="fas fa-print"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#ventas-tbody').html(html);
}

function renderizarPaginacion(pagination) {
    let html = '';
    
    // Botón anterior
    html += `<li class="page-item ${pagination.current_page <= 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="cargarVentas(${pagination.current_page - 1})">Anterior</a>
    </li>`;
    
    // Números de página
    const startPage = Math.max(1, pagination.current_page - 2);
    const endPage = Math.min(pagination.last_page, pagination.current_page + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
            <a class="page-link" href="#" onclick="cargarVentas(${i})">${i}</a>
        </li>`;
    }
    
    // Botón siguiente
    html += `<li class="page-item ${pagination.current_page >= pagination.last_page ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="cargarVentas(${pagination.current_page + 1})">Siguiente</a>
    </li>`;
    
    $('#paginacion').html(html);
}

function actualizarResumen(resumen) {
    $('#total-ventas').text(resumen.total_ventas);
    $('#monto-total').text(`S/. ${parseFloat(resumen.monto_total).toFixed(2)}`);
    $('#ventas-parciales').text(resumen.ventas_parciales);
    $('#saldo-pendiente').text(`S/. ${parseFloat(resumen.saldo_pendiente).toFixed(2)}`);
}

function actualizarInfoPaginacion(pagination) {
    const desde = ((pagination.current_page - 1) * pagination.per_page) + 1;
    const hasta = Math.min(pagination.current_page * pagination.per_page, pagination.total);
    $('#info-paginacion').text(`Mostrando ${desde} a ${hasta} de ${pagination.total} resultados`);
}

function verDetalle(ventaId) {
    $.ajax({
        url: `{{ route('admin.ventas.pos.ventas.show', '') }}/${ventaId}`,
        method: 'GET',
        beforeSend: function() {
            $('#detalle-content').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>');
        },
        success: function(response) {
            $('#detalle-content').html(response.html);
            $('#modalDetalle').modal('show');
            ventaActual = ventaId;
        },
        error: function() {
            $('#detalle-content').html('<div class="text-center text-danger">Error al cargar el detalle</div>');
        }
    });
}

function registrarPagoModal(ventaId) {
    $.ajax({
        url: `{{ route('admin.ventas.pos.ventas.show', '') }}/${ventaId}`,
        method: 'GET',
        success: function(response) {
            $('#venta-id-pago').val(ventaId);
            $('#saldo-disponible').text(`S/. ${parseFloat(response.venta.saldo_pendiente).toFixed(2)}`);
            $('#monto-pago').attr('max', parseFloat(response.venta.saldo_pendiente));
            $('#monto-pago').val('');
            $('#referencia-pago').val('');
            $('#comentario-pago').val('');
            $('#modalPago').modal('show');
        }
    });
}

function registrarPago() {
    const formData = $('#form-pago').serialize();
    
    $.ajax({
        url: '{{ route("admin.ventas.pos.ventas.registrar-pago") }}',
        method: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#modalPago').modal('hide');
                cargarVentas(currentPage);
                toastr.success(response.message);
            } else {
                toastr.error(response.message);
            }
        },
        error: function(xhr) {
            toastr.error('Error al registrar el pago');
        }
    });
}

function imprimirVenta(ventaId = null) {
    const id = ventaId || ventaActual;
    if (id) {
        const baseUrl = '{{ route("admin.ventas.pos.ventas") }}';
        window.open(`${baseUrl}/imprimir/${id}`, '_blank');
    }
}

function imprimirVentaDirecta(ventaId) {
    const baseUrl = '{{ route("admin.ventas.pos.ventas") }}';
    window.open(`${baseUrl}/imprimir/${ventaId}`, '_blank');
}

function exportarVentas() {
    const filtros = {
        fecha_desde: $('#fecha_desde').val(),
        fecha_hasta: $('#fecha_hasta').val(),
        estado: $('#estado').val(),
        almacen_id: $('#almacen_id').val(),
        buscar: $('#buscar').val(),
        moneda: $('#moneda').val()
    };
    
    const params = new URLSearchParams(filtros);
    window.open(`{{ route('admin.ventas.pos.ventas.exportar') }}?${params}`, '_blank');
}

function limpiarFiltros() {
    $('#filtros-form')[0].reset();
    $('#fecha_desde').val('{{ date("Y-m-01") }}');
    $('#fecha_hasta').val('{{ date("Y-m-d") }}');
    currentPage = 1;
    cargarVentas();
}

function getEstadoClass(estado) {
    switch(estado) {
        case 'Completada': return 'bg-success';
        case 'Parcial': return 'bg-warning';
        case 'Pendiente': return 'bg-info';
        case 'Anulada': return 'bg-danger';
        default: return 'bg-secondary';
    }
}

function formatearFecha(fecha) {
    return new Date(fecha).toLocaleDateString('es-PE');
}

function formatearHora(fecha) {
    return new Date(fecha).toLocaleTimeString('es-PE', {hour: '2-digit', minute: '2-digit'});
}
</script>
@endpush