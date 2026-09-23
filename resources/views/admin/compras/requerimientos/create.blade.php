@extends('admin.layouts.app')

@section('title', 'Crear Requerimiento')

@section('content')
<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-plus text-info me-2"></i> Nueva Solicitud
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6 text-shadow-sm d-flex align-items-center">
                    Crear Requerimiento
                </h2>
                <p class="text-white-50 mb-0">Inicia una nueva solicitud de compra de productos.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.compras.requerimientos.index') }}" class="btn bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0">
                    <i class="fas fa-arrow-left text-primary me-2"></i> Volver a Listado
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
                <form id="requerimientoForm" method="POST" action="{{ route('admin.compras.requerimientos.store') }}" class="needs-validation" novalidate>
                    @csrf
                    <div class="row g-3">
                        <!-- Almacén de Destino -->
                        <div class="col-md-6">
                            <label for="almacen_id" class="form-label small text-muted mb-1">Almacén de Destino</label>
                            <select name="almacen_id" id="almacen_id" class="form-select form-select-sm @error('almacen_id') is-invalid @enderror" required>
                                <option value="" disabled selected>Seleccione un almacén</option>
                                @foreach ($almacenes as $almacen)
                                    <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                                    @foreach ($almacen->allChildren as $subalmacen)
                                        <option value="{{ $subalmacen->id }}">-- {{ $subalmacen->nombre }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                            @error('almacen_id')
                                <div class="invalid-feedback">Por favor, seleccione un almacén válido.</div>
                            @enderror
                        </div>

                        <!-- Proveedor Sugerido (Opcional) -->
                        <div class="col-md-6">
                            <label for="proveedor_search" class="form-label small text-muted mb-1">Proveedor Sugerido (Opcional)</label>
                            <div class="position-relative">
                                <input type="text" id="proveedor_search" class="form-control form-control-sm" placeholder="Buscar por RUC, DNI, razón social o nombre" autocomplete="off" data-bs-toggle="tooltip" title="Busque por RUC, DNI o nombre">
                                <div id="proveedor_suggestions" class="dropdown-menu w-100" role="listbox" style="max-height: 200px; overflow-y: auto;"></div>
                                <input type="hidden" id="proveedor_id" name="proveedor_id">
                            </div>
                            <small class="text-muted">Puede sugerir un proveedor específico para este requerimiento</small>
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
                                            <div id="producto_suggestions" class="dropdown-menu w-100" role="listbox" style="max-height: 200px; overflow-y: auto;"></div>
                                            <input type="hidden" id="item_id" name="item_id">
                                            <input type="hidden" id="tipo_item" name="tipo_item">
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label small text-muted mb-1">Cantidad</label>
                                            <div class="input-group input-group-sm">
                                                <input type="number" id="cantidad" class="form-control" placeholder="Ingrese cantidad" step="0.01" min="0.01" data-bs-toggle="tooltip" title="La cantidad debe ser mayor a 0">
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
                                <input type="text" id="tabla_search" class="form-control form-control-sm mb-2" placeholder="Filtrar productos">
                                <table class="table table-hover" :class="darkMode ? 'table-dark' : ''">
                                    <thead class="table-light" :class="darkMode ? 'table-dark' : ''">
                                        <tr>
                                            <th class="small text-uppercase" data-sort="nro">Nro.</th>
                                            <th class="small text-uppercase" data-sort="codigo">Código</th>
                                            <th class="small text-uppercase" data-sort="nombre">Parte o Repuesto</th>
                                            <th class="small text-uppercase" data-sort="cantidad">Cantidad Requerida</th>
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
                        <button type="submit" class="btn btn-primary btn-sm">Guardar Requerimiento</button>
                        <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#ayudaModal">Ayuda</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Ayuda -->
<div class="modal fade" id="ayudaModal" tabindex="-1" aria-labelledby="ayudaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ayudaModalLabel">Guía Rápida</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p>1. Seleccione un almacén.<br>2. Busque y agregue productos.<br>3. Guarde el requerimiento.</p>
            </div>
    </div>
</div>
</div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    let detalles = [];

    // Inicializar tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));

    // Búsqueda de proveedores
    const proveedorSearch = document.getElementById('proveedor_search');
    const proveedorSuggestions = document.getElementById('proveedor_suggestions');
    const proveedorIdInput = document.getElementById('proveedor_id');

    proveedorSearch.addEventListener('input', debounce(async (e) => {
        const query = e.target.value;

        if (query.length < 2) {
            proveedorSuggestions.innerHTML = '';
            proveedorSuggestions.classList.remove('show');
            return;
        }

        try {
            proveedorSuggestions.innerHTML = '<div class="dropdown-item">Cargando...</div>';
            proveedorSuggestions.classList.add('show');

            const response = await fetch(`{{ route('admin.compras.requerimientos.search-proveedores') }}?query=${encodeURIComponent(query)}`);
            if (!response.ok) throw new Error('Error en la solicitud: ' + response.status);
            const proveedores = await response.json();

            if (proveedores.length === 0) {
                proveedorSuggestions.innerHTML = '<div class="dropdown-item text-muted">No se encontraron coincidencias</div>';
            } else {
                proveedorSuggestions.innerHTML = proveedores.slice(0, 10).map((proveedor, i) => `
                    <a href="#" class="dropdown-item" role="option" aria-selected="false" id="sug-prov-${i}" 
                    data-id="${proveedor.id}" data-documento="${proveedor.documento}" data-nombre="${proveedor.nombre_completo}">
                        <div class="fw-medium">${proveedor.nombre_completo}</div>
                        <small class="text-muted">${proveedor.documento}</small>
                    </a>
                `).join('');
            }
            proveedorSuggestions.classList.add('show');
        } catch (error) {
            console.error('Error en la búsqueda de proveedores:', error);
            proveedorSuggestions.innerHTML = '<div class="dropdown-item text-danger">Error al buscar proveedores</div>';
        }
    }, 300));

    proveedorSuggestions.addEventListener('click', (e) => {
        const item = e.target.closest('a');
        if (item) {
            proveedorIdInput.value = item.dataset.id;
            proveedorSearch.value = `${item.dataset.nombre} (${item.dataset.documento})`;
            proveedorSuggestions.classList.remove('show');
        }
    });

    // Búsqueda de productos
    const productoSearch = document.getElementById('producto_search');
    const productoSuggestions = document.getElementById('producto_suggestions');
    const itemIdInput = document.getElementById('item_id');
    const tipoItemInput = document.getElementById('tipo_item');

    productoSearch.addEventListener('input', debounce(async (e) => {
        const query = e.target.value;

        if (query.length < 2) {
            productoSuggestions.innerHTML = '';
            productoSuggestions.classList.remove('show');
            return;
        }

        try {
            productoSuggestions.innerHTML = '<div class="dropdown-item">Cargando...</div>';
            productoSuggestions.classList.add('show');

            const response = await fetch(`{{ route('admin.compras.requerimientos.search-partes') }}?query=${encodeURIComponent(query)}`);
            if (!response.ok) throw new Error('Error en la solicitud: ' + response.status);
            const productos = await response.json();

            productoSuggestions.innerHTML = productos.slice(0, 10).map((producto, i) => `
                <a href="#" class="dropdown-item" role="option" aria-selected="false" id="sug-prod-${i}" 
                   data-id="${producto.id}" data-tipo="${producto.tipo}" data-codigo="${producto.codigo}" data-nombre="${producto.nombre}">
                    ${producto.codigo} - ${producto.nombre} (${producto.tipo})
                </a>
            `).join('');
            productoSuggestions.classList.add('show');
        } catch (error) {
            console.error('Error en la búsqueda de productos:', error);
        }
    }, 300));

    productoSuggestions.addEventListener('click', (e) => {
        const item = e.target.closest('a');
        if (item) {
            itemIdInput.value = item.dataset.id;
            tipoItemInput.value = item.dataset.tipo;
            productoSearch.value = `${item.dataset.codigo} - ${item.dataset.nombre}`;
            productoSuggestions.classList.remove('show');
        }
    });

    // Agregar producto
    document.getElementById('agregarProducto').addEventListener('click', () => {
        const itemId = itemIdInput.value;
        const tipoItem = tipoItemInput.value;
        const cantidad = document.getElementById('cantidad').value;
        const productoText = productoSearch.value;

        if (!itemId || !tipoItem || !cantidad) {
            alert('Por favor, seleccione un producto y especifique la cantidad.');
            return;
        }

        const [codigo, nombre] = productoText.split(' - ');
        detalles.push({ item_id: itemId, tipo_item: tipoItem, cantidad, codigo, nombre });
        actualizarTabla();

        productoSearch.value = '';
        itemIdInput.value = '';
        tipoItemInput.value = '';
        document.getElementById('cantidad').value = '';
    });

    // Filtrar tabla
    document.getElementById('tabla_search').addEventListener('input', (e) => {
        const filtro = e.target.value.toLowerCase();
        const filtrados = detalles.filter(d => d.codigo.toLowerCase().includes(filtro) || d.nombre.toLowerCase().includes(filtro));
        actualizarTabla(filtrados);
    });

    // Ordenar tabla
    document.querySelectorAll('th[data-sort]').forEach(th => {
        th.addEventListener('click', () => {
            const column = th.dataset.sort;
            detalles.sort((a, b) => a[column].localeCompare(b[column]));
            actualizarTabla();
        });
    });

    function actualizarTabla(detallesMostrar = detalles) {
        const tbody = document.getElementById('detallesTabla');
        tbody.innerHTML = detallesMostrar.map((detalle, index) => `
            <tr class="nuevo">
                <td data-label="Nro.">${index + 1}</td>
                <td data-label="Código">${detalle.codigo}</td>
                <td data-label="Parte o Repuesto">${detalle.nombre}</td>
                <td data-label="Cantidad Requerida">${detalle.cantidad}</td>
                <td data-label="Acción" class="text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarDetalle(${index})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');

        setTimeout(() => tbody.querySelectorAll('tr').forEach(tr => tr.classList.remove('nuevo')), 1000);
        actualizarInputsOcultos();
    }

    window.eliminarDetalle = (index) => {
        detalles.splice(index, 1);
        actualizarTabla();
    };

    function actualizarInputsOcultos() {
        const form = document.getElementById('requerimientoForm');
        const existingInputs = form.querySelectorAll('input[name^="detalles"]');
        existingInputs.forEach(input => input.remove());

        detalles.forEach((detalle, index) => {
            form.insertAdjacentHTML('beforeend', `
                <input type="hidden" name="detalles[${index}][item_id]" value="${detalle.item_id}">
                <input type="hidden" name="detalles[${index}][tipo_item]" value="${detalle.tipo_item}">
                <input type="hidden" name="detalles[${index}][cantidad]" value="${detalle.cantidad}">
            `);
        });
    }

    function debounce(func, wait) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    (function () {
        'use strict';
        const form = document.getElementById('requerimientoForm');
        form.addEventListener('submit', (event) => {
            if (form.checkValidity() === false || detalles.length === 0) {
                event.preventDefault();
                event.stopPropagation();
                if (detalles.length === 0) alert('Debe agregar al menos un producto.');
            }
            form.classList.add('was-validated');
        }, false);
    })();
});
</script>
<style>
    .dropdown-menu.show {
        display: block;
        position: absolute;
        z-index: 1000;
    }
    .position-relative {
        position: relative;
    }
    @media (max-width: 768px) {
        .table-responsive table, thead, tbody, th, td, tr {
            display: block;
        }
        tr { margin-bottom: 1rem; }
        td { padding-left: 50%; position: relative; }
        td:before {
            content: attr(data-label);
            position: absolute;
            left: 0;
            width: 45%;
            padding-left: 1rem;
        }
    }
    #detallesTabla tr {
        transition: all 0.3s ease;
    }
    #detallesTabla tr.nuevo {
        background-color: #e0f7e0;
    }
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 5px var(--primary-color);
    }
</style>
@endpush