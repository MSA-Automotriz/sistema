@extends('admin.layouts.app')

@section('title', 'Editar Requerimiento')

@section('content')
<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-edit text-info me-2"></i> Modificación
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6 text-shadow-sm d-flex align-items-center">
                    Requerimiento #{{ $requerimiento->id }}
                </h2>
                <p class="text-white-50 mb-0">Actualizando la solicitud de compra.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.compras.requerimientos.show', $requerimiento) }}" class="btn bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0">
                    <i class="fas fa-arrow-left text-primary me-2"></i> Ver Detalle
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
                <form id="requerimientoForm" method="POST" action="{{ route('admin.compras.requerimientos.update', $requerimiento->id) }}" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <!-- Almacén de Destino -->
                        <div class="col-md-6">
                            <label for="almacen_id" class="form-label small text-muted mb-1">Almacén de Destino</label>
                            <select name="almacen_id" id="almacen_id" class="form-select form-select-sm @error('almacen_id') is-invalid @enderror" required>
                                <option value="" disabled>Seleccione un almacén</option>
                                @foreach ($almacenes as $almacen)
                                    <option value="{{ $almacen->id }}" {{ $requerimiento->almacen_id == $almacen->id ? 'selected' : '' }}>{{ $almacen->nombre }}</option>
                                    @foreach ($almacen->allChildren as $subalmacen)
                                        <option value="{{ $subalmacen->id }}" {{ $requerimiento->almacen_id == $subalmacen->id ? 'selected' : '' }}>-- {{ $subalmacen->nombre }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                            @error('almacen_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Selección de Producto -->
                        <div class="col-12 mt-4">
                            <div class="card bg-light border-0" :class="darkMode ? 'bg-dark-subtle' : ''">
                                <div class="card-body p-3">
                                    <h6 class="fw-medium mb-3" :class="darkMode ? 'text-light' : ''">Agregar Productos</h6>
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-5">
                                            <label class="form-label small text-muted mb-1">Código de Producto</label>
                                            <div class="input-group input-group-sm">
                                                <input type="text" id="producto_search" class="form-control" placeholder="Busca por código o nombre" autocomplete="off">
                                                <span class="input-group-text">Código</span>
                                            </div>
                                            <div id="producto_suggestions" class="dropdown-menu w-100" style="max-height: 200px; overflow-y: auto;"></div>
                                            <input type="hidden" id="item_id" name="item_id">
                                            <input type="hidden" id="tipo_item" name="tipo_item">
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label small text-muted mb-1">Cantidad</label>
                                            <div class="input-group input-group-sm">
                                                <input type="number" id="cantidad" class="form-control" placeholder="Ingrese cantidad" step="0.01" min="0.01">
                                                <span class="input-group-text">Cantidad</span>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" id="agregarProducto" class="btn btn-primary btn-sm w-100">Agregar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla de Detalles -->
                        <div class="col-12 mt-4">
                            <div class="table-responsive">
                                <table class="table table-hover" :class="darkMode ? 'table-dark' : ''">
                                    <thead class="table-light" :class="darkMode ? 'table-dark' : ''">
                                        <tr>
                                            <th class="small text-uppercase">Nro.</th>
                                            <th class="small text-uppercase">Código</th>
                                            <th class="small text-uppercase">Parte o Repuesto</th>
                                            <th class="small text-uppercase">Cantidad Requerida</th>
                                            <th class="small text-uppercase text-end">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detallesTabla"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('admin.compras.requerimientos.index') }}" class="btn btn-outline-secondary btn-sm">Cancelar</a>
                        <button type="submit" class="btn btn-primary btn-sm">Actualizar Requerimiento</button>
                    </div>
                </form>
            </div>
    </div>
</div>
</div>
</div>
@endsection

@push('scripts')
<?php
$detallesIniciales = $requerimiento->detalles->map(function ($detalle) {
    return [
        'item_id' => $detalle->item_id,
        'tipo_item' => $detalle->tipo_item,
        'cantidad' => $detalle->cantidad,
        'codigo' => optional($detalle->item)->codigo ?? '',
        'nombre' => optional($detalle->item)->nombre ?? '',
    ];
})->values()->toArray();
?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let detalles = <?php echo json_encode($detallesIniciales); ?>;

    // Búsqueda de productos
    const productoSearch = document.getElementById('producto_search');
    const productoSuggestions = document.getElementById('producto_suggestions');
    const itemIdInput = document.getElementById('item_id');
    const tipoItemInput = document.getElementById('tipo_item');

    productoSearch.addEventListener('input', debounce(async (e) => {
        const query = e.target.value.trim();

        if (query.length < 2) {
            productoSuggestions.innerHTML = '';
            productoSuggestions.classList.remove('show');
            return;
        }

        try {
            const response = await fetch(`{{ route('admin.compras.requerimientos.search-partes') }}?query=${encodeURIComponent(query)}`);
            
            if (!response.ok) {
                throw new Error(`Error en la solicitud: ${response.status}`);
            }

            const productos = await response.json();
            
            if (productos.length === 0) {
                productoSuggestions.innerHTML = '<div class="dropdown-item text-muted">No se encontraron resultados</div>';
            } else {
                productoSuggestions.innerHTML = productos.map(producto => `
                    <a href="#" class="dropdown-item d-flex justify-content-between align-items-center" 
                       data-id="${producto.id}" 
                       data-tipo="${producto.tipo}" 
                       data-codigo="${producto.codigo}" 
                       data-nombre="${producto.nombre}">
                        <span>
                            <strong>${producto.codigo}</strong> - ${producto.nombre}
                        </span>
                        <span class="badge bg-secondary">${producto.tipo}</span>
                    </a>
                `).join('');
            }
            
            productoSuggestions.classList.add('show');

            // Manejar clic en sugerencias
            productoSuggestions.querySelectorAll('a').forEach(item => {
                item.addEventListener('click', (e) => {
                    e.preventDefault();
                    const { id, tipo, codigo, nombre } = item.dataset;
                    itemIdInput.value = id;
                    tipoItemInput.value = tipo;
                    productoSearch.value = `${codigo} - ${nombre}`;
                    productoSuggestions.classList.remove('show');
                    document.getElementById('cantidad').focus();
                });
            });

        } catch (error) {
            console.error('Error en la búsqueda:', error);
            productoSuggestions.innerHTML = '<div class="dropdown-item text-danger">Error al buscar productos</div>';
            productoSuggestions.classList.add('show');
        }
    }, 300));

    // Agregar producto a la tabla
    document.getElementById('agregarProducto').addEventListener('click', () => {
        const itemId = itemIdInput.value;
        const tipoItem = tipoItemInput.value;
        const cantidad = parseFloat(document.getElementById('cantidad').value);
        const productoText = productoSearch.value;

        if (!itemId || !tipoItem || !cantidad || isNaN(cantidad)) {
            Swal.fire({
                icon: 'warning',
                title: 'Datos incompletos',
                text: 'Por favor, seleccione un producto válido y especifique una cantidad correcta.',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        // Verificar si el producto ya existe en los detalles
        const existe = detalles.some(d => d.item_id == itemId && d.tipo_item == tipoItem);
        if (existe) {
            Swal.fire({
                icon: 'warning',
                title: 'Producto duplicado',
                text: 'Este producto ya está en la lista. Puede editar la cantidad existente.',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        const [codigo, nombre] = productoText.split(' - ');
        detalles.push({
            item_id: itemId,
            tipo_item: tipoItem,
            cantidad: cantidad,
            codigo: codigo,
            nombre: nombre || productoText
        });

        actualizarTabla();
        resetearBusqueda();
    });

    // Actualizar tabla de detalles
    function actualizarTabla() {
        const tbody = document.getElementById('detallesTabla');
        tbody.innerHTML = detalles.map((detalle, index) => `
            <tr>
                <td>${index + 1}</td>
                <td>${detalle.codigo}</td>
                <td>${detalle.nombre}</td>
                <td>
                    <input type="number" class="form-control form-control-sm" 
                           value="${detalle.cantidad}" min="0.01" step="0.01"
                           onchange="actualizarCantidad(${index}, this.value)">
                </td>
                <td class="text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger" 
                            onclick="eliminarDetalle(${index})" title="Eliminar">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>
        `).join('');

        actualizarInputsOcultos();
    }

    // Resetear campos de búsqueda
    function resetearBusqueda() {
        productoSearch.value = '';
        itemIdInput.value = '';
        tipoItemInput.value = '';
        document.getElementById('cantidad').value = '';
        productoSuggestions.innerHTML = '';
        productoSuggestions.classList.remove('show');
        productoSearch.focus();
    }

    // Eliminar detalle
    window.eliminarDetalle = (index) => {
        Swal.fire({
            title: '¿Eliminar producto?',
            text: "¿Está seguro de eliminar este producto de la lista?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                detalles.splice(index, 1);
                actualizarTabla();
                Toast.fire({
                    icon: 'success',
                    title: 'Producto eliminado',
                    background: 'var(--bs-success)'
                });
            }
        });
    };

    // Actualizar cantidad
    window.actualizarCantidad = (index, nuevaCantidad) => {
        const cantidad = parseFloat(nuevaCantidad);
        if (!isNaN(cantidad) && cantidad > 0) {
            detalles[index].cantidad = cantidad;
            actualizarInputsOcultos();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Cantidad inválida',
                text: 'Por favor ingrese un valor numérico mayor a cero',
                confirmButtonColor: '#3085d6'
            });
            document.querySelector(`#detallesTabla tr:nth-child(${index + 1}) input`).value = detalles[index].cantidad;
        }
    };

    // Actualizar inputs ocultos para el formulario
    function actualizarInputsOcultos() {
        const form = document.getElementById('requerimientoForm');
        const existingInputs = form.querySelectorAll('input[name^="detalles"]');
        existingInputs.forEach(input => input.remove());

        detalles.forEach((detalle, index) => {
            const baseName = `detalles[${index}]`;
            form.insertAdjacentHTML('beforeend', `
                <input type="hidden" name="${baseName}[item_id]" value="${detalle.item_id}">
                <input type="hidden" name="${baseName}[tipo_item]" value="${detalle.tipo_item}">
                <input type="hidden" name="${baseName}[cantidad]" value="${detalle.cantidad}">
            `);
        });
    }

    // Configuración de SweetAlert2
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    // Función debounce para la búsqueda
    function debounce(func, wait, immediate = false) {
        let timeout;
        return function() {
            const context = this, args = arguments;
            const later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    }

    // Inicializar tabla al cargar
    actualizarTabla();

    // Validación del formulario
    const form = document.getElementById('requerimientoForm');
    form.addEventListener('submit', function(event) {
        if (detalles.length === 0) {
            event.preventDefault();
            event.stopPropagation();
            Swal.fire({
                icon: 'error',
                title: 'Lista vacía',
                text: 'Debe agregar al menos un producto al requerimiento',
                confirmButtonColor: '#3085d6'
            });
        }
        
        form.classList.add('was-validated');
    }, false);

    // Cerrar sugerencias al hacer clic fuera
    document.addEventListener('click', (e) => {
        if (!productoSearch.contains(e.target) && !productoSuggestions.contains(e.target)) {
            productoSuggestions.classList.remove('show');
        }
    });
});
</script>

<style>
    .dropdown-menu {
        max-height: 300px;
        overflow-y: auto;
    }
    .dropdown-item {
        white-space: normal;
        padding: 0.5rem 1rem;
    }
    .dropdown-item:hover {
        background-color: #f8f9fa;
    }
    #detallesTabla input {
        max-width: 100px;
    }
    .table-responsive {
        min-height: 200px;
    }
</style>
@endpush