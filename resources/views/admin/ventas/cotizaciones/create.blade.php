@extends('admin.layouts.app')

@section('title', 'Nueva Cotización')

@section('header')
@endsection

@section('content')
<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-file-invoice text-info me-2"></i> Módulo de Ventas
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6 d-flex align-items-center">Crear Nueva Cotización</h2>
                <p class="text-white-50 mb-0">Complete la información general y asigne los elementos requeridos.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.ventas.cotizaciones.index') }}" class="btn bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105" style="border: 1px solid rgba(255,255,255,0.8);">
                    <i class="fas fa-arrow-left me-2 text-primary"></i> Volver al Directorio
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-lg-4 position-relative" style="top: -3.5rem; z-index: 10;">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card dashboard-card border-0 shadow-sm">
                <div class="card-body p-3 p-md-4">
                    <form method="POST" action="{{ route('admin.ventas.cotizaciones.store') }}" id="form-cotizacion" novalidate>
                        @csrf
                        <!--input type="hidden" name="tipo_cotizacion" id="tipo_cotizacion" value=""-->
                        <input type="hidden" name="tipo_cotizacion" id="tipo_cotizacion" value="vehiculos">
                        <!-- Sección Cliente -->
                        <div class="mb-4">
                            <fieldset class="border p-3 rounded">
                                <legend class="w-auto fs-6 text-primary fw-semibold mb-0">Información del Cliente</legend>
                                
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-8">
                                        <label for="cliente_search" class="form-label small required">Buscar cliente</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="cliente_search" placeholder="Ingrese RUC/DNI o nombre" autocomplete="off">
                                            <input type="hidden" name="cliente_id" id="cliente_id" required>
                                        </div>
                                        <div id="cliente_info" class="mt-2 small"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-outline-light text-dark w-100" 
                                            data-bs-toggle="modal" data-bs-target="#modalNuevoCliente">
                                            <i class="fas fa-plus-circle me-1"></i> Nuevo Cliente
                                        </button>
                                    </div>
                                </div>
                            </fieldset>
                        </div>

                        <!-- Datos Generales -->
                        <div class="mb-4">
                            <fieldset class="border p-3 rounded">
                                <legend class="w-auto fs-6 text-primary fw-semibold mb-0">Configuración Principal</legend>
                                
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="almacen_id" class="form-label small required">Almacén</label>
                                        <select name="almacen_id" id="almacen_id" class="form-select form-select-sm" required>
                                            <option value="">Seleccionar...</option>
                                            @foreach($almacenes as $almacen)
                                                <option value="{{ $almacen->id }}" @selected(old('almacen_id') == $almacen->id)>
                                                    {{ $almacen->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('almacen_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label small required">Condición</label>
                                        <div class="btn-group w-100 shadow-sm" role="group">
                                            <input type="radio" class="btn-check" name="condicion" id="condicion_nuevo" 
                                                value="Nuevo" checked>
                                            <label class="btn btn-outline-primary" for="condicion_nuevo">
                                                <i class="fas fa-certificate me-1"></i> Nuevo
                                            </label>
                                            <input type="radio" class="btn-check" name="condicion" id="condicion_usado" 
                                                value="Usado">
                                            <label class="btn btn-outline-primary" for="condicion_usado">
                                                <i class="fas fa-history me-1"></i> Usado
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="canal" class="form-label small required">Canal</label>
                                        <select name="canal" id="canal" class="form-select form-select-sm" required>
                                            <option value="">Seleccionar...</option>
                                            @foreach($canales as $canal)
                                                <option value="{{ $canal }}" @selected(old('canal') == $canal)>
                                                    {{ $canal }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('canal')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label small required">Moneda</label>
                                        <div class="btn-group w-100 shadow-sm" role="group">
                                            <input type="radio" class="btn-check" name="moneda" id="moneda_soles" 
                                                value="Soles">
                                            <label class="btn btn-outline-warning" for="moneda_soles">
                                                S/ Soles
                                            </label>
                                            <input type="radio" class="btn-check" name="moneda" id="moneda_dolares" 
                                                value="Dólares" checked>
                                            <label class="btn btn-outline-warning" for="moneda_dolares">
                                                $ Dólares
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label small required">Forma de Pago</label>
                                        <div class="btn-group w-100 shadow-sm" role="group">
                                            <input type="radio" class="btn-check" name="forma_pago" id="forma_pago_contado" 
                                                value="Contado" checked>
                                            <label class="btn btn-outline-success" for="forma_pago_contado">
                                                <i class="fas fa-money-bill-wave me-1"></i> Contado
                                            </label>
                                            <input type="radio" class="btn-check" name="forma_pago" id="forma_pago_credito" 
                                                value="Crédito">
                                            <label class="btn btn-outline-success" for="forma_pago_credito">
                                                <i class="fas fa-credit-card me-1"></i> Crédito
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="fecha_validez" class="form-label small">Validez</label>
                                        <input type="date" name="fecha_validez" id="fecha_validez" 
                                            class="form-control form-control-sm" 
                                            value="{{ old('fecha_validez', now()->addDays(30)->format('Y-m-d')) }}">
                                    </div>

                                    <div class="col-12">
                                        <label for="datos_adicionales" class="form-label small">Notas Adicionales</label>
                                        <textarea name="datos_adicionales" id="datos_adicionales" 
                                            class="form-control form-control-sm" rows="2"
                                            placeholder="Ingrese comentarios adicionales...">{{ old('datos_adicionales') }}</textarea>
                                    </div>
                                </div>
                            </fieldset>
                        </div>

                        <!-- Selector de Tipo -->
                        <div class="d-grid gap-2 d-md-flex">
                            <!--button type="button" class="btn btn-type-select shadow-sm" data-tipo="repuestos">
                                <i class="fas fa-cogs me-2"></i> Partes - Repuestos
                            </button-->
                            <button type="button" class="btn btn-type-select shadow-sm active" data-tipo="vehiculos">
                                <i class="fas fa-car me-2"></i> Vehículos
                            </button>
                            <!--button type="button" class="btn btn-type-select shadow-sm" data-tipo="servicios">
                                <i class="fas fa-tools me-2"></i> Servicios
                            </button-->
                        </div>

                        <!-- Secciones Dinámicas -->
                        <div id="section-repuestos" class="cotizacion-section mb-4" style="display: none;">
                            @include('admin.ventas.cotizaciones.partials.repuestos')
                        </div>
                        <div id="section-vehiculos" class="cotizacion-section mb-4" style="display: none;">
                            @include('admin.ventas.cotizaciones.partials.vehiculos')
                        </div>
                        <div id="section-servicios" class="cotizacion-section mb-4" style="display: none;">
                            @include('admin.ventas.cotizaciones.partials.servicios')
                        </div>

                        <!-- Ítems Agregados -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="text-primary mb-0">Detalle de Ítems</h6>
                                <span class="badge bg-primary rounded-pill fs-6" id="contador-items">0</span>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="10%">Tipo</th>
                                            <th width="45%">Descripción</th>
                                            <th width="10%">Cantidad</th>
                                            <th width="15%">P. Unitario</th>
                                            <th width="15%">Total</th>
                                            <th width="5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="items-tabla">
                                        <tr id="no-items">
                                            <td colspan="6" class="text-center py-3 text-muted small">
                                                <i class="fas fa-box-open fa-2x mb-2"></i>
                                                <div>No se han agregado ítems</div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="bg-light p-2 rounded">
                                <div class="row fw-semibold">
                                    <div class="col-md-4">Subtotal: <span id="subtotal-moneda">$</span> <span id="subtotal-valor">0.00</span></div>
                                    <div class="col-md-4">IGV (18%): <span id="igv-moneda">$</span> <span id="igv-valor">0.00</span></div>
                                    <div class="col-md-4 text-primary">Total: <span id="total-moneda">$</span> <span id="total-valor">0.00</span></div>
                                </div>
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.ventas.cotizaciones.index') }}" 
                               class="btn btn-secondary btn-sm px-4">
                                <i class="fas fa-times me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary btn-sm px-4">
                                <i class="fas fa-save me-1"></i> Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuevo Cliente -->
<div class="modal fade" id="modalNuevoCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title fs-6 mb-0">Registrar Nuevo Cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="iframe-nuevo-cliente" src="{{ route('admin.clientes.create') }}" 
                    style="width: 100%; height: 500px; border: none;"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet">
<style>
    .required::after {
        content: "*";
        color: #dc3545;
        margin-left: 3px;
    }
    
    .btn-type-select {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        color: #495057;
        transition: all 0.2s;
    }
    
    .btn-type-select.active {
        background: var(--bs-primary);
        color: white;
        border-color: var(--bs-primary);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }
    
    .select2-container--default .select2-selection--single {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        height: 38px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    
    .table-sm th, .table-sm td {
        padding: 0.5rem;
    }
    
    .cotizacion-section {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 1rem;
        background: #f8f9fa;
    }
    
    .input-group {
        position: relative;
    }

    #resultados_clientes {
        max-height: 250px;
        overflow-y: auto;
        width: 100%;
    }

    .cliente-item {
        border-bottom: 1px solid #f0f0f0;
    }

    .cliente-item:hover {
        background-color: #f8f9fa;
    }

    .is-invalid.btn-type-select {
        border-color: #dc3545;
        background-color: #f8d7da;
    }

    .is-invalid#items-tabla {
        border: 1px solid #dc3545;
        border-radius: 0.375rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script>
// Variables globales
var itemsAgregados = [];

// Funciones globales
function agregarItem(tipo, descripcion, cantidad, precioUnitario, id, unidad = '') {
    // Validaciones
    if (!descripcion || !cantidad || !precioUnitario) {
        toastr.error('Complete todos los campos del ítem', 'Error');
        return false;
    }

    cantidad = parseFloat(cantidad);
    precioUnitario = parseFloat(precioUnitario);

    if (isNaN(cantidad) || cantidad <= 0) {
        toastr.error('Cantidad inválida', 'Error');
        return false;
    }

    if (isNaN(precioUnitario) || precioUnitario <= 0) {
        toastr.error('Precio inválido', 'Error');
        return false;
    }

    const total = cantidad * precioUnitario;
    const itemId = Date.now() + Math.floor(Math.random() * 1000);
    
    itemsAgregados.push({
        id: itemId,
        tipo: tipo,
        descripcion: descripcion,
        cantidad: cantidad,
        precioUnitario: precioUnitario,
        total: total,
        itemId: id,
        unidad: unidad
    });
    
    actualizarTablaItems();
    recalcularTotales();
    toastr.success('Ítem agregado', 'Éxito');
    return true;
}

function actualizarTablaItems() {
    const $tabla = $('#items-tabla');
    const simbolo = $('input[name="moneda"]:checked').val() === 'Soles' ? 'S/ ' : 'US$ ';
    
    $tabla.empty();
    
    if (itemsAgregados.length === 0) {
        $tabla.append(`
            <tr id="no-items">
                <td colspan="6" class="text-center py-4 text-muted">
                    <i class="fas fa-box-open fa-2x mb-2"></i>
                    <div>No se han agregado ítems</div>
                </td>
            </tr>
        `);
    } else {
        itemsAgregados.forEach((item, index) => {
            $tabla.append(`
                <tr data-id="${item.id}">
                    <td>
                        <span class="badge bg-${getBadgeColor(item.tipo)} rounded-pill">
                            ${item.tipo.charAt(0).toUpperCase() + item.tipo.slice(1)}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <span>${item.descripcion}</span>
                            ${item.unidad ? `<small class="text-muted">Unidad: ${item.unidad}</small>` : ''}
                        </div>
                    </td>
                    <td class="text-end">${item.cantidad}</td>
                    <td class="text-end">${simbolo}${item.precioUnitario.toFixed(2)}</td>
                    <td class="text-end fw-semibold">${simbolo}${item.total.toFixed(2)}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger eliminar-item"
                                data-id="${item.id}" title="Eliminar">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        <input type="hidden" name="items[${index}][tipo]" value="${item.tipo}">
                        <input type="hidden" name="items[${index}][item_id]" value="${item.itemId}">
                        ${item.tipo === 'vehiculos' ? `<input type="hidden" name="items[${index}][vehiculo_catalogo_id]" value="${item.vehiculo_catalogo_id || item.itemId}">` : ''}
                        ${item.tipo === 'vehiculos' && item.color_id ? `<input type="hidden" name="items[${index}][color_id]" value="${item.color_id}">` : ''}
                        <input type="hidden" name="items[${index}][descripcion]" value="${item.descripcion}">
                        <input type="hidden" name="items[${index}][cantidad]" value="${item.cantidad}">
                        <input type="hidden" name="items[${index}][precio_unitario]" value="${item.precioUnitario}">
                        ${item.unidad ? `<input type="hidden" name="items[${index}][unidad]" value="${item.unidad}">` : ''}
                        ${item.descuento ? `<input type="hidden" name="items[${index}][descuento]" value="${item.descuento}">` : ''}
                    </td>
                </tr>
            `);
        });
    }
    
    $('#contador-items').text(itemsAgregados.length);
}

function recalcularTotales() {
    const subtotal = itemsAgregados.reduce((sum, item) => sum + item.total, 0);
    const igv = subtotal * 0.18;
    const total = subtotal + igv;
    const simbolo = $('input[name="moneda"]:checked').val() === 'Soles' ? 'S/ ' : 'US$ ';
    
    $('#subtotal-moneda, #igv-moneda, #total-moneda').text(simbolo);
    $('#subtotal-valor').text(subtotal.toFixed(2));
    $('#igv-valor').text(igv.toFixed(2));
    $('#total-valor').text(total.toFixed(2));
}

function getBadgeColor(tipo) {
    const colores = {
        'repuestos': 'info',
        'vehiculos': 'primary',
        'servicios': 'success'
    };
    return colores[tipo] || 'secondary';
}

function eliminarItem(id) {
    Swal.fire({
        title: '¿Eliminar ítem?',
        text: "No podrás revertir esta acción",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            itemsAgregados = itemsAgregados.filter(item => item.id !== id);
            actualizarTablaItems();
            recalcularTotales();
            toastr.info('Ítem eliminado');
        }
    });
}

// Funciones para vehículos (globales)
function formatColor(color) {
    if (!color.id) return color.text;
    const $color = $(
        `<span><span class="color-badge me-2" style="background-color:${color.color || '#ccc'}; width: 15px; height: 15px; display: inline-block; border: 1px solid #ddd; border-radius: 3px;"></span>${color.text}</span>`
    );
    return $color;
}

function initSelect2Vehiculos() {
    // Select2 para Marcas
    $('.select2-marca').not('.select2-hidden-accessible').each(function() {
        $(this).select2({
            placeholder: 'Buscar marca',
            minimumInputLength: 0,
            ajax: {
                url: '/api/cotizaciones/marcas/search',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { term: params.term || '' };
                },
                processResults: function(data) {
                    console.log("Respuesta API marcas:", data);
                    if (Array.isArray(data)) {
                        return { results: data };
                    } else {
                        console.error("La respuesta no es un array:", data);
                        return { results: [] };
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error buscando marcas:", status, error);
                    return { results: [] };
                },
                cache: true
            }
        });
    });

    // Select2 para Colores
    $('.select2-color').not('.select2-hidden-accessible').each(function() {
        $(this).select2({
            placeholder: 'Buscar color',
            minimumInputLength: 0,
            ajax: {
                url: '/api/cotizaciones/colores/search',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { term: params.term || '' };
                },
                processResults: function(data) {
                    console.log("Respuesta API colores:", data);
                    if (Array.isArray(data)) {
                        return { results: data };
                    } else {
                        console.error("La respuesta no es un array:", data);
                        return { results: [] };
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error buscando colores:", status, error);
                    return { results: [] };
                },
                cache: true
            },
            templateResult: formatColor,
            templateSelection: formatColor
        });
    });

    // Conectar eventos para selects dependientes
    setupDependentSelects();
}

function setupDependentSelects() {
    // Asociar evento a las marcas (nuevas o existentes)
    $('.select2-marca').off('select2:select').on('select2:select', function(e) {
        const marcaId = e.params.data.id;
        const $row = $(this).closest('.vehiculos-row');
        const $modeloSelect = $row.find('.select2-modelo');
        
        console.log("Marca seleccionada:", e.params.data);
        
        $modeloSelect.val(null).trigger('change');
        $modeloSelect.prop('disabled', false).select2({
            placeholder: 'Buscar modelo',
            minimumInputLength: 0,
            ajax: {
                url: `/api/cotizaciones/marcas/${marcaId}/modelos`,
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { term: params.term || '' };
                },
                processResults: function(data) {
                    return { results: data };
                },
                error: function(xhr, status, error) {
                    console.error("Error buscando modelos:", status, error);
                    return { results: [] };
                },
                cache: true
            }
        });
        
        // Resetear selects dependientes
        $row.find('.select2-version').val(null).trigger('change').prop('disabled', true);
        $row.find('.select2-anio').val(null).trigger('change').prop('disabled', true);
    });
}

function initSelect2Repuestos() {
    $('.select2-repuestos').select2({
        placeholder: 'Buscar repuesto',
        minimumInputLength: 2,
        ajax: {
            url: '/api/repuestos/search',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return { term: params.term };
            },
            processResults: function(data) {
                console.log("Respuesta API repuestos:", data);
                
                if (data.error) {
                    console.error("Error API:", data.error);
                    return { results: [] };
                }
                
                if (Array.isArray(data)) {
                    return {
                        results: data.map(function(item) {
                            return {
                                id: item.id,
                                text: item.text || `${item.codigo} - ${item.nombre}`,
                                unidad: item.unidad || 'N/A',
                                precio: parseFloat(item.precio || item.precio_venta || 0)
                            };
                        })
                    };
                } else {
                    console.error("La respuesta no es un array:", data);
                    return { results: [] };
                }
            },
            cache: true
        }
    }).on('select2:select', function(e) {
        const data = e.params.data;
        const $row = $(this).closest('.repuestos-row');
        
        console.log("Repuesto seleccionado:", data);
        
        if (data) {
            $row.find('input[name$="[unidad]"]').val(data.unidad || 'N/A');
            $row.find('input[name$="[precio_unitario]"]').val(data.precio || 0);
        }
    });
}
</script>
<script>
$(document).on('select2:select', '.select2-modelo', function(e) {
    const modeloId = e.params.data.id;
    const $row = $(this).closest('.vehiculos-row');
    const $versionSelect = $row.find('.select2-version');
    
    console.log("Modelo seleccionado:", e.params.data);
    
    $versionSelect.val(null).trigger('change');
    $versionSelect.prop('disabled', false).select2({
        placeholder: 'Buscar versión',
        minimumInputLength: 0,
        ajax: {
            url: `/api/cotizaciones/modelos/${modeloId}/versiones`,
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return { term: params.term || '' };
            },
            processResults: function(data) {
                return { results: data };
            },
            error: function(xhr, status, error) {
                console.error("Error buscando versiones:", status, error);
                return { results: [] };
            },
            cache: true
        }
    });
    
    $row.find('.select2-anio').val(null).trigger('change').prop('disabled', true);
});

$(document).on('select2:select', '.select2-version', function(e) {
    const versionId = e.params.data.id;
    const $row = $(this).closest('.vehiculos-row');
    const $anioSelect = $row.find('.select2-anio');
    
    console.log("Versión seleccionada:", e.params.data);
    
    $anioSelect.val(null).trigger('change');
    $anioSelect.prop('disabled', false).select2({
        placeholder: 'Buscar año',
        minimumInputLength: 0,
        ajax: {
            url: `/api/cotizaciones/versiones/${versionId}/anios`,
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return { term: params.term || '' };
            },
            processResults: function(data) {
                return { results: data };
            },
            error: function(xhr, status, error) {
                console.error("Error buscando años:", status, error);
                return { results: [] };
            },
            cache: true
        }
    }).on('select2:select', function(e) {
        const precio = e.params.data.precio;
        if (precio) {
            $row.find('.precio').val(precio);
        }
    });
});

// Agregar nueva fila de vehículo
$(document).on('click', '.add-vehiculo-row', function() {
    const vehiculosCount = $('.vehiculos-row').length;
    const newIndex = vehiculosCount;
    
    const newRowHtml = `
        <div class="vehiculos-row mt-4 pt-3 border-top" id="vehiculos-row-${newIndex}">
            <div class="row g-2 mb-2">
                <div class="col-md-2">
                    <label class="form-label small">Categoría</label>
                    <select class="form-control form-control-sm categoria" name="vehiculos[${newIndex}][categoria]" id="categoria-${newIndex}">
                        <option value="menores">Menores</option>
                        <option value="livianos">Livianos</option>
                        <option value="pesados">Pesados</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label small">Marca</label>
                    <select class="form-control form-control-sm select2-marca" name="vehiculos[${newIndex}][marca_id]" id="marca-${newIndex}" data-index="${newIndex}"></select>
                </div>
                <div class="col-md-5">
                    <label class="form-label small">Modelo</label>
                    <select class="form-control form-control-sm select2-modelo" name="vehiculos[${newIndex}][modelo_id]" id="modelo-${newIndex}" data-index="${newIndex}" disabled></select>
                </div>
            </div>
            <div class="row g-2 mb-2">
                <div class="col-md-4">
                    <label class="form-label small">Versión</label>
                    <select class="form-control form-control-sm select2-version" name="vehiculos[${newIndex}][version_id]" id="version-${newIndex}" data-index="${newIndex}" disabled></select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Año</label>
                    <select class="form-control form-control-sm select2-anio" name="vehiculos[${newIndex}][anio_modelo_id]" id="anio-${newIndex}" data-index="${newIndex}" disabled></select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Color</label>
                    <select class="form-control form-control-sm select2-color" name="vehiculos[${newIndex}][color_id]" id="color-${newIndex}" data-index="${newIndex}"></select>
                </div>
            </div>
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label small">Cantidad</label>
                    <input type="number" class="form-control form-control-sm cantidad" name="vehiculos[${newIndex}][cantidad]" min="1" value="1" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Precio Unitario</label>
                    <input type="number" class="form-control form-control-sm precio" name="vehiculos[${newIndex}][precio_unitario]" step="0.01" required>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-vehiculo-row w-100">
                        <i class="fas fa-trash-alt"></i> Eliminar
                    </button>
                </div>
            </div>
        </div>
    `;
    
    $('#vehiculos-container').append(newRowHtml);
    initSelect2Vehiculos();
});

// Eliminar fila de vehículo
$(document).on('click', '.remove-vehiculo-row', function() {
    $(this).closest('.vehiculos-row').remove();
});

// Agregar vehículos a la cotización
$(document).on('click', '.add-vehiculo-btn', function() {
    let hasErrors = false;
    let vehiculosAgregados = 0;

    $('.vehiculos-row').each(function() {
        const $row = $(this);
        const marca = $row.find('.select2-marca').select2('data')[0];
        const modelo = $row.find('.select2-modelo').select2('data')[0];
        const version = $row.find('.select2-version').select2('data')[0];
        const anio = $row.find('.select2-anio').select2('data')[0];
        const color = $row.find('.select2-color').select2('data')[0];
        const cantidad = $row.find('.cantidad').val();
        const precio = $row.find('.precio').val();
        const categoria = $row.find('.categoria').val();

        // Validar que todos los campos estén completos
        if (!marca || !modelo || !version || !anio) {
            return true; // Continuar con la siguiente fila si faltan datos básicos
        }

        if (!color || !color.id) {
            toastr.error('Seleccione un color para el vehículo');
            $row.find('.select2-color').focus();
            hasErrors = true;
            return false;
        }

        vehiculosAgregados++;

        if (!cantidad || parseFloat(cantidad) <= 0) {
            toastr.error('La cantidad debe ser mayor que cero');
            $row.find('.cantidad').focus();
            hasErrors = true;
            return false;
        }

        if (!precio || parseFloat(precio) <= 0) {
            toastr.error('El precio debe ser mayor que cero');
            $row.find('.precio').focus();
            hasErrors = true;
            return false;
        }

        const descripcion = `${marca.text} ${modelo.text} ${version.text} ${anio.text} (${color.text}) - ${categoria}`;

        itemsAgregados.push({
        id: Date.now() + Math.floor(Math.random() * 1000),
            tipo: 'vehiculos',
            descripcion: descripcion,
            cantidad: parseFloat(cantidad),
            precioUnitario: parseFloat(precio),
            total: parseFloat(cantidad) * parseFloat(precio),
            itemId: version.id, // Cambia esto si es necesario
            color_id: color.id,
            vehiculo_catalogo_id: version.id // Este nombre de campo debe coincidir con lo que espera el controlador
        });
    });

    if (vehiculosAgregados === 0) {
        toastr.error('Complete al menos un vehículo para agregar a la cotización');
        return;
    }

    if (hasErrors) return;

    actualizarTablaItems();
    recalcularTotales();

    $('#vehiculos-container').empty();
    $('.vehiculos-row:first .select2-marca').val(null).trigger('change');
    $('.vehiculos-row:first .select2-modelo').empty().prop('disabled', true).trigger('change');
    $('.vehiculos-row:first .select2-version').empty().prop('disabled', true).trigger('change');
    $('.vehiculos-row:first .select2-anio').empty().prop('disabled', true).trigger('change');
    $('.vehiculos-row:first .select2-color').val(null).trigger('change');
    $('.vehiculos-row:first .cantidad').val('1');
    $('.vehiculos-row:first .precio').val('');

    toastr.success('Vehículos agregados a la cotización');
});

// ELIMINAR FILA DE REPUESTO
$(document).on('click', '.remove-repuesto', function() {
    $(this).closest('.repuestos-row').remove();
});

// AGREGAR MÁS FILAS DE REPUESTOS
$(document).on('click', '.add-repuesto', function() {
    const repuestosCount = $('.repuestos-row').length;
    const newIndex = repuestosCount;
    
    const newRowHtml = `
        <div class="repuestos-row mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <select class="form-control form-control-sm select2-repuestos" name="repuestos[${newIndex}][repuesto_id]">
                        <option value="">Seleccione un repuesto</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control form-control-sm" name="repuestos[${newIndex}][unidad]" readonly>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control form-control-sm cantidad" name="repuestos[${newIndex}][cantidad]" min="1" value="1" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control form-control-sm precio" name="repuestos[${newIndex}][precio_unitario]" step="0.01" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-repuesto w-100">
                        <i class="fas fa-trash-alt"></i> Eliminar
                    </button>
                </div>
            </div>
        </div>
    `;
    
    $('#repuestos-container').append(newRowHtml);
    initSelect2Repuestos();
});
</script>
<script>
$(document).ready(function() {
    var timeoutId;

    $('#tipo_cotizacion').val('vehiculos');
    $('.btn-type-select[data-tipo="vehiculos"]').addClass('active');
    $('#section-vehiculos').show();

    // BÚSQUEDA DE CLIENTES
    $('#cliente_search').on('input', function() {
        var searchText = $(this).val().trim();
        clearTimeout(timeoutId);
        
        if (searchText.length < 2) {
            $('#resultados_clientes').remove();
            return;
        }
        
        timeoutId = setTimeout(function() {
            $.ajax({
                url: '/api/search/clientes',
                data: { term: searchText },
                dataType: 'json',
                beforeSend: function() {
                    if (!$('#resultados_clientes').length) {
                        $('<div id="resultados_clientes" class="dropdown-menu w-100 shadow-sm" style="display:block; position:absolute; z-index:1000;"><div class="p-2 text-center"><i class="fas fa-spinner fa-spin"></i> Buscando...</div></div>').insertAfter('#cliente_search');
                    } else {
                        $('#resultados_clientes').html('<div class="p-2 text-center"><i class="fas fa-spinner fa-spin"></i> Buscando...</div>');
                    }
                },
                success: function(data) {
                    console.log("Respuesta API clientes:", data);
                    
                    if (!$('#resultados_clientes').length) {
                        $('<div id="resultados_clientes" class="dropdown-menu w-100 shadow-sm" style="display:block; position:absolute; z-index:1000;"></div>').insertAfter('#cliente_search');
                    }
                    
                    var resultadosHtml = '';
                    
                    if (!data || data.length === 0) {
                        resultadosHtml = '<div class="p-2 text-center text-muted">No se encontraron resultados</div>';
                    } else {
                        $.each(data, function(index, cliente) {
                            if (!cliente || !cliente.id) {
                                console.error("Cliente inválido:", cliente);
                                return;
                            }
                            
                            var itemHtml = '<a href="#" class="dropdown-item cliente-item py-2" data-id="' + cliente.id + '" data-tipo="' + (cliente.tipo_cliente || 'desconocido') + '">';
                            
                            if (cliente.tipo_cliente === 'natural') {
                                itemHtml += '<div class="d-flex justify-content-between">';
                                itemHtml += '<span>' + (cliente.nombres || '') + ' ' + (cliente.apellido_paterno || '') + ' ' + (cliente.apellido_materno || '') + '</span>';
                                itemHtml += '</div>';
                                itemHtml += '<small class="text-muted d-block">DNI: ' + (cliente.documento_identidad || 'N/A') + '</small>';
                            } else {
                                itemHtml += '<div class="d-flex justify-content-between">';
                                itemHtml += '<span>' + (cliente.razon_social || 'Sin razón social') + '</span>';
                                itemHtml += '</div>';
                                itemHtml += '<small class="text-muted d-block">RUC: ' + (cliente.documento_identidad || 'N/A') + '</small>';
                            }
                            
                            itemHtml += '</a>';
                            resultadosHtml += itemHtml;
                        });
                    }
                    
                    $('#resultados_clientes').html(resultadosHtml);
                },
                error: function(xhr, status, error) {
                    console.error("Error al buscar clientes:", {status: xhr.status, error: error, response: xhr.responseText});
                    
                    if (xhr.status === 401) {
                        window.location.href = '/login';
                    } else {
                        $('#resultados_clientes').html('<div class="p-2 text-center text-danger">Error al buscar clientes: ' + error + '</div>');
                    }
                }
            });
        }, 300);
    });
    
    // SELECCIÓN DE CLIENTE
    $(document).on('click', '.cliente-item', function(e) {
        e.preventDefault();
        
        var $this = $(this);
        var clienteId = $this.data('id');
        var tipoCliente = $this.data('tipo');
        var clienteNombre = $this.find('span').text();
        var clienteDocumento = $this.find('small').text().split(': ')[1];
        
        $('#cliente_id').val(clienteId);
        $('#cliente_search').val(clienteNombre);
        $('#resultados_clientes').remove();
        
        var infoHtml = '<div class="card p-2">';
        infoHtml += '<strong>' + clienteNombre + '</strong>';
        infoHtml += '<span class="badge bg-info d-inline-block mt-1">' + 
                    (tipoCliente === 'natural' ? 'DNI: ' : 'RUC: ') + clienteDocumento + '</span>';
        infoHtml += '</div>';
        
        $('#cliente_info').html(infoHtml);
    });
    
    // TIPO DE COTIZACIÓN
    $('.btn-type-select').on('click', function(e) {
        e.preventDefault();
        
        const tipo = $(this).data('tipo');
        
        // Asegurar que vehículos siempre esté seleccionado
        if (tipo !== 'vehiculos') {
            return false; // No permitir cambiar a otros tipos
        }
        
        $('#tipo_cotizacion').val(tipo);
        $('.btn-type-select').removeClass('active');
        $(this).addClass('active');
        
        $('.cotizacion-section').hide();
        
        const sectionId = `#section-${tipo}`;
        $(sectionId).show();
    });
    
    // EVENTOS
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#cliente_search, #resultados_clientes').length) {
            $('#resultados_clientes').remove();
        }
    });

    $(document).on('click', '.eliminar-item', function() {
        eliminarItem($(this).data('id'));
    });

    $('input[name="condicion"], input[name="moneda"], input[name="forma_pago"]').change(recalcularTotales);

    // VALIDACIÓN DEL FORMULARIO
    $('#form-cotizacion').submit(function(event) {
        let valido = true;
        let errorMessages = [];

        if (!$('#cliente_id').val()) {
            errorMessages.push('Seleccione un cliente');
            $('#cliente_search').addClass('is-invalid');
            valido = false;
        } else {
            $('#cliente_search').removeClass('is-invalid');
        }

        if (!$('#tipo_cotizacion').val()) {
            errorMessages.push('Seleccione un tipo de cotización');
            $('.btn-type-select').addClass('is-invalid');
            valido = false;
        } else {
            $('.btn-type-select').removeClass('is-invalid');
        }

        if (itemsAgregados.length === 0) {
            errorMessages.push('Agregue al menos un ítem');
            $('#items-tabla').addClass('is-invalid');
            valido = false;
        } else {
            $('#items-tabla').removeClass('is-invalid');
        }

        if (!$('#almacen_id').val()) {
            errorMessages.push('Seleccione un almacén');
            $('#almacen_id').addClass('is-invalid');
            valido = false;
        } else {
            $('#almacen_id').removeClass('is-invalid');
        }

        if (!$('input[name="condicion"]:checked').val()) {
            errorMessages.push('Seleccione una condición');
            $('input[name="condicion"]').closest('.btn-group').addClass('is-invalid');
            valido = false;
        } else {
            $('input[name="condicion"]').closest('.btn-group').removeClass('is-invalid');
        }

        if (!$('#canal').val()) {
            errorMessages.push('Seleccione un canal');
            $('#canal').addClass('is-invalid');
            valido = false;
        } else {
            $('#canal').removeClass('is-invalid');
        }

        if (!$('input[name="moneda"]:checked').val()) {
            errorMessages.push('Seleccione una moneda');
            $('input[name="moneda"]').closest('.btn-group').addClass('is-invalid');
            valido = false;
        } else {
            $('input[name="moneda"]').closest('.btn-group').removeClass('is-invalid');
        }

        if (!$('input[name="forma_pago"]:checked').val()) {
            errorMessages.push('Seleccione una forma de pago');
            $('input[name="forma_pago"]').closest('.btn-group').addClass('is-invalid');
            valido = false;
        } else {
            $('input[name="forma_pago"]').closest('.btn-group').removeClass('is-invalid');
        }

        if (!valido) {
            event.preventDefault();
            errorMessages.forEach(function(message) {
                toastr.error(message);
            });

            const $firstInvalid = $('.is-invalid').first();
            if ($firstInvalid.length > 0) {
                $('html, body').animate({
                    scrollTop: $firstInvalid.offset().top - 100
                }, 500);
            }
            return;
        }

        console.log('Enviando formulario:', $(this).serializeArray());
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                console.log('Cotización guardada:', response);
                toastr.success('Cotización creada exitosamente');
                window.location.href = response.redirect || '/admin/ventas/cotizaciones';
            },
            error: function(xhr) {
                console.error('Error al guardar:', xhr.responseText);
                toastr.error('Error al guardar la cotización: ' + (xhr.responseJSON?.message || 'Error desconocido'));
            }
        });
        
        event.preventDefault();
    });

    // AGREGAR REPUESTOS A COTIZACIÓN
    $('.add-repuesto-btn').on('click', function() {
        let hasErrors = false;
        let repuestosAgregados = 0;
        
        $('.repuestos-row').each(function() {
            const $row = $(this);
            const $select = $row.find('.select2-repuestos');
            const $cantidad = $row.find('.cantidad');
            const $precio = $row.find('.precio');
            
            if (!$select.val()) {
                return true;
            }
            
            repuestosAgregados++;
            
            if (!$cantidad.val() || parseFloat($cantidad.val()) <= 0) {
                toastr.error('Ingrese una cantidad válida para todos los repuestos seleccionados');
                $cantidad.focus();
                hasErrors = true;
                return false;
            }
            
            if (!$precio.val() || parseFloat($precio.val()) <= 0) {
                toastr.error('Ingrese un precio válido para todos los repuestos seleccionados');
                $precio.focus();
                hasErrors = true;
                return false;
            }
        });
        
        if (repuestosAgregados === 0) {
            toastr.error('Seleccione al menos un repuesto');
            return;
        }
        
        if (hasErrors) return;
        
        $('.repuestos-row').each(function() {
            const $row = $(this);
            const repuestoId = $row.find('.select2-repuestos').val();
            
            if (!repuestoId) return true;
            
            const repuestoText = $row.find('.select2-repuestos').select2('data')[0]?.text || 'Repuesto';
            const cantidad = parseFloat($row.find('.cantidad').val());
            const precio = parseFloat($row.find('.precio').val());
            const unidad = $row.find('input[name$="[unidad]"]').val();
            
            agregarItem('repuestos', repuestoText, cantidad, precio, repuestoId, unidad);
        });
        
        $('#repuestos-container').empty();
        $('.repuestos-row:first .select2-repuestos').val(null).trigger('change');
        $('.repuestos-row:first .cantidad').val('1');
        $('.repuestos-row:first .precio').val('');
        $('.repuestos-row:first input[name$="[unidad]"]').val('');
        
        toastr.success('Repuestos agregados a la cotización');
    });

    // Inicialización
    initSelect2Repuestos();
});
</script>
@endpush