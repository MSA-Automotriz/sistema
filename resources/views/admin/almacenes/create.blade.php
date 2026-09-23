@extends('admin.layouts.app')

@section('title', 'Nuevo Almacén')

@section('header', 'Nuevo Almacén')

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
                            Crear Nuevo Almacén
                        </h2>
                        <p class="text-muted small mb-0">Registra un nuevo almacén o subalmacén desde aquí</p>
                    </div>
                </div>

                <!-- Formulario -->
                <form method="POST" action="{{ route('admin.almacenes.store') }}" class="needs-validation" novalidate>
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nombre" class="form-label small text-muted mb-1">Nombre del Almacén *</label>
                            <input type="text" 
                                   name="nombre" 
                                   id="nombre" 
                                   class="form-control form-control-sm @error('nombre') is-invalid @enderror" 
                                   :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                   placeholder="Ingrese el nombre del almacén" 
                                   value="{{ old('nombre') }}" 
                                   required>
                            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="direccion" class="form-label small text-muted mb-1">Dirección *</label>
                            <input type="text" 
                                   name="direccion" 
                                   id="direccion" 
                                   class="form-control form-control-sm @error('direccion') is-invalid @enderror" 
                                   :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                   placeholder="Ingrese la dirección del almacén" 
                                   value="{{ old('direccion') }}" 
                                   required>
                            @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="es_vehiculos" class="form-label small text-muted mb-1">¿Almacén de Vehículos? *</label>
                            <select name="es_vehiculos" 
                                    id="es_vehiculos" 
                                    class="form-select form-control-sm @error('es_vehiculos') is-invalid @enderror" 
                                    :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                    required>
                                <option value="">Seleccione una opción</option>
                                <option value="1" {{ old('es_vehiculos') == '1' ? 'selected' : '' }}>Sí</option>
                                <option value="0" {{ old('es_vehiculos') == '0' ? 'selected' : '' }}>No</option>
                            </select>
                            @error('es_vehiculos') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="centro_costo_id" class="form-label small text-muted mb-1">Centro de Costo *</label>
                            <select name="centro_costo_id" 
                                    id="centro_costo_id" 
                                    class="form-select form-control-sm @error('centro_costo_id') is-invalid @enderror" 
                                    :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                    required>
                                <option value="">Seleccione un centro de costo</option>
                                @foreach ($centrosCostos as $centro)
                                    <option value="{{ $centro->id }}" {{ old('centro_costo_id') == $centro->id ? 'selected' : '' }}>
                                        {{ $centro->codigo }} - {{ $centro->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('centro_costo_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="parent_id" class="form-label small text-muted mb-1">Almacén Padre (Opcional)</label>
                            <select name="parent_id" 
                                    id="parent_id" 
                                    class="form-select form-control-sm @error('parent_id') is-invalid @enderror" 
                                    :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''">
                                <option value="">Ninguno (Almacén Principal)</option>
                                @foreach ($almacenes as $almacen)
                                    @include('admin.almacenes.partials.almacen-options', ['almacen' => $almacen, 'level' => 0])
                                @endforeach
                            </select>
                            @error('parent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.almacenes.index') }}" class="btn btn-outline-secondary btn-sm">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="me-2" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            Guardar Almacén
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