@extends('admin.layouts.app')

@section('title', 'Editar Categoría de Proveedor')

@section('content')
<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-edit text-info me-2"></i> Editar Categoría
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6 text-shadow-sm d-flex align-items-center">
                    {{ $categoriaProveedor->nombre_categoria_proveedor }}
                </h2>
                <p class="text-white-50 mb-0">Modifica los detalles de esta clasificación.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.compras.proveedores.categorias.index') }}" class="btn bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0">
                    <i class="fas fa-arrow-left text-primary me-2"></i> Volver a Listado
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-lg-4 position-relative" style="top: -3.5rem; z-index: 10;">
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <!-- Panel principal -->
        <div class="card dashboard-card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <!-- Formulario -->
                <form method="POST" action="{{ route('admin.compras.proveedores.categorias.update', $categoriaProveedor) }}" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label for="nombre_categoria_proveedor" class="form-label small text-muted mb-1">Nombre de la Categoría de Proveedor</label>
                        <input type="text" 
                               name="nombre_categoria_proveedor" 
                               id="nombre_categoria_proveedor" 
                               class="form-control form-control-sm @error('nombre_categoria_proveedor') is-invalid @enderror" 
                               :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                               placeholder="Ingrese el nombre de la categoría" 
                               value="{{ old('nombre_categoria_proveedor', $categoriaProveedor->nombre_categoria_proveedor) }}" 
                               required>
                        @error('nombre_categoria_proveedor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Botones de acción -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.compras.proveedores.categorias.index') }}" class="btn btn-outline-secondary btn-sm">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="me-2" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            Actualizar Categoría
                        </button>
                    </div>
                </form>
            </div>
        </div>
</div>
</div>
</div>
@endsection

@push('scripts')
    <!-- Validación de formulario con Bootstrap -->
    <script>
        (function () {
            'use strict';
            window.addEventListener('load', function () {
                var forms = document.getElementsByClassName('needs-validation');
                Array.prototype.filter.call(forms, function (form) {
                    form.addEventListener('submit', function (event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
@endpush
