@extends('admin.layouts.app')

@section('title', 'Nueva Parte')

@section('header', 'Nueva Parte')

@section('content')
<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-plus text-info me-2"></i> Nuevo Registro
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6 text-shadow-sm d-flex align-items-center">
                    Crear Nueva Parte
                </h2>
                <p class="text-white-50 mb-0">Completa los datos para registrar un nuevo producto en el catálogo</p>
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
                <form method="POST" action="{{ route('admin.almacenes.partes.store') }}" class="needs-validation" novalidate>
                    @csrf
                    <div class="row g-3" x-data="scannerCodigo()">
                        <div class="col-md-4">
                            <label for="codigo" class="form-label small text-muted mb-1">Código *</label>
                            <div class="input-group input-group-sm">
                                <input type="text" 
                                       name="codigo" 
                                       id="codigo"
                                       x-ref="campoCodigo"
                                       class="form-control form-control-sm @error('codigo') is-invalid @enderror" 
                                       :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                       placeholder="Ingrese o escanee el código" 
                                       value="{{ old('codigo', $nuevoCodigo) }}" 
                                       required>
                                <button type="button"
                                        @click="abrirCamara()"
                                        class="btn btn-outline-secondary px-2"
                                        title="Escanear con cámara">
                                    <i class="fas fa-camera"></i>
                                </button>
                            </div>
                            @error('codigo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                            {{-- Vista de cámara --}}
                            <div x-show="cameraOpen" x-cloak class="mt-2 border rounded p-2 bg-dark">
                                <div id="qr-reader-create" style="width:100%;"></div>
                                <button type="button" @click="cerrarCamara()" class="btn btn-sm btn-danger mt-2 w-100">
                                    <i class="fas fa-times me-1"></i> Cerrar cámara
                                </button>
                            </div>
                            <div x-show="scanError" x-cloak class="text-danger small mt-1" x-text="scanError"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="autogenerar_codigo" class="form-label small text-muted mb-1">Autogenerar Código *</label>
                            <select name="autogenerar_codigo" 
                                    id="autogenerar_codigo" 
                                    class="form-select form-control-sm @error('autogenerar_codigo') is-invalid @enderror" 
                                    :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                    required>
                                <option value="1" {{ old('autogenerar_codigo') == '1' ? 'selected' : '' }}>Sí</option>
                                <option value="0" {{ old('autogenerar_codigo') == '0' ? 'selected' : '' }}>No</option>
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
                                   value="{{ old('nombre') }}" 
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
                                    <option value="{{ $unidad->id }}" {{ old('unidad_id') == $unidad->id ? 'selected' : '' }}>{{ $unidad->nombre }}</option>
                                @endforeach
                            </select>
                            @error('unidad_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="fabricante_id" class="form-label small text-muted mb-1">Fabricante *</label>
                            <select name="fabricante_id" 
                                    id="fabricante_id" 
                                    class="form-select form-control-sm @error('fabricante_id') is-invalid @enderror" 
                                    :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''">
                                <option value="">Seleccionar</option>
                                @foreach ($fabricantes as $fabricante)
                                    <option value="{{ $fabricante->id }}" {{ old('fabricante_id') == $fabricante->id ? 'selected' : '' }}>{{ $fabricante->nombre_fabricante }}</option>
                                @endforeach
                            </select>
                            @error('fabricante_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <!-- Agregar esto dentro del formulario en create.blade.php, junto con los otros campos -->
                        <div class="col-md-4">
                            <label for="proveedor_id" class="form-label small text-muted mb-1">Proveedor</label>
                            <select name="proveedor_id" 
                                    id="proveedor_id" 
                                    class="form-select form-control-sm @error('proveedor_id') is-invalid @enderror" 
                                    :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''">
                                <option value="">Seleccionar (Opcional)</option>
                                @foreach ($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}" {{ old('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                                        {{ $proveedor->nombre_completo }} ({{ $proveedor->documento_formateado }})
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
                                    <option value="{{ $categoria->id }}" {{ old('categoria_parte_id') == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                                @endforeach
                            </select>
                            @error('categoria_parte_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="precio_venta" class="form-label small text-muted mb-1">Precio de Venta *</label>
                            <div class="input-group">
                                <input type="number" 
                                       name="precio_venta" 
                                       id="precio_venta" 
                                       class="form-control form-control-sm @error('precio_venta') is-invalid @enderror" 
                                       :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                       placeholder="Ingrese el precio de venta" 
                                       value="{{ old('precio_venta') }}" 
                                       step="0.01" 
                                       min="0" 
                                       required>
                                <select name="moneda_venta" 
                                        class="form-select form-control-sm @error('moneda_venta') is-invalid @enderror" 
                                        :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                        style="max-width: 120px;" 
                                        required>
                                    <option value="SOL" {{ old('moneda_venta') == 'SOL' ? 'selected' : '' }}>Soles</option>
                                    <option value="USD" {{ old('moneda_venta') == 'USD' ? 'selected' : '' }}>Dólares</option>
                                </select>
                                @error('precio_venta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @error('moneda_venta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="precio_compra" class="form-label small text-muted mb-1">Precio de Compra Referencial *</label>
                            <div class="input-group">
                                <input type="number" 
                                       name="precio_compra" 
                                       id="precio_compra" 
                                       class="form-control form-control-sm @error('precio_compra') is-invalid @enderror" 
                                       :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                       placeholder="Ingrese el precio de compra" 
                                       value="{{ old('precio_compra') }}" 
                                       step="0.01" 
                                       min="0" 
                                       required>
                                <select name="moneda_compra" 
                                        class="form-select form-control-sm @error('moneda_compra') is-invalid @enderror" 
                                        :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                        style="max-width: 120px;" 
                                        required>
                                    <option value="SOL" {{ old('moneda_compra') == 'SOL' ? 'selected' : '' }}>Soles</option>
                                    <option value="USD" {{ old('moneda_compra') == 'USD' ? 'selected' : '' }}>Dólares</option>
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
                            <i class="fas fa-save me-2"></i> Guardar Parte
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
function scannerCodigo() {
    return {
        cameraOpen: false,
        scanError: '',
        qrScanner: null,

        async abrirCamara() {
            this.scanError = '';

            if (!window.isSecureContext) {
                this.scanError = '⚠️ La cámara requiere HTTPS. En Chrome: chrome://flags/#unsafely-treat-insecure-origin-as-secure → agrega http://' + window.location.hostname + ' → Enabled → Relaunch.';
                return;
            }
            if (typeof Html5Qrcode === 'undefined') {
                this.scanError = 'Librería no cargada. Recarga la página.';
                return;
            }

            this.cameraOpen = true;
            await this.$nextTick();
            await new Promise(r => setTimeout(r, 150));

            try {
                this.qrScanner = new Html5Qrcode('qr-reader-create');
                await this.qrScanner.start(
                    { facingMode: 'environment' },
                    { fps: 10, qrbox: { width: 260, height: 120 } },
                    (codigo) => {
                        this.qrScanner.stop().catch(() => {});
                        this.qrScanner = null;
                        this.cameraOpen = false;
                        this.$refs.campoCodigo.value = codigo;
                        this.$refs.campoCodigo.focus();
                    },
                    () => {}
                );
            } catch (err) {
                if (err.name === 'NotAllowedError') {
                    this.scanError = 'Permiso de cámara denegado. Toca el ícono de cámara en la barra del navegador y permite el acceso.';
                } else if (err.name === 'NotFoundError') {
                    this.scanError = 'No se encontró ninguna cámara en este dispositivo.';
                } else {
                    this.scanError = 'No se pudo acceder a la cámara: ' + (err.message || err.name);
                }
                this.cameraOpen = false;
                this.qrScanner = null;
            }
        },

        async cerrarCamara() {
            if (this.qrScanner) {
                await this.qrScanner.stop().catch(() => {});
                this.qrScanner = null;
            }
            this.cameraOpen = false;
        }
    };
}
</script>
@endpush

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

        document.getElementById('autogenerar_codigo').addEventListener('change', function() {
            document.getElementById('codigo').readOnly = this.value == '1';
            if (this.value == '1') {
                document.getElementById('codigo').value = '{{ $nuevoCodigo }}';
            }
        });
    </script>
@endpush