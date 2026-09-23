@extends('admin.layouts.app')

@section('title', 'Editar Categoría de Servicios')

@section('content')
<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-edit text-info me-2"></i> Edición de Registro
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6 text-shadow-sm d-flex align-items-center">
                    Editar Categoría
                </h2>
                <p class="text-white-50 mb-0">Actualiza el nombre de la categoría: {{ $categoria->nombre }}</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.productos-servicios.servicios.categorias.index') }}" class="btn bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0">
                    <i class="fas fa-arrow-left text-primary me-2"></i> Volver al Listado
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-lg-4 position-relative" style="top: -3.5rem; z-index: 10;">
    <div class="card dashboard-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <!-- Formulario -->
            <form method="POST" action="{{ route('admin.productos-servicios.servicios.categorias.update', $categoria) }}" class="needs-validation" novalidate>
                @csrf
                @method('PUT')
                <div class="row g-4">
                    <div class="col-md-12">
                        <label for="nombre" class="form-label fw-bold small text-muted text-uppercase mb-2">Nombre de la Categoría *</label>
                        <input type="text" 
                               name="nombre" 
                               id="nombre" 
                               class="form-control rounded-pill px-3 shadow-sm border-light @error('nombre') is-invalid @enderror" 
                               placeholder="Ingrese el nombre" 
                               value="{{ old('nombre', $categoria->nombre) }}" 
                               required>
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="d-flex justify-content-end gap-2 mt-5 pt-4 border-top">
                    <a href="{{ route('admin.productos-servicios.servicios.categorias.index') }}" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm transition hover:scale-105 border-0">
                        <i class="fas fa-times me-2 text-danger"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm transition hover:scale-105 border-0">
                        <i class="fas fa-save me-2"></i> Actualizar Categoría
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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