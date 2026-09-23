@extends('admin.layouts.app')

@section('title', 'Crear Orden de Compra')

@section('content')
<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-plus text-info me-2"></i> Adquisición
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6 text-shadow-sm d-flex align-items-center">
                    Nueva Orden de Compra
                </h2>
                <p class="text-white-50 mb-0">Generando orden para Requerimiento #{{ $requerimiento->id }}</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.compras.requerimientos.show', $requerimiento) }}" class="btn bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0">
                    <i class="fas fa-arrow-left text-primary me-2"></i> Volver a Requerimiento
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-lg-4 position-relative" style="top: -3.5rem; z-index: 10;">
<div class="row">
    <div class="col-12">
        <div class="card dashboard-card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <form action="{{ route('admin.compras.ordenes.store') }}" method="POST" id="ordenForm">
                    @csrf
                    <input type="hidden" name="requerimiento_id" value="{{ $requerimiento->id }}">
                    
                    <!-- Información del Requerimiento -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3" :class="darkMode ? 'text-light' : 'text-dark'">Información del Requerimiento</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label small text-muted">ID Requerimiento</label>
                                <input type="text" class="form-control form-control-sm" value="{{ $requerimiento->id }}" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label small text-muted">Almacén de Destino</label>
                                <input type="text" class="form-control form-control-sm" value="{{ $requerimiento->almacen->nombre }}" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label small text-muted">Fecha del Requerimiento</label>
                                <input type="text" class="form-control form-control-sm" value="{{ $requerimiento->created_at->format('d/m/Y') }}" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Información de la Orden -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3" :class="darkMode ? 'text-light' : 'text-dark'">Información de la Orden</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="proveedor_id" class="form-label small text-muted required">Proveedor</label>
                                <select id="proveedor_id" name="proveedor_id" class="form-select form-select-sm @error('proveedor_id') is-invalid @enderror" required>
                                    <option value="">Seleccionar proveedor</option>
                                    @foreach ($proveedores as $proveedor)
                                        <option value="{{ $proveedor->id }}" {{ old('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                                            {{ $proveedor->razon_social }} ({{ $proveedor->documento }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('proveedor_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="moneda" class="form-label small text-muted required">Moneda</label>
                                <select id="moneda" name="moneda" class="form-select form-select-sm @error('moneda') is-invalid @enderror" required>
                                    <option value="S/" {{ old('moneda') == 'S/' ? 'selected' : '' }}>S/ (Soles)</option>
                                    <option value="US$" {{ old('moneda') == 'US$' ? 'selected' : '' }}>US$ (Dólares)</option>
                                    <option value="€" {{ old('moneda') == '€' ? 'selected' : '' }}>€ (Euros)</option>
                                </select>
                                @error('moneda')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="observaciones" class="form-label small text-muted">Observaciones</label>
                            <textarea id="observaciones" name="observaciones" class="form-control form-control-sm @error('observaciones') is-invalid @enderror" rows="3">{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Detalles de Productos -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3" :class="darkMode ? 'text-light' : 'text-dark'">Productos</h5>
                        <div class="table-responsive">
                            <table class="table table-hover" :class="darkMode ? 'table-dark' : ''">
                                <thead class="table-light" :class="darkMode ? 'table-dark' : ''">
                                    <tr>
                                        <th class="small text-uppercase">Nro.</th>
                                        <th class="small text-uppercase">Código</th>
                                        <th class="small text-uppercase">Producto</th>
                                        <th class="small text-uppercase">Tipo</th>
                                        <th class="small text-uppercase">Cant. Requerida</th>
                                        <th class="small text-uppercase">Cant. Compra</th>
                                        <th class="small text-uppercase">Unidad</th>
                                        <th class="small text-uppercase">Precio</th>
                                        <th class="small text-uppercase">Descuento</th>
                                        <th class="small text-uppercase">Total</th>
                                        <th class="small text-uppercase">IGV</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($requerimiento->detalles as $index => $detalle)
                                        @php
                                            $itemNombre = '';
                                            $codigo = '';
                                            
                                            if ($detalle->tipo_item === 'parte') {
                                                $item = \App\Models\Parte::find($detalle->item_id);
                                                $codigo = $item ? $item->codigo : '';
                                                $itemNombre = $item ? $item->nombre : 'Parte no encontrada';
                                            } elseif ($detalle->tipo_item === 'vehiculo') {
                                                $item = \App\Models\Vehiculo::with(['marca', 'modelo'])->find($detalle->item_id);
                                                $codigo = $item ? "V{$item->id}" : '';
                                                $itemNombre = $item ? implode(' ', array_filter([
                                                    $item->marca?->nombre ?? '',
                                                    $item->modelo?->nombre ?? '',
                                                    $item->version?->nombre ?? '',
                                                    $item->anioModelo?->anio ?? '',
                                                ])) : 'Vehículo no encontrado';
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $codigo }}</td>
                                            <td>{{ $itemNombre }}</td>
                                            <td>{{ ucfirst($detalle->tipo_item) }}</td>
                                            <td>
                                                <input type="hidden" name="detalles[{{ $index }}][item_id]" value="{{ $detalle->item_id }}">
                                                <input type="hidden" name="detalles[{{ $index }}][tipo_item]" value="{{ $detalle->tipo_item }}">
                                                <input type="hidden" name="detalles[{{ $index }}][cantidad_requerida]" value="{{ $detalle->cantidad }}">
                                                {{ $detalle->cantidad }}
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm cantidad-compra" 
                                                    name="detalles[{{ $index }}][cantidad_en_compra]" 
                                                    value="{{ old("detalles.{$index}.cantidad_en_compra", $detalle->cantidad) }}" 
                                                    min="0.01" step="0.01" required>
                                            </td>
                                            <td>
                                                <select name="detalles[{{ $index }}][unidad]" class="form-select form-select-sm">
                                                    <option value="UND" selected>UND</option>
                                                    <option value="KG">KG</option>
                                                    <option value="LT">LT</option>
                                                    <option value="MT">MT</option>
                                                    <option value="M2">M2</option>
                                                    <option value="M3">M3</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm precio-compra" 
                                                    name="detalles[{{ $index }}][precio_compra]" 
                                                    value="{{ old("detalles.{$index}.precio_compra", 0) }}" 
                                                    min="0" step="0.01" required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm descuento" 
                                                    name="detalles[{{ $index }}][descuento]" 
                                                    value="{{ old("detalles.{$index}.descuento", 0) }}" 
                                                    min="0" step="0.01">
                                            </td>
                                            <td>
                                                <span class="total-item">0.00</span>
                                            </td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" 
                                                        name="detalles[{{ $index }}][afecto_igv]" 
                                                        id="afectoIgv{{ $index }}" 
                                                        value="1" checked>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="9" class="text-end"><strong>Total:</strong></td>
                                        <td><span id="totalGeneral">0.00</span></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Botones de Acción -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.compras.requerimientos.show', $requerimiento) }}" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Guardar Orden de Compra</button>
                    </div>
                </form>
        </div>
    </div>
</div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Función para calcular totales
        function calcularTotales() {
            let totalGeneral = 0;
            document.querySelectorAll('tbody tr').forEach(function(row) {
                const cantidad = parseFloat(row.querySelector('.cantidad-compra').value) || 0;
                const precio = parseFloat(row.querySelector('.precio-compra').value) || 0;
                const descuento = parseFloat(row.querySelector('.descuento').value) || 0;
                
                const totalItem = (cantidad * precio) - descuento;
                row.querySelector('.total-item').textContent = totalItem.toFixed(2);
                
                totalGeneral += totalItem;
            });
            
            document.getElementById('totalGeneral').textContent = totalGeneral.toFixed(2);
        }
        
        // Eventos para calcular totales al cambiar valores
        document.querySelectorAll('.cantidad-compra, .precio-compra, .descuento').forEach(function(input) {
            input.addEventListener('input', calcularTotales);
        });
        
        // Calcular totales iniciales
        calcularTotales();
        
        // Validación del formulario
        document.getElementById('ordenForm').addEventListener('submit', function(e) {
            let valid = true;
            const cantidadInputs = document.querySelectorAll('.cantidad-compra');
            const precioInputs = document.querySelectorAll('.precio-compra');
            
            cantidadInputs.forEach(function(input) {
                if (parseFloat(input.value) <= 0) {
                    valid = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });
            
            precioInputs.forEach(function(input) {
                if (parseFloat(input.value) < 0) {
                    valid = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });
            
            if (!valid) {
                e.preventDefault();
                alert('Por favor, verifica que las cantidades y precios sean correctos.');
            }
        });
    });
</script>
@endpush
@endsection