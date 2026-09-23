@extends('admin.layouts.app')

@section('title', 'Nuevo Vale de Devolución')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
    }
    .table-productos tbody tr:hover {
        background-color: #f8f9fa;
    }
    .badge-tipo {
        font-size: 0.75rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('admin.devoluciones.store') }}" method="POST" id="form-devolucion">
                @csrf

                <!-- Card Principal -->
                <div class="card shadow-sm mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-undo-alt me-2"></i>
                            Nuevo Vale de Devolución
                        </h5>
                        <a href="{{ route('admin.devoluciones.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Volver
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="numero" class="form-label">Número de Vale</label>
                                    <input type="text" class="form-control" value="Automático" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="fecha" class="form-label">Fecha <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('fecha') is-invalid @enderror"
                                           id="fecha" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required>
                                    @error('fecha')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="proveedor_id" class="form-label">Proveedor <span class="text-danger">*</span></label>
                                    <select class="form-select @error('proveedor_id') is-invalid @enderror"
                                            id="proveedor_id" name="proveedor_id" required>
                                        <option value="">Seleccione un proveedor</option>
                                        @foreach($proveedores as $proveedor)
                                            <option value="{{ $proveedor->id }}" {{ old('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                                                {{ $proveedor->razon_social }} - {{ $proveedor->ruc }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('proveedor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="motivo" class="form-label">Motivo de Devolución <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('motivo') is-invalid @enderror"
                                           id="motivo" name="motivo" value="{{ old('motivo') }}"
                                           placeholder="Ej: Productos defectuosos, Error en pedido, etc." required>
                                    @error('motivo')
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
                                              id="observaciones" name="observaciones" rows="2"
                                              placeholder="Observaciones adicionales (opcional)">{{ old('observaciones') }}</textarea>
                                    @error('observaciones')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Búsqueda de Productos -->
                <div class="card shadow-sm mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-search me-2"></i>
                            Buscar y Agregar Productos
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="buscar_producto_select" class="form-label">Buscar producto por código o nombre</label>
                                    <select class="form-control" id="buscar_producto_select">
                                        <option value="">Escriba para buscar...</option>
                                    </select>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Escriba al menos 2 caracteres para buscar. Seleccione un producto para agregarlo a la lista.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Productos Agregados -->
                <div class="card shadow-sm mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-boxes me-2"></i>
                            Productos a Devolver
                        </h6>
                        <span class="badge bg-primary" id="contador-productos">0 productos</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-productos">
                                <thead class="table-light">
                                    <tr>
                                        <th width="10%">Tipo</th>
                                        <th width="15%">Código</th>
                                        <th width="30%">Nombre</th>
                                        <th width="12%">Cantidad</th>
                                        <th width="12%">Precio Unit.</th>
                                        <th width="13%">Subtotal</th>
                                        <th width="8%">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla-productos">
                                    <tr id="sin-productos">
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p class="mb-0">No hay productos agregados</p>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot id="tabla-footer" style="display: none;">
                                    <tr class="table-light">
                                        <td colspan="5" class="text-end"><strong>TOTAL:</strong></td>
                                        <td><strong id="total-general">S/. 0.00</strong></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.devoluciones.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary" id="btn-guardar">
                                <i class="fas fa-save me-1"></i>
                                Guardar Vale de Devolución
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    let productos = [];

    // Inicializar Select2 en proveedor
    $('#proveedor_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'Seleccione un proveedor',
        allowClear: true,
        width: '100%'
    });

    // Inicializar Select2 para búsqueda de productos
    $('#buscar_producto_select').select2({
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
                        id: `${item.tipo}_${item.id}`,
                        text: `${item.codigo} - ${item.nombre}`,
                        tipo: item.tipo,
                        producto_id: item.id,
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
        templateResult: formatProductoSelect,
        templateSelection: formatProductoSelectionSimple
    });

    // Event handler cuando se selecciona un producto del Select2
    $('#buscar_producto_select').on('select2:select', function (e) {
        const data = e.params.data;
        agregarProducto({
            id: data.producto_id,
            tipo: data.tipo,
            codigo: data.codigo,
            nombre: data.nombre,
            precio: parseFloat(data.precio),
            stock: data.stock
        });

        // Limpiar el select
        $(this).val(null).trigger('change');
    });

    function formatProductoSelect(item) {
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

    function formatProductoSelectionSimple(item) {
        return item.text || 'Buscar producto...';
    }

    function agregarProducto(producto) {
        // Verificar si ya existe
        const existe = productos.find(p => p.id === producto.id && p.tipo === producto.tipo);
        if (existe) {
            toastr.warning('Este producto ya fue agregado');
            return;
        }

        productos.push({
            ...producto,
            cantidad: 1,
            subtotal: producto.precio
        });

        actualizarTabla();
        $('#buscar_producto').val('').focus();
        $('#resultados-busqueda').html('');
        toastr.success('Producto agregado correctamente');
    }

    function actualizarTabla() {
        const tbody = $('#tabla-productos');

        if (productos.length === 0) {
            tbody.html(`
                <tr id="sin-productos">
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p class="mb-0">No hay productos agregados</p>
                    </td>
                </tr>
            `);
            $('#tabla-footer').hide();
            $('#contador-productos').text('0 productos');
            return;
        }

        let html = '';
        let total = 0;

        productos.forEach((producto, index) => {
            const subtotal = producto.cantidad * producto.precio;
            total += subtotal;

            html += `
                <tr>
                    <td>
                        <span class="badge ${producto.tipo === 'vehiculo' ? 'bg-info' : 'bg-primary'}">${producto.tipo.toUpperCase()}</span>
                        <input type="hidden" name="productos[${index}][id]" value="${producto.id}">
                        <input type="hidden" name="productos[${index}][tipo]" value="${producto.tipo}">
                    </td>
                    <td>${producto.codigo}</td>
                    <td>${producto.nombre}</td>
                    <td>
                        <input type="number" class="form-control form-control-sm cantidad-input"
                               name="productos[${index}][cantidad]"
                               value="${producto.cantidad}"
                               min="0.01" step="0.01"
                               data-index="${index}" required>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm precio-input"
                               name="productos[${index}][precio]"
                               value="${producto.precio}"
                               min="0" step="0.01"
                               data-index="${index}" required>
                    </td>
                    <td><strong>S/. ${subtotal.toFixed(2)}</strong></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger btn-eliminar" data-index="${index}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        tbody.html(html);
        $('#total-general').text(`S/. ${total.toFixed(2)}`);
        $('#tabla-footer').show();
        $('#contador-productos').text(`${productos.length} producto${productos.length !== 1 ? 's' : ''}`);

        // Event listeners para inputs
        $('.cantidad-input, .precio-input').on('input', function() {
            const index = $(this).data('index');
            const cantidad = parseFloat($(`input[name="productos[${index}][cantidad]"]`).val()) || 0;
            const precio = parseFloat($(`input[name="productos[${index}][precio]"]`).val()) || 0;

            productos[index].cantidad = cantidad;
            productos[index].precio = precio;
            productos[index].subtotal = cantidad * precio;

            actualizarTabla();
        });

        // Event listener para eliminar
        $('.btn-eliminar').on('click', function() {
            const index = $(this).data('index');
            eliminarProducto(index);
        });
    }

    function eliminarProducto(index) {
        if (confirm('¿Está seguro de eliminar este producto?')) {
            productos.splice(index, 1);
            actualizarTabla();
            toastr.info('Producto eliminado');
        }
    }

    // Validación antes de enviar
    $('#form-devolucion').on('submit', function(e) {
        if (productos.length === 0) {
            e.preventDefault();
            toastr.error('Debe agregar al menos un producto');
            return false;
        }

        // Validar que todos los productos tengan cantidad y precio válidos
        let valido = true;
        productos.forEach(producto => {
            if (producto.cantidad <= 0 || producto.precio < 0) {
                valido = false;
            }
        });

        if (!valido) {
            e.preventDefault();
            toastr.error('Todos los productos deben tener cantidad y precio válidos');
            return false;
        }

        // Deshabilitar botón para evitar doble submit
        $('#btn-guardar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Guardando...');
    });

    // Restaurar old values si hay errores de validación
    @if(old('productos'))
        const oldProductos = @json(old('productos'));
        if (oldProductos && oldProductos.length > 0) {
            // Aquí podrías cargar los productos antiguos si hay errores de validación
            // pero necesitarías hacer una petición AJAX para obtener los detalles completos
        }
    @endif
});
</script>
@endpush