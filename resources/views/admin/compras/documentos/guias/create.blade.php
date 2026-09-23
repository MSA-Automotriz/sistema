@extends('admin.layouts.app')

@section('title', 'Nueva Guía de Entrega')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
    }
    .producto-item {
        border-left: 4px solid #0d6efd;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Nueva Guía de Entrega</h5>
                    <a href="{{ route('admin.guias.index') }}" class="btn btn-sm btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left me-1" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                        </svg>
                        Volver
                    </a>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.guias.store') }}" method="POST" id="form-guia">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="numero" class="form-label">Número de Guía</label>
                                    <input type="text" class="form-control" id="numero" name="numero" placeholder="Se generará automáticamente" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="fecha" class="form-label">Fecha <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('fecha') is-invalid @enderror" 
                                           id="fecha" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required>
                                    @error('fecha')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="proveedor_id" class="form-label">Proveedor <span class="text-danger">*</span></label>
                                    <select class="form-control @error('proveedor_id') is-invalid @enderror" 
                                            id="proveedor_id" name="proveedor_id" required>
                                        <option value="">Seleccione un proveedor</option>
                                        @foreach($proveedores as $proveedor)
                                            <option value="{{ $proveedor->id }}" {{ old('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                                                {{ $proveedor->razon_social }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('proveedor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Información del transporte -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="transportista" class="form-label">Transportista</label>
                                    <input type="text" class="form-control @error('transportista') is-invalid @enderror" 
                                           id="transportista" name="transportista" value="{{ old('transportista') }}"
                                           placeholder="Nombre de la empresa transportista">
                                    @error('transportista')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="placa_vehiculo" class="form-label">Placa del Vehículo</label>
                                    <input type="text" class="form-control @error('placa_vehiculo') is-invalid @enderror" 
                                           id="placa_vehiculo" name="placa_vehiculo" value="{{ old('placa_vehiculo') }}"
                                           placeholder="ABC-123">
                                    @error('placa_vehiculo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="conductor" class="form-label">Conductor</label>
                                    <input type="text" class="form-control @error('conductor') is-invalid @enderror" 
                                           id="conductor" name="conductor" value="{{ old('conductor') }}"
                                           placeholder="Nombre completo del conductor">
                                    @error('conductor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="dni_conductor" class="form-label">DNI del Conductor</label>
                                    <input type="text" class="form-control @error('dni_conductor') is-invalid @enderror" 
                                           id="dni_conductor" name="dni_conductor" value="{{ old('dni_conductor') }}"
                                           placeholder="12345678">
                                    @error('dni_conductor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="observaciones" class="form-label">Observaciones</label>
                                    <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                              id="observaciones" name="observaciones" rows="3" 
                                              placeholder="Observaciones adicionales...">{{ old('observaciones') }}</textarea>
                                    @error('observaciones')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Sección de productos -->
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="border-bottom pb-2 mb-3">Productos a Entregar</h6>
                                <div id="productos-container">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Agregue productos utilizando el botón "Agregar Producto" debajo.
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="agregar-producto">
                                    <i class="fas fa-plus me-1"></i>
                                    Agregar Producto
                                </button>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.guias.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar Guía</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
let productoIndex = 0;

$(document).ready(function() {
    // Inicializar Select2 en proveedor
    $('#proveedor_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'Seleccione un proveedor',
        allowClear: true,
        width: '100%'
    });
});

document.getElementById('agregar-producto').addEventListener('click', function() {
    agregarProducto();
});

function agregarProducto() {
    const container = document.getElementById('productos-container');
    const alertInfo = container.querySelector('.alert-info');
    if (alertInfo) {
        alertInfo.remove();
    }

    const productoHtml = `
        <div class="card mb-3 producto-item">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">
                        <i class="fas fa-box me-2"></i>
                        Producto ${productoIndex + 1}
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarProducto(this)">
                        <i class="fas fa-trash me-1"></i>
                        Eliminar
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Buscar Producto <span class="text-danger">*</span></label>
                            <select class="form-control producto-select"
                                    id="producto_select_${productoIndex}"
                                    data-index="${productoIndex}"
                                    required>
                                <option value="">Buscar por código o nombre...</option>
                            </select>
                            <input type="hidden" name="productos[${productoIndex}][id]" class="producto-id">
                            <input type="hidden" name="productos[${productoIndex}][tipo]" class="producto-tipo">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-3">
                            <label class="form-label">Cantidad <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01"
                                   class="form-control cantidad-input"
                                   name="productos[${productoIndex}][cantidad]"
                                   placeholder="0.00"
                                   data-index="${productoIndex}"
                                   required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-3">
                            <label class="form-label">Precio Unit. <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0"
                                   class="form-control precio-input"
                                   name="productos[${productoIndex}][precio]"
                                   placeholder="0.00"
                                   data-index="${productoIndex}"
                                   required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-3">
                            <label class="form-label">Subtotal</label>
                            <input type="text" class="form-control subtotal-input bg-light" readonly placeholder="S/. 0.00">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="info-producto text-muted small"></div>
                    </div>
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', productoHtml);

    // Inicializar Select2 para el nuevo producto
    const newSelect = $(`#producto_select_${productoIndex}`);
    newSelect.select2({
        theme: 'bootstrap-5',
        placeholder: 'Buscar por código o nombre...',
        allowClear: true,
        width: '100%',
        ajax: {
            url: '{{ route("admin.devoluciones.buscar-productos") }}',
            dataType: 'json',
            delay: 300,
            data: function (params) {
                return {
                    search: params.term || '',
                    tipo: ''
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(item => ({
                        id: `${item.tipo}_${item.id}`, // ID único para Select2
                        producto_id: item.id, // ID real del producto
                        text: `${item.codigo} - ${item.nombre}`,
                        tipo: item.tipo,
                        codigo: item.codigo,
                        nombre: item.nombre,
                        stock: item.stock,
                        precio: item.precio
                    }))
                };
            },
            cache: true
        },
        minimumInputLength: 2,
        templateResult: formatProducto,
        templateSelection: formatProductoSelection
    });

    // Event handler cuando se selecciona un producto
    newSelect.on('select2:select', function (e) {
        const data = e.params.data;
        const index = $(this).data('index');
        const card = $(this).closest('.producto-item');

        // Llenar campos ocultos con el ID real del producto
        card.find('.producto-id').val(data.producto_id);
        card.find('.producto-tipo').val(data.tipo);

        // Establecer precio
        card.find('.precio-input').val(data.precio);

        // Establecer cantidad por defecto
        if (card.find('.cantidad-input').val() === '') {
            card.find('.cantidad-input').val(1);
        }

        // Mostrar información del producto
        card.find('.info-producto').html(`
            <i class="fas fa-info-circle me-1"></i>
            <span class="badge ${data.tipo === 'vehiculo' ? 'bg-info' : 'bg-primary'}">${data.tipo.toUpperCase()}</span>
            Stock disponible: <strong>${data.stock}</strong> unidades
        `);

        // Calcular subtotal
        calcularSubtotalCard(card);
    });

    // Event listeners para cálculo automático
    const lastProduct = container.lastElementChild;
    const cantidadInput = lastProduct.querySelector('.cantidad-input');
    const precioInput = lastProduct.querySelector('.precio-input');

    [cantidadInput, precioInput].forEach(input => {
        input.addEventListener('input', function() {
            calcularSubtotalCard($(lastProduct));
        });
    });

    productoIndex++;
}

function formatProducto(item) {
    if (item.loading) {
        return item.text;
    }

    const tipo = item.tipo || 'parte';
    const badgeClass = tipo === 'vehiculo' ? 'bg-info' : 'bg-primary';

    return $(`
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <div>
                    <span class="badge ${badgeClass}">${tipo.toUpperCase()}</span>
                    <strong>${item.codigo}</strong> - ${item.nombre}
                </div>
                <small class="text-muted">Stock: ${item.stock} | Precio: S/. ${parseFloat(item.precio).toFixed(2)}</small>
            </div>
        </div>
    `);
}

function formatProductoSelection(item) {
    return item.text;
}

function calcularSubtotalCard(card) {
    const cantidad = parseFloat(card.find('.cantidad-input').val()) || 0;
    const precio = parseFloat(card.find('.precio-input').val()) || 0;
    const subtotal = cantidad * precio;
    card.find('.subtotal-input').val('S/. ' + subtotal.toFixed(2));
    calcularTotal();
}

function calcularTotal() {
    let total = 0;
    $('.producto-item').each(function() {
        const cantidad = parseFloat($(this).find('.cantidad-input').val()) || 0;
        const precio = parseFloat($(this).find('.precio-input').val()) || 0;
        total += cantidad * precio;
    });

    // Puedes mostrar el total en algún lugar si lo deseas
    console.log('Total general:', total.toFixed(2));
}

function eliminarProducto(button) {
    if (!confirm('¿Está seguro de eliminar este producto?')) {
        return;
    }

    const productoItem = button.closest('.producto-item');
    $(productoItem).find('select').select2('destroy');
    productoItem.remove();

    calcularTotal();

    // Si no quedan productos, mostrar el mensaje informativo
    const container = document.getElementById('productos-container');
    if (container.children.length === 0) {
        container.innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Agregue productos utilizando el botón "Agregar Producto" debajo.
            </div>
        `;
    }
}

function calcularSubtotal(cantidadInput, precioInput, subtotalInput) {
    const cantidad = parseFloat(cantidadInput.value) || 0;
    const precio = parseFloat(precioInput.value) || 0;
    const subtotal = cantidad * precio;
    subtotalInput.value = 'S/. ' + subtotal.toFixed(2);
}

// Validación del formulario
document.getElementById('form-guia').addEventListener('submit', function(e) {
    const productosContainer = document.getElementById('productos-container');
    const productos = productosContainer.querySelectorAll('.producto-item');

    if (productos.length === 0) {
        e.preventDefault();
        toastr.error('Debe agregar al menos un producto para crear la guía de entrega.');
        return false;
    }

    // Validar que todos los productos tengan ID
    let valido = true;
    productos.forEach(producto => {
        const productoId = producto.querySelector('.producto-id').value;
        if (!productoId) {
            valido = false;
        }
    });

    if (!valido) {
        e.preventDefault();
        toastr.error('Todos los productos deben tener un producto seleccionado.');
        return false;
    }
});
</script>
@endpush

@endsection