@extends('admin.layouts.app')

@section('title', 'Clientes')

@section('content')
<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-users text-info me-2"></i> Módulo de Clientes (CRM)
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6">Directorio de Clientes</h2>
                <p class="text-white-50 mb-0">Total documentados: <span id="total-clientes" class="fw-bold text-white fs-5 ms-1">{{ $totalClientes }}</span></p>
            </div>
            <div>
                <a href="{{ route('admin.clientes.create') }}" class="btn bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105" style="border: 1px solid rgba(255,255,255,0.8);">
                    <i class="fas fa-plus me-2 text-primary"></i> Agregar Cliente
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-lg-4 position-relative" style="top: -3.5rem; z-index: 10;">
    
    <!-- Mensaje de éxito (si existe) -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm border-0 rounded-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Panel principal flotante (Filtros) -->
    <div class="card dashboard-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-3">
                <i class="fas fa-filter text-primary me-2"></i>
                <h6 class="mb-0 fw-bold text-dark">Filtros de búsqueda</h6>
            </div>
            <div id="filtros-form">
                <div class="row g-3 align-items-center">
                    <div class="col-md-6 col-lg-3">
                        <select id="tipo_cliente" name="tipo_cliente" class="form-select bg-light border-light shadow-none filtro-campo">
                            <option value="">Tipo de Cliente</option>
                            <option value="natural" {{ request('tipo_cliente') == 'natural' ? 'selected' : '' }}>Persona Natural</option>
                            <option value="juridica" {{ request('tipo_cliente') == 'juridica' ? 'selected' : '' }}>Persona Jurídica</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <select id="categoria_cliente_id" name="categoria_cliente_id" class="form-select bg-light border-light shadow-none filtro-campo">
                            <option value="">Todas las Categorías</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}" {{ request('categoria_cliente_id') == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <select id="canal_captacion_id" name="canal_captacion_id" class="form-select bg-light border-light shadow-none filtro-campo">
                            <option value="">Canal de Captación</option>
                            @foreach ($canales as $canal)
                                <option value="{{ $canal->id }}" {{ request('canal_captacion_id') == $canal->id ? 'selected' : '' }}>
                                    {{ $canal->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 border-light text-muted"><i class="fas fa-search"></i></span>
                            <input type="text" id="query" name="query" class="form-control bg-light border-start-0 border-light shadow-none filtro-campo" placeholder="Buscar por documento, nombre..." value="{{ request('query') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenedor Resultados -->
    <div class="row">
        <div class="col-12">
            <div class="card dashboard-card border-0 shadow-sm">
                <!-- Tabla de clientes -->
                <div id="resultados-container" class="p-0">
                    @include('admin.clientes.partials.table', ['clientes' => $clientes])
                </div>

                <!-- Paginación -->
                <div id="pagination-container" class="card-footer bg-white border-top-0 d-flex justify-content-center mt-3 pb-4">
                    @include('admin.clientes.partials.pagination', ['clientes' => $clientes])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Seleccionar todos los elementos de filtro
    const filtrosCampos = document.querySelectorAll('.filtro-campo');
    
    // Añadir listener para cada campo
    filtrosCampos.forEach(campo => {
        campo.addEventListener('change', function() {
            aplicarFiltros();
        });
        
        // Para el campo de búsqueda, añadir un retardo para no realizar peticiones con cada tecla
        if (campo.id === 'query') {
            campo.addEventListener('input', debounce(function() {
                aplicarFiltros();
            }, 500));
        }
    });
    
    // Función para aplicar los filtros con AJAX
    function aplicarFiltros() {
        // Mostrar indicador de carga
        document.getElementById('resultados-container').innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></div>';
        
        // Obtener los valores de los filtros
        const tipo_cliente = document.getElementById('tipo_cliente').value;
        const categoria_cliente_id = document.getElementById('categoria_cliente_id').value;
        const canal_captacion_id = document.getElementById('canal_captacion_id').value;
        const query = document.getElementById('query').value;
        
        // Construir la URL con los parámetros
        const url = `{{ route('admin.clientes.index') }}?tipo_cliente=${encodeURIComponent(tipo_cliente)}&categoria_cliente_id=${encodeURIComponent(categoria_cliente_id)}&canal_captacion_id=${encodeURIComponent(canal_captacion_id)}&query=${encodeURIComponent(query)}`;
        
        // Actualizar la URL del navegador sin recargar la página
        window.history.pushState({}, '', url);
        
        // Realizar la petición AJAX
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Actualizar el contenido de la tabla
            document.getElementById('resultados-container').innerHTML = data.table;
            
            // Actualizar la paginación
            document.getElementById('pagination-container').innerHTML = data.pagination;
            
            // Actualizar el contador de clientes
            document.getElementById('total-clientes').textContent = data.totalClientes;
        })
        .catch(error => {
            console.error('Error al filtrar clientes:', error);
            document.getElementById('resultados-container').innerHTML = '<div class="alert alert-danger">Error al cargar los datos. Por favor, intente nuevamente.</div>';
        });
    }
    
    // Función debounce para evitar múltiples peticiones al escribir
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }
    
    // Manejar la paginación de forma dinámica (delegación de eventos)
    document.addEventListener('click', function(e) {
        // Verificar si se hizo clic en un enlace de paginación
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            const url = e.target.closest('.pagination a').href;
            
            // Realizar la petición AJAX a la página seleccionada
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Actualizar el contenido de la tabla
                document.getElementById('resultados-container').innerHTML = data.table;
                
                // Actualizar la paginación
                document.getElementById('pagination-container').innerHTML = data.pagination;
                
                // Actualizar la URL del navegador
                window.history.pushState({}, '', url);
            })
            .catch(error => {
                console.error('Error al cambiar de página:', error);
            });
        }
    });
});
</script>
@endpush