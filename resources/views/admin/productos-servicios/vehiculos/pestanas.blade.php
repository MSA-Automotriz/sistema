@extends('admin.layouts.app')

@section('title', 'Catálogo de Vehículos')

@section('content')
<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-car text-info me-2"></i> Gestión de Productos
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6 text-shadow-sm d-flex align-items-center">
                    Catálogo de Vehículos
                </h2>
                <p class="text-white-50 mb-0">Administra marcas, modelos, versiones y características técnicas</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.productos-servicios.vehiculos.import.form') }}" class="btn bg-white bg-opacity-10 text-white rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border border-white border-opacity-25 backdrop-blur me-2">
                    <i class="fas fa-file-excel me-2"></i> Importar Datos
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-lg-4 position-relative" style="top: -3.5rem; z-index: 10;">
    <div class="card dashboard-card border-0 shadow-sm mb-4">
        <div class="card-body p-0">
            <!-- Pestañas Modernas -->
            <ul class="nav nav-tabs nav-fill border-0 premium-tabs" id="vehiculosTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active fw-bold py-3" id="vehiculoscat-tab" data-bs-toggle="tab" data-bs-target="#vehiculoscat" 
                       href="{{ route('admin.productos-servicios.vehiculos.caracteristicas.vehiculo.index') }}" type="button" role="tab">
                       <i class="fas fa-car-side me-2"></i> Catálogo
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link fw-bold py-3" id="marcas-tab" data-bs-toggle="tab" data-bs-target="#marcas" 
                       href="{{ route('admin.productos-servicios.vehiculos.caracteristicas.marcas.index') }}" type="button" role="tab">
                       <i class="fas fa-copyright me-2"></i> Marcas
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link fw-bold py-3" id="modelos-tab" data-bs-toggle="tab" data-bs-target="#modelos" 
                       href="{{ route('admin.productos-servicios.vehiculos.caracteristicas.modelos.index') }}" type="button" role="tab">
                       <i class="fas fa-tags me-2"></i> Modelos
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link fw-bold py-3" id="versiones-tab" data-bs-toggle="tab" data-bs-target="#versiones" 
                       href="{{ route('admin.productos-servicios.vehiculos.caracteristicas.versiones.index') }}" type="button" role="tab">
                       <i class="fas fa-code-branch me-2"></i> Versiones
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link fw-bold py-3" id="anios-modelo-tab" data-bs-toggle="tab" data-bs-target="#anios-modelo" 
                       href="{{ route('admin.productos-servicios.vehiculos.caracteristicas.anios-modelo.index') }}" type="button" role="tab">
                       <i class="fas fa-calendar-alt me-2"></i> Años
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link fw-bold py-3" id="colores-tab" data-bs-toggle="tab" data-bs-target="#colores" 
                       href="{{ route('admin.productos-servicios.vehiculos.caracteristicas.colores.index') }}" type="button" role="tab">
                       <i class="fas fa-palette me-2"></i> Colores
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link fw-bold py-3" id="importar-tab" data-bs-toggle="tab" data-bs-target="#importar" 
                       href="{{ route('admin.productos-servicios.vehiculos.import.form') }}" type="button" role="tab">
                       <i class="fas fa-file-import me-2"></i> Importar
                    </a>
                </li>
            </ul>

            <div class="p-4">
                <!-- Contenido de las Pestañas -->
                <div class="tab-content" id="vehiculosTabContent">
                    <!-- Vehículos Catálogo -->
                    <div class="tab-pane fade show active" id="vehiculoscat" role="tabpanel">
                        <div id="vehiculoscat-content">
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary mb-3" role="status"></div>
                                <p class="text-muted">Cargando catálogo...</p>
                            </div>
                        </div>
                    </div>
                    <!-- Marcas -->
                    <div class="tab-pane fade" id="marcas" role="tabpanel">
                        <div id="marcas-content"></div>
                    </div>
                    <!-- Modelos -->
                    <div class="tab-pane fade" id="modelos" role="tabpanel">
                        <div id="modelos-content"></div>
                    </div>
                    <!-- Versiones -->
                    <div class="tab-pane fade" id="versiones" role="tabpanel">
                        <div id="versiones-content"></div>
                    </div>
                    <!-- Años de Modelo -->
                    <div class="tab-pane fade" id="anios-modelo" role="tabpanel">
                        <div id="anios-modelo-content"></div>
                    </div>
                    <!-- Colores -->
                    <div class="tab-pane fade" id="colores" role="tabpanel">
                        <div id="colores-content"></div>
                    </div>
                    <!-- Importar Excel -->
                    <div class="tab-pane fade" id="importar" role="tabpanel">
                        <div id="importar-content">
                            @if (session('success'))
                                <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
                                    <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                                </div>
                            @endif
                            
                            <div class="row">
                                <div class="col-lg-5">
                                    <div class="card bg-light border-0 rounded-4 h-100">
                                        <div class="card-body p-4">
                                            <h5 class="fw-bold mb-3 d-flex align-items-center text-primary">
                                                <i class="fas fa-cloud-upload-alt me-2"></i> Subida de Archivo
                                            </h5>
                                            <form action="{{ route('admin.productos-servicios.vehiculos.import') }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="mb-4">
                                                    <label for="file" class="form-label small text-muted text-uppercase fw-bold mb-2">Seleccionar archivo Excel (.xlsx, .xls)</label>
                                                    <input type="file" name="file" id="file" accept=".xlsx,.xls" class="form-control rounded-pill px-3 shadow-sm border-0 @error('file') is-invalid @enderror">
                                                    @error('file')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold py-2 shadow-sm transition hover:scale-105 border-0">
                                                    <i class="fas fa-file-import me-2"></i> Procesar Importación
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <div class="card border-light rounded-4 h-100">
                                        <div class="card-body p-4">
                                            <h5 class="fw-bold mb-3 text-dark">
                                                <i class="fas fa-info-circle text-info me-2"></i> Guía de Formato Excel
                                            </h5>
                                            <p class="text-muted small mb-3">Asegúrate de que tu archivo tenga las siguientes columnas en el orden exacto o con los nombres correspondientes:</p>
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <ul class="list-group list-group-flush rounded-3 overflow-hidden border">
                                                        <li class="list-group-item small py-2"><strong>Fecha de compra</strong>: YYYY-MM-DD</li>
                                                        <li class="list-group-item small py-2"><strong>Precio compra</strong>: Decimal (ej. 25000.50)</li>
                                                        <li class="list-group-item small py-2"><strong>Nro de factura</strong>: Texto</li>
                                                        <li class="list-group-item small py-2"><strong>Marca</strong>: Nombre de marca</li>
                                                        <li class="list-group-item small py-2"><strong>Modelo</strong>: Nombre de modelo</li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <ul class="list-group list-group-flush rounded-3 overflow-hidden border">
                                                        <li class="list-group-item small py-2"><strong>Version</strong>: Nombre de versión</li>
                                                        <li class="list-group-item small py-2"><strong>Año</strong>: Año (ej. 2023)</li>
                                                        <li class="list-group-item small py-2"><strong>Serie VIN</strong>: Código VIN</li>
                                                        <li class="list-group-item small py-2"><strong>Color</strong>: Nombre del color</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .premium-tabs .nav-link {
        color: #6c757d;
        border: none;
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
    }
    .premium-tabs .nav-link:hover {
        background-color: rgba(0, 123, 255, 0.05);
        color: #0d6efd;
    }
    .premium-tabs .nav-link.active {
        color: #0d6efd;
        background-color: transparent;
        border-bottom-color: #0d6efd;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cargar contenido de la pestaña activa por defecto
    loadTabContent('vehiculoscat');
    
    // Manejar clics en las pestañas
    document.querySelectorAll('#vehiculosTab a').forEach(function(tab) {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Desactivar todas las pestañas
            document.querySelectorAll('#vehiculosTab a').forEach(function(t) {
                t.classList.remove('active');
                let tabPanel = document.querySelector(t.getAttribute('data-bs-target'));
                if (tabPanel) {
                    tabPanel.classList.remove('show', 'active');
                }
            });
            
            // Activar esta pestaña
            this.classList.add('active');
            let targetPanel = document.querySelector(this.getAttribute('data-bs-target'));
            if (targetPanel) {
                targetPanel.classList.add('show', 'active');
            }
            
            // Obtener el ID del tab
            let tabId = this.getAttribute('id').replace('-tab', '');
            
            // No cargar contenido para la pestaña de importar, ya que está renderizada directamente
            if (tabId !== 'importar') {
                loadTabContent(tabId);
            }
        });
    });
    
    function loadTabContent(tabId) {
        let contentContainer = document.getElementById(tabId + '-content');
        
        // No recargar si ya tiene contenido (opcional, para esta demo recargamos para asegurar)
        contentContainer.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary mb-3" role="status"></div><p class="text-muted">Cargando...</p></div>';

        let url = document.getElementById(tabId + '-tab').getAttribute('href');
        
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al cargar datos del servidor');
                }
                return response.text();
            })
            .then(html => {
                let tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                
                let content = null;
                // Intentar extraer el contenido útil (ajustar según la estructura de las vistas hijas)
                let mainContent = tempDiv.querySelector('.card-body') || tempDiv.querySelector('.container') || tempDiv.querySelector('#app');
                
                if (mainContent) {
                    // Limpiar scripts y estilos del contenido inyectado para evitar colisiones
                    let scripts = mainContent.querySelectorAll('script');
                    scripts.forEach(s => s.remove());
                    content = mainContent.innerHTML;
                } else {
                    // Fallback si no se encuentra un contenedor estándar
                    let head = tempDiv.querySelector('head');
                    if (head) head.remove();
                    content = tempDiv.innerHTML;
                }
                
                contentContainer.innerHTML = content;
                
                // Reinicializar tooltips si es necesario
                if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                }
            })
            .catch(error => {
                console.error('Error al cargar el contenido:', error);
                contentContainer.innerHTML = 
                    '<div class="alert alert-danger border-0 rounded-4 shadow-sm"><i class="fas fa-exclamation-circle me-2"></i> No se pudo cargar la información: ' + error.message + '</div>';
            });
    }
});
</script>
@endpush
@endsection