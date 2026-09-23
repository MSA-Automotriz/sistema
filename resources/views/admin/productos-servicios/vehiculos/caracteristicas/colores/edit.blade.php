@extends('admin.layouts.app')

@section('title', 'Editar Color')

@section('header', 'Editar Color')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
                    <div class="mb-3 mb-sm-0">
                        <h2 class="h4 fw-bold mb-1" :class="darkMode ? 'text-light' : 'text-dark'">
                            Editar Color: {{ $color->nombre }}
                        </h2>
                        <p class="text-muted small mb-0">Actualiza los datos del color desde aquí</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.productos-servicios.vehiculos.caracteristicas.colores.update', $color) }}" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nombre" class="form-label small text-muted mb-1">Nombre del Color *</label>
                            <input type="text" 
                                   name="nombre" 
                                   id="nombre" 
                                   class="form-control form-control-sm @error('nombre') is-invalid @enderror" 
                                   :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                   placeholder="Ej: Rojo" 
                                   value="{{ old('nombre', $color->nombre) }}" 
                                   required>
                            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="hexadecimal" class="form-label small text-muted mb-1">Color (Hexadecimal) *</label>
                            <div class="input-group">
                                <input type="color" 
                                       id="colorPicker" 
                                       class="form-control form-control-sm p-0 border-0" 
                                       style="width: 40px;" 
                                       value="{{ old('hexadecimal', $color->hexadecimal) }}" 
                                       onchange="document.getElementById('hexadecimal').value = this.value">
                                <input type="text" 
                                       name="hexadecimal" 
                                       id="hexadecimal" 
                                       class="form-control form-control-sm @error('hexadecimal') is-invalid @enderror" 
                                       :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                       placeholder="Ej: #FF0000" 
                                       value="{{ old('hexadecimal', $color->hexadecimal) }}" 
                                       required>
                            </div>
                            @error('hexadecimal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.productos-servicios.vehiculos.caracteristicas.colores.index') }}" class="btn btn-outline-secondary btn-sm">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="me-2" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            Guardar Color
                        </button>
                    </div>
                </form>
            </div>
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