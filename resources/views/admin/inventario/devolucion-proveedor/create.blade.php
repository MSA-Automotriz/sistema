@extends('admin.layouts.app')

@section('title', 'Devoluciones a Proveedores')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Nueva Devolución a Proveedor</h5>
                    <a href="{{ route('admin.inventario.devoluciones.index') }}" class="btn btn-sm btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left me-1" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                        </svg>
                        Volver
                    </a>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.inventario.devoluciones.store') }}" method="POST" id="devolucionForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="proveedor_id" class="form-label">Proveedor <span class="text-danger">*</span></label>
                                    <select name="proveedor_id" id="proveedor_id" class="form-select @error('proveedor_id') is-invalid @enderror" required>
                                        <option value="">Seleccione un proveedor</option>
                                        @foreach($proveedores as $proveedor)
                                            <option value="{{ $proveedor->id }}" {{ old('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                                                {{ $proveedor->nombre_completo }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('proveedor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="almacen_id" class="form-label">Almacén <span class="text-danger">*</span></label>
                                    <select name="almacen_id" id="almacen_id" class="form-select @error('almacen_id') is-invalid @enderror" required>
                                        <option value="">Seleccione un almacén</option>
                                        @foreach($almacenes as $almacen)
                                            <option value="{{ $almacen->id }}" {{ old('almacen_id') == $almacen->id ? 'selected' : '' }}>
                                                {{ $almacen->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('almacen_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="motivo" class="form-label">Motivo <span class="text-danger">*</span></label>
                                    <input type="text" name="motivo" id="motivo" class="form-control @error('motivo') is-invalid @enderror" value="{{ old('motivo') }}" required>
                                    @error('motivo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fecha_emision" class="form-label">Fecha de Emisión <span class="text-danger">*</span></label>
                                    <input type="date" name="fecha_emision" id="fecha_emision" class="form-control @error('fecha_emision') is-invalid @enderror" value="{{ old('fecha_emision', date('Y-m-d')) }}" required>
                                    @error('fecha_emision')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea name="observaciones" id="observaciones" class="form-control @error('observaciones') is-invalid @enderror" rows="3">{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <h5 class="mt-4 mb-3">Productos a Devolver</h5>
                        
                        <div class="mb-3">
                            <button type="button" id="agregarProductoBtn" class="btn btn-sm btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                                    <path d="M8 0a1 1 0 0 1 1 1v6h6a1 1 0 1 1 0 2H9v6a1 1 0 1 1-2 0V9H1a1 1 0 0 1 0-2h6V1a1 1 0 0 1 1-1z"/>
                                </svg>
                                Agregar Producto
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover" id="tablaItems">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Código</th>
                                        <th>Unidad</th>
                                        <th>Cantidad</th>
                                        <th>Motivo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Aquí se agregarán las filas de productos dinámicamente -->
                                    <tr id="noItemsRow">
                                        <td colspan="6" class="text-center">No hay productos agregados</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary" id="submitBtn">Guardar Devolución</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para seleccionar producto -->
<div class="modal fade" id="productosModal" tabindex="-1" aria-labelledby="productosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productosModalLabel">Seleccionar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="buscarProducto" class="form-label">Buscar Producto</label>
                    <select id="buscarProducto" class="form-control select2-productos" style="width: 100%"></select>
                </div>
                
                <div id="detalleProducto" class="d-none">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="modalCantidad" class="form-label">Cantidad a Devolver</label>
                                <input type="number" id="modalCantidad" class="form-control" min="0.01" step="0.01" value="1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="modalMotivo" class="form-label">Motivo del Detalle</label>
                                <input type="text" id="modalMotivo" class="form-control" placeholder="Ej: Producto dañado, error en pedido, etc.">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="agregarItemBtn" disabled>Agregar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Inicializar Select2 para el buscador de productos
        $('.select2-productos').select2({
            dropdownParent: $('#productosModal'),
            placeholder: 'Buscar por nombre o código',
            minimumInputLength: 2,
            ajax: {
                // Verifica que esta ruta sea correcta
                url: '{{ route("admin.inventario.devoluciones.buscar") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            }
        });

        // Al seleccionar un producto en el modal
        $('#buscarProducto').on('change', function() {
            let data = $(this).select2('data')[0];
            if (data) {
                $('#detalleProducto').removeClass('d-none');
                $('#agregarItemBtn').prop('disabled', false);
            } else {
                $('#detalleProducto').addClass('d-none');
                $('#agregarItemBtn').prop('disabled', true);
            }
        });

        // Mostrar modal al hacer clic en "Agregar Producto"
        $('#agregarProductoBtn').on('click', function() {
            $('#buscarProducto').val(null).trigger('change');
            $('#detalleProducto').addClass('d-none');
            $('#modalCantidad').val(1);
            $('#modalMotivo').val('');
            $('#agregarItemBtn').prop('disabled', true);
            $('#productosModal').modal('show');
        });

        // Contador para generar IDs únicos para cada item
        let itemCounter = 0;

        // Agregar el item a la tabla desde el modal
        $('#agregarItemBtn').on('click', function() {
            const itemData = $('#buscarProducto').select2('data')[0];
            const cantidad = parseFloat($('#modalCantidad').val());
            const motivo = $('#modalMotivo').val();
            
            if (!itemData || isNaN(cantidad) || cantidad <= 0) {
                alert('Por favor ingrese datos válidos');
                return;
            }
            
            // Ocultar la fila "No hay productos"
            $('#noItemsRow').hide();
            
            // Crear nueva fila
            const newRow = `
                <tr id="row-${itemCounter}">
                    <td>${itemData.nombre}</td>
                    <td>${itemData.codigo}</td>
                    <td>${itemData.unidad}</td>
                    <td>
                        <input type="number" name="items[${itemCounter}][cantidad]" value="${cantidad}" min="0.01" step="0.01" class="form-control form-control-sm" required>
                        <input type="hidden" name="items[${itemCounter}][id]" value="${itemData.id}">
                        <input type="hidden" name="items[${itemCounter}][tipo]" value="${itemData.tipo}">
                    </td>
                    <td>
                        <input type="text" name="items[${itemCounter}][motivo_detalle]" value="${motivo}" class="form-control form-control-sm">
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger eliminar-item" data-row="${itemCounter}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                            </svg>
                        </button>
                    </td>
                </tr>
            `;
            
            $('#tablaItems tbody').append(newRow);
            itemCounter++;
            
            // Cerrar el modal
            $('#productosModal').modal('hide');
        });
        
        // Eliminar item
        $(document).on('click', '.eliminar-item', function() {
            const rowId = $(this).data('row');
            $(`#row-${rowId}`).remove();
            
            // Mostrar mensaje "No hay productos" si no hay filas
            if ($('#tablaItems tbody tr').length === 1) {
                $('#noItemsRow').show();
            }
        });
        
        // Validar formulario antes de enviar
        $('#devolucionForm').on('submit', function(e) {
            const rowCount = $('#tablaItems tbody tr').length - $('#noItemsRow:visible').length;
            
            if (rowCount === 0) {
                e.preventDefault();
                alert('Debe agregar al menos un producto a la devolución');
                return false;
            }
            
            return true;
        });
        
        // Inicializar Select2 para otros select del formulario
        $('#proveedor_id, #almacen_id').select2({
            placeholder: 'Seleccione una opción',
            width: '100%'
        });
    });
</script>
@endpush