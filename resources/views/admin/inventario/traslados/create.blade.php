@extends('admin.layouts.app')

@section('title', 'Nuevo Traslado')

@section('header', 'Nuevo Traslado')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h4 fw-bold mb-0" :class="darkMode ? 'text-light' : 'text-dark'">
                        Registrar Nuevo Traslado
                    </h2>
                    <a href="{{ route('admin.inventario.traslados.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left me-2"></i> Volver
                    </a>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.inventario.traslados.store') }}">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="almacen_origen_id" class="form-label">Almacén Origen <span class="text-danger">*</span></label>
                                <select class="form-select @error('almacen_origen_id') is-invalid @enderror" id="almacen_origen_id" name="almacen_origen_id" required>
                                    <option value="">Seleccione un almacén</option>
                                    @foreach($almacenes as $almacen)
                                        <option value="{{ $almacen->id }}" {{ old('almacen_origen_id') == $almacen->id ? 'selected' : '' }}>
                                            {{ $almacen->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('almacen_origen_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="almacen_destino_id" class="form-label">Almacén Destino <span class="text-danger">*</span></label>
                                <select class="form-select @error('almacen_destino_id') is-invalid @enderror" id="almacen_destino_id" name="almacen_destino_id" required>
                                    <option value="">Seleccione un almacén</option>
                                    @foreach($almacenes as $almacen)
                                        <option value="{{ $almacen->id }}" {{ old('almacen_destino_id') == $almacen->id ? 'selected' : '' }}>
                                            {{ $almacen->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('almacen_destino_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="motivo" class="form-label">Motivo del Traslado <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('motivo') is-invalid @enderror" id="motivo" name="motivo" rows="3" required>{{ old('motivo') }}</textarea>
                        @error('motivo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Tipo de Item <span class="text-danger">*</span></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipo_item" id="tipo_parte" value="parte" {{ old('tipo_item', 'parte') == 'parte' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="tipo_parte">
                                        Parte/Repuesto
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipo_item" id="tipo_vehiculo" value="vehiculo" {{ old('tipo_item') == 'vehiculo' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="tipo_vehiculo">
                                        Vehículo
                                    </label>
                                </div>
                                @error('tipo_item')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Selector de Partes (visible cuando tipo_item = parte) -->
                    <div class="row mb-3" id="parte_selector">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="parte_id" class="form-label">Parte/Repuesto <span class="text-danger">*</span></label>
                                <select class="form-select @error('parte_id') is-invalid @enderror" id="parte_id" name="parte_id">
                                    <option value="">Seleccione una parte</option>
                                    @foreach($partes as $parte)
                                        <option value="{{ $parte->id }}" {{ old('parte_id') == $parte->id ? 'selected' : '' }}>
                                            {{ $parte->codigo }} - {{ $parte->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parte_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Selector de Vehículos (visible cuando tipo_item = vehiculo) -->
                    <div class="row mb-3" id="vehiculo_selector" style="display: none;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="vehiculo_id" class="form-label">Vehículo <span class="text-danger">*</span></label>
                                <select class="form-select @error('vehiculo_id') is-invalid @enderror" id="vehiculo_id" name="vehiculo_id">
                                    <option value="">Seleccione un vehículo</option>
                                    @foreach($vehiculos as $vehiculo)
                                        <option value="{{ $vehiculo->id }}" {{ old('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}>
                                            {{ $vehiculo->marca->nombre ?? '' }} {{ $vehiculo->modelo->nombre ?? '' }} {{ $vehiculo->version->nombre ?? '' }} {{ $vehiculo->anioModelo->anio ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehiculo_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="stock_actual" class="form-label">Stock Actual</label>
                                <input type="text" class="form-control" id="stock_actual" readonly>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="cantidad" class="form-label">Cantidad a Trasladar <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" class="form-control @error('cantidad') is-invalid @enderror" id="cantidad" name="cantidad" value="{{ old('cantidad') }}" required>
                                @error('cantidad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save me-2"></i> Guardar Traslado
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
    document.addEventListener('DOMContentLoaded', function() {
        // Variables para controlar los elementos del formulario
        const almacenOrigenSelect = document.getElementById('almacen_origen_id');
        const tipoParte = document.getElementById('tipo_parte');
        const tipoVehiculo = document.getElementById('tipo_vehiculo');
        const parteSelector = document.getElementById('parte_selector');
        const vehiculoSelector = document.getElementById('vehiculo_selector');
        const parteSelect = document.getElementById('parte_id');
        const vehiculoSelect = document.getElementById('vehiculo_id');
        const stockActualInput = document.getElementById('stock_actual');
        const cantidadInput = document.getElementById('cantidad');
        
        // Función para mostrar/ocultar selectores según el tipo seleccionado
        function toggleItemSelectors() {
            if (tipoParte.checked) {
                parteSelector.style.display = 'flex';
                vehiculoSelector.style.display = 'none';
                vehiculoSelect.removeAttribute('required');
                parteSelect.setAttribute('required', 'required');
            } else if (tipoVehiculo.checked) {
                parteSelector.style.display = 'none';
                vehiculoSelector.style.display = 'flex';
                parteSelect.removeAttribute('required');
                vehiculoSelect.setAttribute('required', 'required');
            }
            getStockActual();
        }
        
        // Función para obtener el stock actual
        function getStockActual() {
            const almacenId = almacenOrigenSelect.value;
            let tipo = '';
            let itemId = '';
            
            if (tipoParte.checked) {
                tipo = 'parte';
                itemId = parteSelect.value;
            } else if (tipoVehiculo.checked) {
                tipo = 'vehiculo';
                itemId = vehiculoSelect.value;
            }
            
            if (almacenId && tipo && itemId) {
                fetch(`{{ url('admin/inventario/traslados/get-stock') }}?almacen_id=${almacenId}&tipo_item=${tipo}&item_id=${itemId}`)
                    .then(response => response.json())
                    .then(data => {
                        stockActualInput.value = data.stock;
                        validarCantidad();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        stockActualInput.value = '0';
                    });
            } else {
                stockActualInput.value = '';
            }
        }
        
        // Función para validar que la cantidad no exceda el stock actual
        function validarCantidad() {
            const stockActual = parseFloat(stockActualInput.value) || 0;
            const cantidad = parseFloat(cantidadInput.value) || 0;
            
            if (cantidad > stockActual) {
                cantidadInput.classList.add('is-invalid');
                
                // Eliminar feedback existente si hay alguno
                const existingFeedback = cantidadInput.nextElementSibling;
                if (existingFeedback && existingFeedback.classList.contains('invalid-feedback')) {
                    existingFeedback.remove();
                }
                
                const feedbackDiv = document.createElement('div');
                feedbackDiv.classList.add('invalid-feedback');
                feedbackDiv.textContent = 'La cantidad a trasladar no puede ser mayor al stock disponible.';
                cantidadInput.parentNode.appendChild(feedbackDiv);
            } else {
                cantidadInput.classList.remove('is-invalid');
                
                const existingFeedback = cantidadInput.nextElementSibling;
                if (existingFeedback && existingFeedback.classList.contains('invalid-feedback')) {
                    existingFeedback.remove();
                }
            }
        }
        
        // Event listeners
        tipoParte.addEventListener('change', toggleItemSelectors);
        tipoVehiculo.addEventListener('change', toggleItemSelectors);
        almacenOrigenSelect.addEventListener('change', getStockActual);
        parteSelect.addEventListener('change', getStockActual);
        vehiculoSelect.addEventListener('change', getStockActual);
        cantidadInput.addEventListener('input', validarCantidad);
        
        // Validar que los almacenes origen y destino sean diferentes
        const almacenDestinoSelect = document.getElementById('almacen_destino_id');
        
        function validarAlmacenesDiferentes() {
            const origenId = almacenOrigenSelect.value;
            const destinoId = almacenDestinoSelect.value;
            
            if (origenId && destinoId && origenId === destinoId) {
                almacenDestinoSelect.classList.add('is-invalid');
                
                // Eliminar feedback existente si hay alguno
                const existingFeedback = almacenDestinoSelect.nextElementSibling;
                if (existingFeedback && existingFeedback.classList.contains('invalid-feedback')) {
                    existingFeedback.remove();
                }
                
                const feedbackDiv = document.createElement('div');
                feedbackDiv.classList.add('invalid-feedback');
                feedbackDiv.textContent = 'El almacén de destino debe ser diferente al de origen.';
                almacenDestinoSelect.parentNode.appendChild(feedbackDiv);
            } else {
                almacenDestinoSelect.classList.remove('is-invalid');
                
                const existingFeedback = almacenDestinoSelect.nextElementSibling;
                if (existingFeedback && existingFeedback.classList.contains('invalid-feedback')) {
                    existingFeedback.remove();
                }
            }
        }
        
        almacenOrigenSelect.addEventListener('change', validarAlmacenesDiferentes);
        almacenDestinoSelect.addEventListener('change', validarAlmacenesDiferentes);
        
        // Inicializar los selectores
        toggleItemSelectors();
        
        // Inicializar los valores si ya están seleccionados
        if (almacenOrigenSelect.value) {
            if ((tipoParte.checked && parteSelect.value) || 
                (tipoVehiculo.checked && vehiculoSelect.value)) {
                getStockActual();
            }
            
            if (almacenDestinoSelect.value) {
                validarAlmacenesDiferentes();
            }
        }
    });
</script>
@endpush