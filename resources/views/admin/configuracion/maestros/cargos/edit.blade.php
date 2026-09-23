@extends('admin.layouts.app')

@section('title', 'Editar cargo')

@section('header', 'Editar cargo')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Panel principal -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <!-- Header -->
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
                    <div class="mb-3 mb-sm-0">
                        <h2 class="h4 fw-bold mb-1" :class="darkMode ? 'text-light' : 'text-dark'">
                            Editar cargo
                        </h2>
                        <p class="text-muted small mb-0">Actualiza la información del cargo desde aquí</p>
                    </div>
                </div>

                <!-- Formulario -->
                <form method="POST" action="{{ route('admin.configuracion.maestros.cargos.update', $cargo) }}" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label for="nombre_cargo" class="form-label small text-muted mb-1">Nombre del cargo</label>
                        <input type="text" 
                               name="nombre_cargo" 
                               id="nombre_cargo" 
                               class="form-control form-control-sm @error('nombre_cargo') is-invalid @enderror" 
                               :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                               placeholder="Ingrese el nombre del cargo" 
                               value="{{ old('nombre_cargo', $cargo->nombre_cargo) }}" 
                               required>
                        @error('nombre_cargo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Botones de acción -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.configuracion.maestros.cargos.index') }}" class="btn btn-outline-secondary btn-sm">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="me-2" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            Actualizar cargo
                        </button>
                    </div>
                </form>
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