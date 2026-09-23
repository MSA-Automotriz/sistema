@extends('admin.layouts.app')

@section('title', 'Catálogo de Partes')

@section('content')
<div x-data="{
    carrito: [],
    scanInput: '',
    scanError: '',
    scanSuccess: false,
    scanning: false,
    cameraOpen: false,
    qrScanner: null,
    buscarUrl: '{{ route('admin.almacenes.partes.buscar-codigo') }}',

    async escanear() {
        const codigo = this.scanInput.trim();
        if (!codigo) return;
        this.scanning = true;
        this.scanError = '';
        try {
            const res = await fetch(this.buscarUrl + '?codigo=' + encodeURIComponent(codigo), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (data.found) {
                const existe = this.carrito.find(i => i.id === data.parte.id);
                if (existe) {
                    existe.cantidad++;
                } else {
                    this.carrito.push({ ...data.parte, cantidad: 1 });
                }
                this.scanSuccess = true;
                setTimeout(() => { this.scanSuccess = false; }, 800);            } else {
                this.scanError = data.message;
            }
        } catch(e) {
            this.scanError = 'Error al buscar el producto.';
        }
        this.scanning = false;
        this.scanInput = '';
        // Re-enfocar siempre para el siguiente escaneo con lector físico
        await this.$nextTick();
        this.$refs.scanInput.focus();
    },

    quitarItem(id) {
        this.carrito = this.carrito.filter(i => i.id !== id);
    },

    async abrirCamara() {
        this.scanError = '';

        // Los navegadores solo permiten cámara en contextos seguros (HTTPS o localhost)
        if (!window.isSecureContext) {
            this.scanError = '⚠️ La cámara requiere HTTPS. En Chrome (Android): abre chrome://flags/#unsafely-treat-insecure-origin-as-secure → agrega http://' + window.location.hostname + ' → Reiniciar.';
            return;
        }

        if (typeof Html5Qrcode === 'undefined') {
            this.scanError = 'Librería de cámara no cargada. Recarga la página.';
            return;
        }

        // Verificar que el navegador tiene API de cámara
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            this.scanError = 'Este navegador no soporta acceso a la cámara.';
            return;
        }

        this.cameraOpen = true;
        await this.$nextTick();
        await new Promise(r => setTimeout(r, 100));
        try {
            this.qrScanner = new Html5Qrcode('qr-reader');
            await this.qrScanner.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 260, height: 120 } },
                async (codigo) => {
                    await this.qrScanner.stop().catch(() => {});
                    this.qrScanner = null;
                    this.cameraOpen = false;
                    this.scanInput = codigo;
                    await this.escanear();
                },
                () => {}
            );
        } catch(err) {
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
    },

    get total() {
        return this.carrito.reduce((s, i) => s + (i.precio_venta * i.cantidad), 0).toFixed(2);
    },

    // ===== MODAL PROCESAR CARRITO =====
    modalCarrito: false,
    destinoCarrito: '',
    // Venta
    clienteBusqueda: '',
    clientesResultados: [],
    clienteSeleccionado: null,
    buscandoClientes: false,
    formaPago: 'Contado',
    tipoDocumento: 'Boleta',
    monedaVenta: 'Soles',
    procesandoVenta: false,
    resultadoVenta: null,
    // Taller
    ordenBusqueda: '',
    ordenesResultados: [],
    ordenSeleccionada: null,
    buscandoOrdenes: false,
    procesandoTaller: false,
    resultadoTaller: null,

    abrirModal() {
        this.modalCarrito = true;
        this.destinoCarrito = '';
        this.clienteBusqueda = '';
        this.clientesResultados = [];
        this.clienteSeleccionado = null;
        this.formaPago = 'Contado';
        this.tipoDocumento = 'Boleta';
        this.monedaVenta = 'Soles';
        this.resultadoVenta = null;
        this.ordenBusqueda = '';
        this.ordenesResultados = [];
        this.ordenSeleccionada = null;
        this.resultadoTaller = null;
    },

    async buscarClientesModal() {
        if (this.clienteBusqueda.length < 2) { this.clientesResultados = []; return; }
        this.buscandoClientes = true;
        try {
            const res = await fetch('{{ route('admin.ventas.pos.buscar-clientes') }}?query=' + encodeURIComponent(this.clienteBusqueda), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const data = await res.json();
            this.clientesResultados = data.items || [];
        } catch(e) { this.clientesResultados = []; }
        this.buscandoClientes = false;
    },

    seleccionarCliente(c) {
        this.clienteSeleccionado = c;
        this.clienteBusqueda = c.nombre;
        this.clientesResultados = [];
    },

    async buscarOrdenesActivas() {
        if (this.ordenBusqueda.length < 1) { this.ordenesResultados = []; return; }
        this.buscandoOrdenes = true;
        try {
            const res = await fetch('{{ route('admin.mantenimiento.ordenes.buscar-activas') }}?q=' + encodeURIComponent(this.ordenBusqueda), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            this.ordenesResultados = await res.json();
        } catch(e) { this.ordenesResultados = []; }
        this.buscandoOrdenes = false;
    },

    async confirmarVenta() {
        this.procesandoVenta = true;
        this.resultadoVenta = null;
        const items = this.carrito.map(i => ({ id: i.id, tipo: 'parte', cantidad: i.cantidad, precio: i.precio_venta, descuento: 0 }));
        try {
            const res = await fetch('{{ route('admin.ventas.pos.procesar-venta') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    items,
                    moneda: this.monedaVenta,
                    condicion: 'Nuevo',
                    forma_pago: this.formaPago,
                    tipo_documento: this.tipoDocumento,
                    cliente_id: this.clienteSeleccionado ? this.clienteSeleccionado.id : null
                })
            });
            const data = await res.json();
            this.resultadoVenta = data;
            if (data.success) this.carrito = [];
        } catch(e) { this.resultadoVenta = { success: false, message: 'Error de conexión.' }; }
        this.procesandoVenta = false;
    },

    async confirmarTaller() {
        if (!this.ordenSeleccionada) return;
        this.procesandoTaller = true;
        this.resultadoTaller = null;
        const items = this.carrito.map(i => ({ parte_id: i.id, cantidad: i.cantidad, precio_unitario: i.precio_venta }));
        try {
            const res = await fetch('/admin/mantenimiento/ordenes/' + this.ordenSeleccionada.id + '/agregar-repuestos-lote', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ items })
            });
            const data = await res.json();
            this.resultadoTaller = data;
            if (data.success) this.carrito = [];
        } catch(e) { this.resultadoTaller = { success: false, message: 'Error de conexión.' }; }
        this.procesandoTaller = false;
    }
}" x-init="$nextTick(() => $refs.scanInput && $refs.scanInput.focus())">

<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-cog text-info me-2"></i> Inventario de Partes
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6 text-shadow-sm d-flex align-items-center">
                    Catálogo de Partes Vehículos/Motos
                </h2>
                <p class="text-white-50 mb-0">Total de partes registradas: {{ $totalPartes }}</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.almacenes.partes.create') }}" class="btn bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0">
                    <i class="fas fa-plus text-primary me-2"></i> Agregar Parte
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-lg-4 position-relative" style="top: -3.5rem; z-index: 10;">

    {{-- SCANNER DE CÓDIGO DE BARRAS --}}
    <div class="card dashboard-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="fas fa-barcode text-primary"></i> Escanea aquí el Código de Barras
            </h6>
            <div class="d-flex gap-2 align-items-center">
                <div class="position-relative flex-grow-1" style="max-width: 400px;">
                    <span class="position-absolute top-50 translate-middle-y ms-3 text-muted"><i class="fas fa-barcode"></i></span>
                    <input type="text"
                           x-ref="scanInput"
                           x-model="scanInput"
                           @keydown.enter.prevent="escanear()"
                           placeholder="Ingresa el código o abre la cámara..."
                           class="form-control ps-5"
                           :class="scanSuccess ? 'border-success bg-success bg-opacity-10' : (scanError ? 'border-danger' : '')"
                           :disabled="scanning"
                           autocomplete="off"
                           autofocus>
                </div>
                <button @click="escanear()" :disabled="scanning || !scanInput.trim()" class="btn btn-primary px-4">
                    <span x-show="!scanning"><i class="fas fa-search me-1"></i> Buscar</span>
                    <span x-show="scanning"><i class="fas fa-spinner fa-spin me-1"></i> Buscando...</span>
                </button>
                <button @click="abrirCamara()" class="btn btn-outline-secondary px-3" title="Usar cámara del celular">
                    <i class="fas fa-camera me-1"></i> Cámara
                </button>
            </div>
            <div x-show="scanError" x-text="scanError" class="alert alert-danger mt-2 mb-0 py-2 px-3 rounded-3" style="font-size:0.875rem;"></div>

            {{-- VISOR CÁMARA --}}
            <div x-show="cameraOpen" x-transition class="mt-3 border rounded-3 overflow-hidden" style="max-width:420px; background:#000;">
                <div class="d-flex justify-content-between align-items-center px-3 py-2" style="background:#1e293b;">
                    <span class="text-white small fw-bold"><i class="fas fa-camera me-2 text-info"></i>Apunta al código de barras</span>
                    <button @click="cerrarCamara()" class="btn btn-sm btn-outline-light border-0 py-0 px-2">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="qr-reader" style="width:420px; min-height:300px;"></div>
            </div>
        </div>
    </div>

    {{-- CARRITO --}}
    <div x-cloak x-show="carrito.length > 0" class="card dashboard-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                    <i class="fas fa-shopping-cart text-success"></i> Carrito
                    <span class="badge bg-success rounded-pill ms-1" x-text="carrito.length + ' ítem(s)'"></span>
                </h6>
                <button @click="carrito = []" class="btn btn-outline-danger btn-sm rounded-pill">
                    <i class="fas fa-trash me-1"></i> Vaciar
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-2 px-3 border-0 text-uppercase small">Código</th>
                            <th class="py-2 px-3 border-0 text-uppercase small">Nombre</th>
                            <th class="py-2 px-3 border-0 text-uppercase small">Categoría</th>
                            <th class="py-2 px-3 border-0 text-uppercase small">Unidad</th>
                            <th class="py-2 px-3 border-0 text-uppercase small text-center">Cantidad</th>
                            <th class="py-2 px-3 border-0 text-uppercase small text-end">Precio Unit.</th>
                            <th class="py-2 px-3 border-0 text-uppercase small text-end">Subtotal</th>
                            <th class="py-2 px-3 border-0"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="item in carrito" :key="item.id">
                            <tr>
                                <td class="px-3 py-2 fw-bold text-primary" x-text="item.codigo"></td>
                                <td class="px-3 py-2" x-text="item.nombre"></td>
                                <td class="px-3 py-2"><span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3" x-text="item.categoria"></span></td>
                                <td class="px-3 py-2" x-text="item.unidad"></td>
                                <td class="px-3 py-2 text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <button @click="item.cantidad > 1 ? item.cantidad-- : quitarItem(item.id)" class="btn btn-outline-secondary btn-sm" style="width:28px;height:28px;padding:0;line-height:1;">
                                            <i class="fas fa-minus" style="font-size:0.6rem;"></i>
                                        </button>
                                        <input type="number" x-model.number="item.cantidad" min="1" class="form-control form-control-sm text-center fw-bold" style="width:60px;">
                                        <button @click="item.cantidad++" class="btn btn-outline-secondary btn-sm" style="width:28px;height:28px;padding:0;line-height:1;">
                                            <i class="fas fa-plus" style="font-size:0.6rem;"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-3 py-2 text-end fw-bold text-success">
                                    <span x-text="parseFloat(item.precio_venta).toFixed(2)"></span>
                                    <small x-text="item.moneda_venta" class="text-muted ms-1"></small>
                                </td>
                                <td class="px-3 py-2 text-end fw-bold">
                                    <span x-text="(item.precio_venta * item.cantidad).toFixed(2)"></span>
                                    <small x-text="item.moneda_venta" class="text-muted ms-1"></small>
                                </td>
                                <td class="px-3 py-2 text-end">
                                    <button @click="quitarItem(item.id)" class="btn btn-outline-danger btn-sm rounded-pill px-2">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="6" class="px-3 py-2 text-end">Total:</td>
                            <td class="px-3 py-2 text-end text-success fs-6" x-text="total"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="8" class="px-3 py-3 text-end border-0">
                                <button @click="abrirModal()" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow-sm">
                                    <i class="fas fa-arrow-circle-right me-2"></i> Procesar Carrito
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- TABLA CATÁLOGO --}}
    <div class="card dashboard-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            @if (session('success'))
                <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-4 border-0 text-uppercase small">#</th>
                            <th class="py-3 px-4 border-0 text-uppercase small">Código</th>
                            <th class="py-3 px-4 border-0 text-uppercase small">Nombre</th>
                            <th class="py-3 px-4 border-0 text-uppercase small">Unidad</th>
                            <th class="py-3 px-4 border-0 text-uppercase small">Fabricante</th>
                            <th class="py-3 px-4 border-0 text-uppercase small">Proveedor</th>
                            <th class="py-3 px-4 border-0 text-uppercase small">Categoría</th>
                            <th class="py-3 px-4 border-0 text-uppercase small">Precio Venta</th>
                            <th class="py-3 px-4 border-0 text-uppercase small text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($partes as $index => $parte)
                            <tr>
                                <td class="px-4 py-3">{{ $partes->firstItem() + $index }}</td>
                                <td class="px-4 py-3 fw-bold text-primary">{{ $parte->codigo }}</td>
                                <td class="px-4 py-3">{{ $parte->nombre }}</td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-light text-dark rounded-pill px-3">{{ $parte->unidad->nombre ?? 'N/A' }}</span>
                                </td>
                                <td class="px-4 py-3">{{ $parte->fabricante->nombre_fabricante ?? 'N/A' }}</td>
                                <td class="px-4 py-3">{{ $parte->proveedor ? $parte->proveedor->nombre_completo : 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3">{{ $parte->categoriaParte->nombre ?? 'N/A' }}</span>
                                </td>
                                <td class="px-4 py-3 fw-bold text-success">
                                    {{ number_format($parte->precio_venta, 2) }} {{ $parte->moneda_venta }}
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                                        <a href="{{ route('admin.almacenes.partes.edit', $parte) }}" class="btn btn-white btn-sm border-0 px-3 transition hover:bg-warning hover:text-white" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.almacenes.partes.destroy', $parte) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('¿Estás seguro de eliminar esta parte?')" class="btn btn-white btn-sm border-0 px-3 transition hover:bg-danger hover:text-white" title="Eliminar">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-5 text-center">
                                    <div class="bg-light d-inline-flex p-4 rounded-circle mb-3">
                                        <i class="fas fa-cog text-muted fa-3x"></i>
                                    </div>
                                    <h5 class="text-dark fw-bold">No hay partes registradas</h5>
                                    <p class="text-muted mb-0">Comienza agregando tu primera parte al catálogo</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $partes->links() }}
            </div>
        </div>
    </div>
</div>

{{-- MODAL: PROCESAR CARRITO (fuera del container-fluid para evitar conflictos de z-index) --}}
<template x-if="modalCarrito">
<div @keydown.escape.window="modalCarrito = false"
     @click.self="modalCarrito = false"
     class="position-fixed top-0 start-0 w-100 h-100"
     style="display:flex;align-items:center;justify-content:center;background:rgba(15,23,42,0.55);z-index:10000;">
    <div class="card shadow-lg border-0 rounded-4 mx-3" style="width:100%;max-width:540px;max-height:92vh;overflow-y:auto;">

        {{-- Cabecera --}}
        <div class="p-4 d-flex justify-content-between align-items-center rounded-top-4" style="background:linear-gradient(135deg,#1e293b,#0f172a);">
            <h5 class="mb-0 text-white fw-bold">
                <i class="fas fa-cart-arrow-down me-2 text-success"></i> Procesar Carrito
            </h5>
            <button type="button" @click.prevent.stop="modalCarrito = false"
                    class="btn btn-sm btn-outline-light border-0 rounded-circle"
                    style="width:32px;height:32px;padding:0;">
                <i class="fas fa-times" style="font-size:0.85rem;"></i>
            </button>
        </div>

        <div class="card-body p-4">

            {{-- PASO 1: ELEGIR DESTINO --}}
            <div x-show="!destinoCarrito">
                <p class="text-muted fw-semibold mb-3">¿A dónde se diriguen estos productos?</p>
                <div class="row g-3">
                    <div class="col-6">
                        <button type="button" @click="destinoCarrito='venta'"
                                class="btn btn-light border rounded-4 w-100 p-4 text-center h-100"
                                style="transition:.15s;border-color:#dee2e6!important;">
                            <i class="fas fa-receipt text-success mb-2" style="font-size:2rem;display:block;"></i>
                            <span class="fw-bold d-block mb-1">Venta Directa</span>
                            <small class="text-muted">Registrar como venta al cliente</small>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" @click="destinoCarrito='taller'"
                                class="btn btn-light border rounded-4 w-100 p-4 text-center h-100"
                                style="transition:.15s;border-color:#dee2e6!important;">
                            <i class="fas fa-tools text-primary mb-2" style="font-size:2rem;display:block;"></i>
                            <span class="fw-bold d-block mb-1">Enviar al Taller</span>
                            <small class="text-muted">Agregar a una orden de trabajo</small>
                        </button>
                    </div>
                </div>
            </div>

            {{-- PASO 2A: VENTA DIRECTA --}}
            <div x-show="destinoCarrito === 'venta'">
                <button type="button" @click="destinoCarrito=''" class="btn btn-link text-muted p-0 mb-3 text-decoration-none small">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                </button>
                <h6 class="fw-bold mb-3"><i class="fas fa-receipt text-success me-2"></i>Venta Directa</h6>

                {{-- Resultado venta --}}
                <div x-show="resultadoVenta !== null" class="mb-3">
                    <div x-show="resultadoVenta && resultadoVenta.success" class="alert alert-success rounded-3 border-0">
                        <i class="fas fa-check-circle me-2"></i>
                        <span x-text="resultadoVenta ? (resultadoVenta.message || 'Venta registrada correctamente.') : ''"></span>
                        <div class="mt-2 d-flex gap-2">
                            <a href="{{ url('admin/ventas/pos/ventas') }}" class="btn btn-success btn-sm rounded-pill">
                                <i class="fas fa-list me-1"></i> Ver ventas
                            </a>
                            <button type="button" @click="modalCarrito=false" class="btn btn-outline-secondary btn-sm rounded-pill">Cerrar</button>
                        </div>
                    </div>
                    <div x-show="resultadoVenta && !resultadoVenta.success" class="alert alert-danger rounded-3 border-0">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <span x-text="resultadoVenta ? (resultadoVenta.message || 'Error al procesar la venta.') : ''"></span>
                    </div>
                </div>

                <div x-show="!(resultadoVenta && resultadoVenta.success)">
                    {{-- Buscar cliente --}}
                    <div class="mb-3 position-relative">
                        <label class="form-label fw-semibold small">
                            Cliente <span class="text-muted fw-normal">(Ingrese DNI o RUC)</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-user text-muted"></i></span>
                            <input type="text"
                                   x-model="clienteBusqueda"
                                   @input.debounce.400ms="buscarClientesModal()"
                                   @keydown.escape="clientesResultados=[]"
                                   placeholder="Buscar por nombre o DNI..."
                                   class="form-control border-start-0"
                                   autocomplete="off">
                            <button type="button" x-show="clienteSeleccionado" @click="clienteSeleccionado=null;clienteBusqueda=''" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        {{-- Dropdown resultados --}}
                        <div x-show="clientesResultados.length > 0"
                             class="border rounded-3 shadow position-absolute w-100 bg-white"
                             style="z-index:1100;max-height:200px;overflow-y:auto;top:calc(100% + 2px);">
                            <template x-for="c in clientesResultados" :key="c.id">
                                <button type="button" @click="seleccionarCliente(c)"
                                        class="btn btn-light w-100 text-start rounded-0 py-2 px-3 border-bottom border-light">
                                    <div class="fw-semibold small" x-text="c.nombre"></div>
                                    <div class="text-muted" style="font-size:.75rem;" x-text="(c.tipo_documento || 'Doc') + ': ' + c.documento"></div>
                                </button>
                            </template>
                        </div>
                        <div x-show="buscandoClientes" class="mt-1 text-muted small">
                            <i class="fas fa-spinner fa-spin me-1"></i> Buscando...
                        </div>
                        <div x-show="clienteSeleccionado" class="mt-1 text-success small">
                            <i class="fas fa-check-circle me-1"></i>
                            <span x-text="'Seleccionado: ' + (clienteSeleccionado ? clienteSeleccionado.nombre : '')"></span>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <label class="form-label fw-semibold small">Moneda</label>
                            <select x-model="monedaVenta" class="form-select form-select-sm">
                                <option value="Soles">Soles (S/)</option>
                                <option value="Dólares">Dólares ($)</option>
                            </select>
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-semibold small">Forma de pago</label>
                            <select x-model="formaPago" class="form-select form-select-sm">
                                <option value="Contado">Contado</option>
                                <option value="Crédito">Crédito</option>
                            </select>
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-semibold small">Metodo de Pago</label>
                            <select x-model="metodoPago" class="form-select form-select-sm">
                                <option value="Efectivo">Efectivo</option>
                                <option value="Tarjeta de Crédito">Tarjeta de Crédito</option>
                                <option value="Transferencia">Transferencia</option>
                                <option value="Yape">Yape</option>
                                <option value="Plin">Plin</option>
                            </select>

                        </div>
                        <div class="col-4">
                            <label class="form-label fw-semibold small">Documento</label>
                            <select x-model="tipoDocumento" class="form-select form-select-sm">
                                <option value="Boleta">Boleta</option>
                                <option value="Factura">Factura</option>
                                <option value="Ticket">Ticket</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-light rounded-3 p-3 mb-3 d-flex justify-content-between align-items-center">
                        <div class="fw-semibold small text-muted" x-text="carrito.length + ' producto(s) en el carrito'"></div>
                        <div class="text-end">
                            <div class="text-muted small">Total a cobrar</div>
                            <div class="fw-bold text-success fs-5" x-text="total + ' ' + (monedaVenta === 'Soles' ? 'S/' : '$')"></div>
                        </div>
                    </div>

                    <button type="button" @click="confirmarVenta()" :disabled="procesandoVenta" class="btn btn-success w-100 rounded-pill fw-bold py-2">
                        <span x-show="!procesandoVenta"><i class="fas fa-check-circle me-2"></i>Confirmar Venta</span>
                        <span x-show="procesandoVenta"><i class="fas fa-spinner fa-spin me-2"></i>Procesando...</span>
                    </button>
                </div>
            </div>

            {{-- PASO 2B: ENVIAR AL TALLER --}}
            <div x-show="destinoCarrito === 'taller'">
                <button type="button" @click="destinoCarrito=''" class="btn btn-link text-muted p-0 mb-3 text-decoration-none small">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                </button>
                <h6 class="fw-bold mb-3"><i class="fas fa-tools text-primary me-2"></i>Enviar al Taller</h6>

                {{-- Resultado taller --}}
                <div x-show="resultadoTaller !== null" class="mb-3">
                    <div x-show="resultadoTaller && resultadoTaller.success" class="alert alert-success rounded-3 border-0">
                        <i class="fas fa-check-circle me-2"></i>
                        <span x-text="resultadoTaller ? resultadoTaller.message : ''"></span>
                        <div class="mt-2 d-flex gap-2">
                            <a :href="resultadoTaller ? resultadoTaller.redirect : '#'" class="btn btn-primary btn-sm rounded-pill">
                                <i class="fas fa-eye me-1"></i> Ver orden de trabajo
                            </a>
                            <button type="button" @click="modalCarrito=false" class="btn btn-outline-secondary btn-sm rounded-pill">Cerrar</button>
                        </div>
                    </div>
                    <div x-show="resultadoTaller && !resultadoTaller.success" class="alert alert-danger rounded-3 border-0">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <span x-text="resultadoTaller ? (resultadoTaller.message || 'Error al procesar.') : ''"></span>
                    </div>
                </div>

                <div x-show="!(resultadoTaller && resultadoTaller.success)">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Buscar Orden de Trabajo activa</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                            <input type="text"
                                   x-model="ordenBusqueda"
                                   @input.debounce.400ms="buscarOrdenesActivas()"
                                   placeholder="Código, nombre de cliente o placa..."
                                   class="form-control border-start-0"
                                   autocomplete="off">
                        </div>
                        <div x-show="buscandoOrdenes" class="mt-1 text-muted small">
                            <i class="fas fa-spinner fa-spin me-1"></i> Buscando...
                        </div>
                    </div>

                    <div x-show="ordenesResultados.length > 0" class="mb-3">
                        <template x-for="o in ordenesResultados" :key="o.id">
                            <button type="button" @click="ordenSeleccionada = o; ordenesResultados = []"
                                    class="btn w-100 text-start mb-1 rounded-3 px-3 py-2 border"
                                    :class="ordenSeleccionada && ordenSeleccionada.id === o.id ? 'btn-primary border-primary' : 'btn-light'">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="fw-bold small" x-text="o.codigo_orden"></span>
                                        <span class="ms-2 text-muted small" x-text="o.cliente_nombre"></span>
                                    </div>
                                    <span class="badge rounded-pill ms-2 flex-shrink-0"
                                          :class="{
                                              'bg-warning text-dark': o.estado === 'diagnostico' || o.estado === 'espera_aprobacion',
                                              'bg-info text-dark': o.estado === 'en_progreso',
                                              'bg-success': o.estado === 'finalizado'
                                          }"
                                          x-text="o.estado.replace(/_/g,' ')"></span>
                                </div>
                                <div class="text-muted mt-1" style="font-size:.75rem;">
                                    <i class="fas fa-car me-1"></i>
                                    <span x-text="o.placa"></span>
                                    <span x-show="o.fecha_ingreso"> · <span x-text="o.fecha_ingreso"></span></span>
                                </div>
                            </button>
                        </template>
                    </div>

                    <div x-show="ordenesResultados.length === 0 && ordenBusqueda.length >= 1 && !buscandoOrdenes"
                         class="text-muted small mb-3">
                        <i class="fas fa-info-circle me-1"></i> No se encontraron órdenes activas con ese criterio.
                    </div>

                    <div x-show="ordenSeleccionada !== null" class="alert alert-primary rounded-3 border-0 py-2 mb-3">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong x-text="ordenSeleccionada ? ordenSeleccionada.codigo_orden : ''"></strong>
                        <span class="ms-1 text-muted small"
                              x-text="ordenSeleccionada ? ('— ' + ordenSeleccionada.cliente_nombre + ' · ' + ordenSeleccionada.placa) : ''"></span>
                        <button type="button" @click="ordenSeleccionada=null" class="btn btn-link text-muted p-0 ms-2" style="font-size:.75rem;">cambiar</button>
                    </div>

                    <div class="bg-light rounded-3 p-3 mb-3">
                        <div class="text-muted small" x-text="carrito.length + ' repuesto(s) se agregarán a la orden seleccionada'"></div>
                    </div>

                    <button type="button" @click="confirmarTaller()" :disabled="!ordenSeleccionada || procesandoTaller" class="btn btn-primary w-100 rounded-pill fw-bold py-2">
                        <span x-show="!procesandoTaller"><i class="fas fa-tools me-2"></i>Agregar al Taller</span>
                        <span x-show="procesandoTaller"><i class="fas fa-spinner fa-spin me-2"></i>Procesando...</span>
                    </button>
                </div>
            </div>

        </div>{{-- /card-body --}}
    </div>{{-- /card --}}
</div>{{-- /overlay --}}
</template>{{-- /modal --}}

</div>{{-- end x-data --}}

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
@endpush

@endsection