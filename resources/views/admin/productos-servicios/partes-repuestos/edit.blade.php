@extends('admin.layouts.app')

@section('title', 'Editar Parte')

@section('header', 'Editar Parte')

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
                    Editar Parte: {{ $parte->nombre }}
                </h2>
                <p class="text-white-50 mb-0">Actualiza la información técnica y comercial de la parte</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.almacenes.partes.index') }}" class="btn bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0">
                    <i class="fas fa-arrow-left text-primary me-2"></i> Volver al Catálogo
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-lg-4 position-relative" style="top: -3.5rem; z-index: 10;">
    <div class="card dashboard-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
                <!-- Formulario -->
                <form method="POST" action="{{ route('admin.almacenes.partes.update', $parte) }}" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="codigo" class="form-label small text-muted mb-1">Código *</label>
                            <input type="text" 
                                   name="codigo" 
                                   id="codigo" 
                                   class="form-control form-control-sm @error('codigo') is-invalid @enderror" 
                                   :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                   placeholder="Ingrese el código de la parte" 
                                   value="{{ old('codigo', $parte->codigo) }}" 
                                   required>
                            @error('codigo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="autogenerar_codigo" class="form-label small text-muted mb-1">Autogenerar Código *</label>
                            <select name="autogenerar_codigo" 
                                    id="autogenerar_codigo" 
                                    class="form-select form-control-sm @error('autogenerar_codigo') is-invalid @enderror" 
                                    :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                    required>
                                <option value="1" {{ old('autogenerar_codigo', $parte->autogenerar_codigo) ? 'selected' : '' }}>Sí</option>
                                <option value="0" {{ old('autogenerar_codigo', $parte->autogenerar_codigo) ? '' : 'selected' }}>No</option>
                            </select>
                            @error('autogenerar_codigo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="nombre" class="form-label small text-muted mb-1">Nombre del Producto *</label>
                            <input type="text" 
                                   name="nombre" 
                                   id="nombre" 
                                   class="form-control form-control-sm @error('nombre') is-invalid @enderror" 
                                   :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                   placeholder="Ingrese el nombre del producto" 
                                   value="{{ old('nombre', $parte->nombre) }}" 
                                   required>
                            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="unidad_id" class="form-label small text-muted mb-1">Unidad *</label>
                            <select name="unidad_id" 
                                    id="unidad_id" 
                                    class="form-select form-control-sm @error('unidad_id') is-invalid @enderror" 
                                    :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                    required>
                                <option value="">Seleccionar</option>
                                @foreach ($unidades as $unidad)
                                    <option value="{{ $unidad->id }}" {{ old('unidad_id', $parte->unidad_id) == $unidad->id ? 'selected' : '' }}>{{ $unidad->nombre }}</option>
                                @endforeach
                            </select>
                            @error('unidad_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="fabricante_id" class="form-label small text-muted mb-1">Fabricante *</label>
                            <select name="fabricante_id" 
                                    id="fabricante_id" 
                                    class="form-select form-control-sm @error('fabricante_id') is-invalid @enderror" 
                                    :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                    required>
                                <option value="">Seleccionar</option>
                                @foreach ($fabricantes as $fabricante)
                                    <option value="{{ $fabricante->id }}" {{ old('fabricante_id', $parte->fabricante_id) == $fabricante->id ? 'selected' : '' }}>{{ $fabricante->nombre_fabricante }}</option>
                                @endforeach
                            </select>
                            @error('fabricante_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="proveedor_id" class="form-label small text-muted mb-1">Proveedor</label>
                            <select name="proveedor_id" 
                                    id="proveedor_id" 
                                    class="form-select form-control-sm @error('proveedor_id') is-invalid @enderror" 
                                    :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''">
                                <option value="">Seleccionar (Opcional)</option>
                                @foreach ($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}" {{ old('proveedor_id', $parte->proveedor_id) == $proveedor->id ? 'selected' : '' }}>
                                        {{ $proveedor->nombre_completo }}
                                    </option>
                                @endforeach
                            </select>
                            @error('proveedor_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="categoria_parte_id" class="form-label small text-muted mb-1">Categoría *</label>
                            <select name="categoria_parte_id" 
                                    id="categoria_parte_id" 
                                    class="form-select form-control-sm @error('categoria_parte_id') is-invalid @enderror" 
                                    :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                    required>
                                <option value="">Seleccionar</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" {{ old('categoria_parte_id', $parte->categoria_parte_id) == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                                @endforeach
                            </select>
                            @error('categoria_parte_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="precio_venta" class="form-label small text-muted mb-1">Precio de Venta *</label>
                            <div class="input-group">
                                <input type="number" 
                                       name="precio_venta" 
                                       id="precio_venta" 
                                       class="form-control form-control-sm @error('precio_venta') is-invalid @enderror" 
                                       :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                       placeholder="Ingrese el precio de venta" 
                                       value="{{ old('precio_venta', $parte->precio_venta) }}" 
                                       step="0.01" 
                                       min="0" 
                                       required>
                                <select name="moneda_venta" 
                                        class="form-select form-control-sm @error('moneda_venta') is-invalid @enderror" 
                                        :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                        style="max-width: 120px;" 
                                        required>
                                    <option value="SOL" {{ old('moneda_venta', $parte->moneda_venta) == 'SOL' ? 'selected' : '' }}>Soles</option>
                                    <option value="USD" {{ old('moneda_venta', $parte->moneda_venta) == 'USD' ? 'selected' : '' }}>Dólares</option>
                                </select>
                                @error('precio_venta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @error('moneda_venta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="precio_compra" class="form-label small text-muted mb-1">Precio de Compra Referencial *</label>
                            <div class="input-group">
                                <input type="number" 
                                       name="precio_compra" 
                                       id="precio_compra" 
                                       class="form-control form-control-sm @error('precio_compra') is-invalid @enderror" 
                                       :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                       placeholder="Ingrese el precio de compra" 
                                       value="{{ old('precio_compra', $parte->precio_compra) }}" 
                                       step="0.01" 
                                       min="0" 
                                       required>
                                <select name="moneda_compra" 
                                        class="form-select form-control-sm @error('moneda_compra') is-invalid @enderror" 
                                        :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                        style="max-width: 120px;" 
                                        required>
                                    <option value="SOL" {{ old('moneda_compra', $parte->moneda_compra) == 'SOL' ? 'selected' : '' }}>Soles</option>
                                    <option value="USD" {{ old('moneda_compra', $parte->moneda_compra) == 'USD' ? 'selected' : '' }}>Dólares</option>
                                </select>
                                @error('precio_compra') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @error('moneda_compra') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                        <a href="{{ route('admin.almacenes.partes.index') }}" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm transition hover:scale-105 border-0">
                            <i class="fas fa-times me-2 text-danger"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm transition hover:scale-105 border-0">
                            <i class="fas fa-save me-2"></i> Actualizar Parte
                        </button>
                    </div>
                </form>
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