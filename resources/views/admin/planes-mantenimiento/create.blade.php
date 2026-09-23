@extends('admin.layouts.app')

@section('title', 'Crear Plan de Mantenimiento')

@section('content')
<div class="container-fluid">
    <form id="planMantenimientoForm" action="{{ route('admin.planes-mantenimiento.store') }}" method="POST">
        @csrf
        
        <!-- Mostrar todos los errores de validación -->
        @if($errors->any())
            <div class="alert alert-danger">
                <h5><i class="fas fa-exclamation-triangle mr-2"></i>Errores de validación:</h5>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <!-- Mostrar mensajes de success/error -->
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-times-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif
        
        <!-- Header -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2>
                        <i class="fas fa-wrench mr-2"></i>
                        Crear Plan de Mantenimiento
                    </h2>
                    <div>
                        <a href="{{ route('admin.planes-mantenimiento.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>
                            Volver
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>
                            Guardar Plan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección 1: Información General -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">1. Información General del Plan</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre" class="required">Nombre del Plan</label>
                                    <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                                           value="{{ old('nombre') }}" required>
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="modelo_vehiculo" class="required">Modelo de Vehículo</label>
                                    <select name="modelo_vehiculo" id="modelo_vehiculo" 
                                            class="form-control @error('modelo_vehiculo') is-invalid @enderror" required>
                                        <option value="">Seleccionar modelo</option>
                                        @foreach($modelos as $modelo)
                                            <option value="{{ $modelo->nombre }}" 
                                                    {{ old('modelo_vehiculo') == $modelo->nombre ? 'selected' : '' }}>
                                                {{ $modelo->nombre }}
                                                @if($modelo->marca)
                                                    - {{ $modelo->marca->nombre }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('modelo_vehiculo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ano_modelo" class="required">Año del Modelo</label>
                                    <input type="number" name="ano_modelo" id="ano_modelo" 
                                           class="form-control @error('ano_modelo') is-invalid @enderror" 
                                           min="1990" max="{{ date('Y') + 2 }}" 
                                           value="{{ old('ano_modelo', date('Y')) }}" required>
                                    @error('ano_modelo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="required">Tipo de Transmisión</label>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <input type="radio" name="tipo_transmision" value="MT" id="trans_mt" 
                                                   class="form-check-input @error('tipo_transmision') is-invalid @enderror"
                                                   {{ old('tipo_transmision', 'MT') == 'MT' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="trans_mt">MT</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type="radio" name="tipo_transmision" value="AT" id="trans_at" 
                                                   class="form-check-input"
                                                   {{ old('tipo_transmision') == 'AT' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="trans_at">AT</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type="radio" name="tipo_transmision" value="CVT" id="trans_cvt" 
                                                   class="form-check-input"
                                                   {{ old('tipo_transmision') == 'CVT' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="trans_cvt">CVT</label>
                                        </div>
                                    </div>
                                    @error('tipo_transmision')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tono_vehiculo">Tono del Vehículo</label>
                                    <input type="text" name="tono_vehiculo" id="tono_vehiculo" 
                                           class="form-control @error('tono_vehiculo') is-invalid @enderror" 
                                           value="{{ old('tono_vehiculo') }}" placeholder="Ej: Comercial, Pasajeros">
                                    @error('tono_vehiculo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="descripcion">Descripción</label>
                                    <textarea name="descripcion" id="descripcion" rows="3" 
                                              class="form-control @error('descripcion') is-invalid @enderror"
                                              placeholder="Descripción detallada del plan de mantenimiento">{{ old('descripcion') }}</textarea>
                                    @error('descripcion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección 2: Configuración de Intervalos -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">2. Configuración de Intervalos</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="intervalo_base" class="required">Intervalo Base (km)</label>
                            <input type="number" name="intervalo_base" id="intervalo_base" 
                                   class="form-control @error('intervalo_base') is-invalid @enderror" 
                                   min="1000" step="1000" value="{{ old('intervalo_base', 5000) }}" required>
                            @error('intervalo_base')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="kilometraje_maximo" class="required">Máximo Kilometraje (km)</label>
                            <input type="number" name="kilometraje_maximo" id="kilometraje_maximo" 
                                   class="form-control @error('kilometraje_maximo') is-invalid @enderror" 
                                   min="5000" step="5000" value="{{ old('kilometraje_maximo', 100000) }}" required>
                            @error('kilometraje_maximo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="relacion_horas_km" class="required">Relación Horas-Km</label>
                            <input type="number" name="relacion_horas_km" id="relacion_horas_km" 
                                   class="form-control @error('relacion_horas_km') is-invalid @enderror" 
                                   min="50" value="{{ old('relacion_horas_km', 250) }}" required>
                            <small class="form-text text-muted">Horas por cada 5000 km</small>
                            @error('relacion_horas_km')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección 3: Componentes de Mantenimiento -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">3. Componentes de Mantenimiento</h4>
                        <button type="button" class="btn btn-primary btn-sm" onclick="agregarComponenteAhora()">
                            <i class="fas fa-plus mr-1"></i>
                            Agregar Componente
                        </button>
                        
                        <!-- SCRIPT DIRECTO AQUÍ PARA GARANTIZAR QUE SE CARGUE -->
                        <script>
                        // Variables globales simples
                        var componenteIndex = 0;
                        var partes = @json($partes);
                        var proveedores = @json($proveedores);

                        // Función ULTRA SIMPLE para agregar componente
                        function agregarComponenteAhora() {
                            
                            var opcionesPartes = '<option value="">-- Seleccionar Parte --</option>';
                            if (partes && partes.length > 0) {
                                for (var i = 0; i < partes.length; i++) {
                                    opcionesPartes += '<option value="' + partes[i].id + '">' + partes[i].nombre + '</option>';
                                }
                            }
                            
                            var opcionesProveedores = '<option value="">-- Seleccionar Proveedor (Opcional) --</option>';
                            if (proveedores && proveedores.length > 0) {
                                for (var i = 0; i < proveedores.length; i++) {
                                    opcionesProveedores += '<option value="' + proveedores[i].id + '">' + proveedores[i].nombre_completo + '</option>';
                                }
                            }
                            
                            var componente = document.createElement('div');
                            componente.className = 'border p-3 mb-3 bg-light';
                            componente.id = 'componente_' + componenteIndex;
                            
                            componente.innerHTML = 
                                '<div class="d-flex justify-content-between mb-2">' +
                                    '<h5>🔧 Componente ' + (componenteIndex + 1) + '</h5>' +
                                    '<button type="button" class="btn btn-danger btn-sm" onclick="eliminarComponenteAhora(' + componenteIndex + ')">❌ Eliminar</button>' +
                                '</div>' +
                                '<div class="row">' +
                                    '<div class="col-md-6">' +
                                        '<label><strong>Parte:</strong></label>' +
                                        '<select name="componentes[' + componenteIndex + '][parte_id]" class="form-control" required>' +
                                            opcionesPartes +
                                        '</select>' +
                                    '</div>' +
                                    '<div class="col-md-3">' +
                                        '<label><strong>Cantidad:</strong></label>' +
                                        '<input type="number" name="componentes[' + componenteIndex + '][cantidad]" class="form-control" value="1" required>' +
                                    '</div>' +
                                    '<div class="col-md-3">' +
                                        '<label><strong>Unidad:</strong></label>' +
                                        '<select name="componentes[' + componenteIndex + '][unidad_medida]" class="form-control" required>' +
                                            '<option value="Unidades">Unidades</option>' +
                                            '<option value="Litros">Litros</option>' +
                                            '<option value="Kg">Kg</option>' +
                                        '</select>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="row mt-2">' +
                                    '<div class="col-md-4">' +
                                        '<label><strong>Acción:</strong></label>' +
                                        '<select name="componentes[' + componenteIndex + '][accion]" class="form-control" required>' +
                                            '<option value="Reemplazar">Reemplazar</option>' +
                                            '<option value="Inspeccionar">Inspeccionar</option>' +
                                            '<option value="Lubricar">Lubricar</option>' +
                                        '</select>' +
                                    '</div>' +
                                    '<div class="col-md-4">' +
                                        '<label><strong>Proveedor:</strong></label>' +
                                        '<select name="componentes[' + componenteIndex + '][proveedor_id]" class="form-control">' +
                                            opcionesProveedores +
                                        '</select>' +
                                    '</div>' +
                                    '<div class="col-md-4">' +
                                        '<label><strong>Moneda:</strong></label>' +
                                        '<select name="componentes[' + componenteIndex + '][moneda]" class="form-control" required>' +
                                            '<option value="PEN">PEN</option>' +
                                            '<option value="USD">USD</option>' +
                                        '</select>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="row mt-2">' +
                                    '<div class="col-md-6">' +
                                        '<label><strong>Precio Base:</strong></label>' +
                                        '<input type="number" name="componentes[' + componenteIndex + '][precio_base]" class="form-control" step="0.01" min="0">' +
                                    '</div>' +
                                    '<div class="col-md-6">' +
                                        '<label><strong>Observaciones:</strong></label>' +
                                        '<input type="text" name="componentes[' + componenteIndex + '][observaciones]" class="form-control" placeholder="Observaciones opcionales">' +
                                    '</div>' +
                                '</div>';
                            
                            document.getElementById('componentesContainer').appendChild(componente);
                            componenteIndex++;
                            
                            // Regenerar tabla de intervalos
                            generarTablaIntervalos();
                        }

                        // Función para eliminar
                        function eliminarComponenteAhora(index) {
                            var elemento = document.getElementById('componente_' + index);
                            if (elemento) {
                                elemento.remove();
                                reindexarComponentes();
                                generarTablaIntervalos();
                            }
                        }
                        
                        // Función para reindexar componentes después de eliminar
                        function reindexarComponentes() {
                            var componentes = document.querySelectorAll('#componentesContainer > div');
                            componenteIndex = 0;
                            
                            for (var i = 0; i < componentes.length; i++) {
                                var componente = componentes[i];
                                
                                // Actualizar ID del div
                                componente.id = 'componente_' + i;
                                
                                // Actualizar título
                                var titulo = componente.querySelector('h5');
                                if (titulo) {
                                    titulo.textContent = '🔧 Componente ' + (i + 1);
                                }
                                
                                // Actualizar botón eliminar
                                var botonEliminar = componente.querySelector('button[onclick*="eliminarComponenteAhora"]');
                                if (botonEliminar) {
                                    botonEliminar.setAttribute('onclick', 'eliminarComponenteAhora(' + i + ')');
                                }
                                
                                // Actualizar names de inputs
                                var inputs = componente.querySelectorAll('input, select');
                                for (var j = 0; j < inputs.length; j++) {
                                    var input = inputs[j];
                                    var name = input.name;
                                    if (name && name.includes('componentes[')) {
                                        input.name = name.replace(/componentes\[\d+\]/, 'componentes[' + i + ']');
                                    }
                                }
                                
                                componenteIndex = i + 1;
                            }
                        }
                        
                        // Función para generar la tabla de intervalos
                        function generarTablaIntervalos() {
                            var intervalBase = parseInt(document.getElementById('intervalo_base').value) || 5000;
                            var kmMaximo = parseInt(document.getElementById('kilometraje_maximo').value) || 100000;
                            var componentesActuales = document.querySelectorAll('#componentesContainer > div');
                            var intervalosOld = @json(old('intervalos', []));
                            
                            if (componentesActuales.length === 0) {
                                document.getElementById('intervalosContainer').innerHTML = '<p class="text-muted">Agregue componentes para ver la programación de intervalos</p>';
                                return;
                            }
                            
                            // GUARDAR SELECCIONES ACTUALES ANTES DE REGENERAR
                            var seleccionesActuales = {};
                            var checkboxesExistentes = document.querySelectorAll('#intervalosContainer input[type="checkbox"]');
                            for (var i = 0; i < checkboxesExistentes.length; i++) {
                                var checkbox = checkboxesExistentes[i];
                                if (checkbox.name && checkbox.checked) {
                                    seleccionesActuales[checkbox.name] = true;
                                }
                            }
                            
                            // Generar intervalos (cada intervalo_base km hasta km_maximo)
                            var intervalos = [];
                            for (var km = intervalBase; km <= kmMaximo; km += intervalBase) {
                                intervalos.push(km);
                            }
                            
                            // Crear tabla
                            var tabla = '<table class="table table-bordered table-sm">';
                            tabla += '<thead class="thead-light">';
                            tabla += '<tr><th>Componente</th>';
                            
                            // Headers de intervalos
                            for (var i = 0; i < intervalos.length; i++) {
                                tabla += '<th class="text-center">' + intervalos[i].toLocaleString() + ' km</th>';
                            }
                            tabla += '</tr></thead><tbody>';
                            
                            // Filas por cada componente
                            for (var c = 0; c < componentesActuales.length; c++) {
                                var componenteDiv = componentesActuales[c];
                                var parteSelect = componenteDiv.querySelector('select[name*="[parte_id]"]');
                                var parteTexto = parteSelect.options[parteSelect.selectedIndex]?.text || 'Componente ' + (c + 1);
                                
                                tabla += '<tr><td><strong>' + parteTexto + '</strong></td>';
                                
                                // Checkboxes para cada intervalo
                                for (var i = 0; i < intervalos.length; i++) {
                                    var km = intervalos[i];
                                    var checked = '';
                                    var checkboxName = 'intervalos[' + c + '][' + km + '][aplica]';
                                    
                                    // PRIORIDAD 1: Verificar si hay datos old() para este checkbox
                                    if (intervalosOld[c] && intervalosOld[c][km] && intervalosOld[c][km].aplica == '1') {
                                        checked = ' checked';
                                    }
                                    // PRIORIDAD 2: Verificar selecciones actuales del usuario
                                    else if (seleccionesActuales[checkboxName]) {
                                        checked = ' checked';
                                    }
                                    // NO PRE-MARCAR NADA - Dejar que el usuario decida
                                    
                                    tabla += '<td class="text-center">';
                                    tabla += '<input type="hidden" name="intervalos[' + c + '][' + km + '][aplica]" value="0">';
                                    tabla += '<input type="checkbox" name="intervalos[' + c + '][' + km + '][aplica]" value="1" class="form-check-input"' + checked + '>';
                                    tabla += '</td>';
                                }
                                tabla += '</tr>';
                            }
                            
                            tabla += '</tbody></table>';
                            document.getElementById('intervalosContainer').innerHTML = tabla;
                        }
                        
                        // Regenerar tabla cuando cambie intervalo base o km máximo
                        document.addEventListener('DOMContentLoaded', function() {
                            var intervaloBase = document.getElementById('intervalo_base');
                            var kmMaximo = document.getElementById('kilometraje_maximo');
                            
                            if (intervaloBase) {
                                intervaloBase.addEventListener('change', generarTablaIntervalos);
                            }
                            if (kmMaximo) {
                                kmMaximo.addEventListener('change', generarTablaIntervalos);
                            }
                            
                            // Restaurar componentes si hay datos old() (errores de validación)
                            var componentesOld = @json(old('componentes', []));
                            var intervalosOld = @json(old('intervalos', []));
                            
                            console.log('Datos old() encontrados:');
                            console.log('Componentes:', componentesOld);
                            console.log('Intervalos:', intervalosOld);
                            console.log('Errores:', @json($errors->any()));
                            
                            if (componentesOld && componentesOld.length > 0) {
                                for (var i = 0; i < componentesOld.length; i++) {
                                    var comp = componentesOld[i];
                                    componenteIndex = i;
                                    
                                    var opcionesPartes = '<option value="">-- Seleccionar Parte --</option>';
                                    if (partes && partes.length > 0) {
                                        for (var j = 0; j < partes.length; j++) {
                                            var selected = partes[j].id == comp.parte_id ? 'selected' : '';
                                            opcionesPartes += '<option value="' + partes[j].id + '" ' + selected + '>' + partes[j].nombre + '</option>';
                                        }
                                    }
                                    
                                    var opcionesProveedores = '<option value="">-- Seleccionar Proveedor (Opcional) --</option>';
                                    if (proveedores && proveedores.length > 0) {
                                        for (var j = 0; j < proveedores.length; j++) {
                                            var selected = proveedores[j].id == comp.proveedor_id ? 'selected' : '';
                                            opcionesProveedores += '<option value="' + proveedores[j].id + '" ' + selected + '>' + proveedores[j].nombre_completo + '</option>';
                                        }
                                    }
                                    
                                    var componente = document.createElement('div');
                                    componente.className = 'border p-3 mb-3 bg-light';
                                    componente.id = 'componente_' + i;
                                    
                                    componente.innerHTML = 
                                        '<div class="d-flex justify-content-between mb-2">' +
                                            '<h5>🔧 Componente ' + (i + 1) + '</h5>' +
                                            '<button type="button" class="btn btn-danger btn-sm" onclick="eliminarComponenteAhora(' + i + ')">❌ Eliminar</button>' +
                                        '</div>' +
                                        '<div class="row">' +
                                            '<div class="col-md-6">' +
                                                '<label><strong>Parte:</strong></label>' +
                                                '<select name="componentes[' + i + '][parte_id]" class="form-control" required>' +
                                                    opcionesPartes +
                                                '</select>' +
                                            '</div>' +
                                            '<div class="col-md-3">' +
                                                '<label><strong>Cantidad:</strong></label>' +
                                                '<input type="number" name="componentes[' + i + '][cantidad]" class="form-control" value="' + (comp.cantidad || 1) + '" required>' +
                                            '</div>' +
                                            '<div class="col-md-3">' +
                                                '<label><strong>Unidad:</strong></label>' +
                                                '<select name="componentes[' + i + '][unidad_medida]" class="form-control" required>' +
                                                    '<option value="Unidades"' + (comp.unidad_medida == 'Unidades' ? ' selected' : '') + '>Unidades</option>' +
                                                    '<option value="Litros"' + (comp.unidad_medida == 'Litros' ? ' selected' : '') + '>Litros</option>' +
                                                    '<option value="Kg"' + (comp.unidad_medida == 'Kg' ? ' selected' : '') + '>Kg</option>' +
                                                '</select>' +
                                            '</div>' +
                                        '</div>' +
                                        '<div class="row mt-2">' +
                                            '<div class="col-md-4">' +
                                                '<label><strong>Acción:</strong></label>' +
                                                '<select name="componentes[' + i + '][accion]" class="form-control" required>' +
                                                    '<option value="Reemplazar"' + (comp.accion == 'Reemplazar' ? ' selected' : '') + '>Reemplazar</option>' +
                                                    '<option value="Inspeccionar"' + (comp.accion == 'Inspeccionar' ? ' selected' : '') + '>Inspeccionar</option>' +
                                                    '<option value="Lubricar"' + (comp.accion == 'Lubricar' ? ' selected' : '') + '>Lubricar</option>' +
                                                '</select>' +
                                            '</div>' +
                                            '<div class="col-md-4">' +
                                                '<label><strong>Proveedor:</strong></label>' +
                                                '<select name="componentes[' + i + '][proveedor_id]" class="form-control">' +
                                                    opcionesProveedores +
                                                '</select>' +
                                            '</div>' +
                                            '<div class="col-md-4">' +
                                                '<label><strong>Moneda:</strong></label>' +
                                                '<select name="componentes[' + i + '][moneda]" class="form-control" required>' +
                                                    '<option value="PEN"' + (comp.moneda == 'PEN' ? ' selected' : '') + '>PEN</option>' +
                                                    '<option value="USD"' + (comp.moneda == 'USD' ? ' selected' : '') + '>USD</option>' +
                                                '</select>' +
                                            '</div>' +
                                        '</div>' +
                                        '<div class="row mt-2">' +
                                            '<div class="col-md-6">' +
                                                '<label><strong>Precio Base:</strong></label>' +
                                                '<input type="number" name="componentes[' + i + '][precio_base]" class="form-control" step="0.01" min="0" value="' + (comp.precio_base || '') + '">' +
                                            '</div>' +
                                            '<div class="col-md-6">' +
                                                '<label><strong>Observaciones:</strong></label>' +
                                                '<input type="text" name="componentes[' + i + '][observaciones]" class="form-control" placeholder="Observaciones opcionales" value="' + (comp.observaciones || '') + '">' +
                                            '</div>' +
                                        '</div>';
                                    
                                    document.getElementById('componentesContainer').appendChild(componente);
                                }
                                componenteIndex = componentesOld.length;
                                generarTablaIntervalos();
                            }
                        });
                        </script>
                    </div>
                    <div class="card-body">
                        <div id="componentesContainer">
                            <!-- Los componentes se agregarán dinámicamente aquí -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección 4: Programación por Intervalos -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">4. Programación por Intervalos</h4>
                        <small class="text-muted">
                            <i class="fas fa-exclamation-triangle text-warning mr-1"></i>
                            <strong>IMPORTANTE:</strong> Marque los checkboxes para indicar qué componentes aplican en cada intervalo de kilometraje
                        </small>
                    </div>
                    <div class="card-body">
                        <div id="intervalosContainer" class="table-responsive">
                            <!-- La tabla de intervalos se generará dinámicamente -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección 5: Parámetros de Costeo -->
        <div class="row mt-3">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">5. Parámetros de Costeo</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tarifa_mano_obra">Tarifa de Mano de Obra</label>
                                    <div class="input-group">
                                        <input type="number" name="tarifa_mano_obra" id="tarifa_mano_obra" 
                                               class="form-control @error('tarifa_mano_obra') is-invalid @enderror" 
                                               min="0" step="0.01" value="{{ old('tarifa_mano_obra', 0) }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text">por hora</span>
                                        </div>
                                    </div>
                                    @error('tarifa_mano_obra')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="impuestos" class="required">Impuestos (%)</label>
                                    <input type="number" name="impuestos" id="impuestos" 
                                           class="form-control @error('impuestos') is-invalid @enderror" 
                                           min="0" max="100" step="0.01" value="{{ old('impuestos', 18) }}" required>
                                    @error('impuestos')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="margen_beneficio">Margen de Beneficio (%)</label>
                                    <input type="number" name="margen_beneficio" id="margen_beneficio" 
                                           class="form-control @error('margen_beneficio') is-invalid @enderror" 
                                           min="0" max="100" step="0.01" value="{{ old('margen_beneficio', 0) }}">
                                    @error('margen_beneficio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección 6: Configuraciones Adicionales -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">6. Configuraciones Adicionales</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="required">Moneda Principal</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input type="radio" name="moneda_principal" value="PEN" id="moneda_pen" 
                                                   class="form-check-input @error('moneda_principal') is-invalid @enderror"
                                                   {{ old('moneda_principal', 'PEN') == 'PEN' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="moneda_pen">Soles (PEN)</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type="radio" name="moneda_principal" value="USD" id="moneda_usd" 
                                                   class="form-check-input"
                                                   {{ old('moneda_principal') == 'USD' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="moneda_usd">Dólares (USD)</label>
                                        </div>
                                    </div>
                                    @error('moneda_principal')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="proveedor_predeterminado_id">Proveedor Predeterminado</label>
                                    <select name="proveedor_predeterminado_id" id="proveedor_predeterminado_id" 
                                            class="form-control @error('proveedor_predeterminado_id') is-invalid @enderror">
                                        <option value="">Seleccionar proveedor</option>
                                        @foreach($proveedores as $proveedor)
                                            <option value="{{ $proveedor->id }}" 
                                                    {{ old('proveedor_predeterminado_id') == $proveedor->id ? 'selected' : '' }}>
                                                {{ $proveedor->nombre_completo }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('proveedor_predeterminado_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" name="mostrar_precios" id="mostrar_precios" 
                                               class="form-check-input" value="1" 
                                               {{ old('mostrar_precios', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="mostrar_precios">
                                            Mostrar precios en el plan
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" name="activo" id="activo" 
                                               class="form-check-input" value="1" 
                                               {{ old('activo', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="activo">
                                            Plan activo
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="text-center">
                    <a href="{{ route('admin.planes-mantenimiento.index') }}" class="btn btn-secondary btn-lg mr-3">
                        <i class="fas fa-times mr-1"></i>
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg" onclick="validarAnteEnvio(event)">
                        <i class="fas fa-save mr-1"></i>
                        Guardar Plan de Mantenimiento
                    </button>
                    
                    <script>
                    function validarAnteEnvio(event) {
                        var componentes = document.querySelectorAll('#componentesContainer > div');
                        if (componentes.length === 0) {
                            event.preventDefault();
                            alert('Debe agregar al menos un componente antes de guardar el plan.');
                            return false;
                        }
                        
                        // Reindexar componentes antes de enviar para asegurar índices consecutivos
                        reindexarComponentes();
                        generarTablaIntervalos();
                        
                        console.log('Enviando formulario con', componentes.length, 'componentes');
                        return true;
                    }
                    
                    // Refrescar token CSRF cada 10 minutos para evitar error 419
                    setInterval(function() {
                        fetch('/admin/refresh-csrf-token')
                        .then(response => response.json())
                        .then(data => {
                            if (data.token) {
                                document.querySelector('input[name="_token"]').value = data.token;
                                console.log('CSRF token actualizado');
                            }
                        })
                        .catch(error => {
                            console.warn('No se pudo actualizar CSRF token:', error);
                        });
                    }, 600000); // 10 minutos
                    </script>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
// Este script ya no es necesario porque está inline arriba
console.log('Script de backup cargado');
</script>
@endsection

@section('styles')
<style>
    .required::after {
        content: ' *';
        color: red;
    }
    
    .componente-item {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        margin-bottom: 15px;
        padding: 15px;
        background-color: #f8f9fa;
    }
    
    .componente-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 15px;
    }
    
    #intervalosContainer table th,
    #intervalosContainer table td {
        white-space: nowrap;
        min-width: 120px;
    }
    
    #intervalosContainer .form-control-sm {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
</style>
@endsection