@extends('admin.layouts.app')

@section('title', 'Editar Plan de Mantenimiento')

@section('content')
<div class="container-fluid">
    <form id="planMantenimientoForm" action="{{ route('admin.planes-mantenimiento.update', $planMantenimiento) }}" method="POST">
        @csrf
        @method('PUT')
        
        <!-- Header -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2>
                        <i class="fas fa-wrench mr-2"></i>
                        Editar Plan de Mantenimiento
                    </h2>
                    <div>
                        <a href="{{ route('admin.planes-mantenimiento.show', $planMantenimiento) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>
                            Volver
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>
                            Actualizar Plan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información General -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Información General del Plan</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre" class="required">Nombre del Plan</label>
                                    <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                                           value="{{ old('nombre', $planMantenimiento->nombre) }}" required>
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
                                                    {{ old('modelo_vehiculo', $planMantenimiento->modelo_vehiculo) == $modelo->nombre ? 'selected' : '' }}>
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
                                           value="{{ old('ano_modelo', $planMantenimiento->ano_modelo) }}" required>
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
                                                   {{ old('tipo_transmision', $planMantenimiento->tipo_transmision) == 'MT' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="trans_mt">MT</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type="radio" name="tipo_transmision" value="AT" id="trans_at" 
                                                   class="form-check-input"
                                                   {{ old('tipo_transmision', $planMantenimiento->tipo_transmision) == 'AT' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="trans_at">AT</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type="radio" name="tipo_transmision" value="CVT" id="trans_cvt" 
                                                   class="form-check-input"
                                                   {{ old('tipo_transmision', $planMantenimiento->tipo_transmision) == 'CVT' ? 'checked' : '' }}>
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
                                           value="{{ old('tono_vehiculo', $planMantenimiento->tono_vehiculo) }}" placeholder="Ej: Comercial, Pasajeros">
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
                                              placeholder="Descripción detallada del plan de mantenimiento">{{ old('descripcion', $planMantenimiento->descripcion) }}</textarea>
                                    @error('descripcion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuración de Intervalos -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Configuración de Intervalos</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="intervalo_base" class="required">Intervalo Base (km)</label>
                            <input type="number" name="intervalo_base" id="intervalo_base" 
                                   class="form-control @error('intervalo_base') is-invalid @enderror" 
                                   min="1000" step="1000" value="{{ old('intervalo_base', $planMantenimiento->intervalo_base) }}" required>
                            @error('intervalo_base')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="kilometraje_maximo" class="required">Máximo Kilometraje (km)</label>
                            <input type="number" name="kilometraje_maximo" id="kilometraje_maximo" 
                                   class="form-control @error('kilometraje_maximo') is-invalid @enderror" 
                                   min="5000" step="5000" value="{{ old('kilometraje_maximo', $planMantenimiento->kilometraje_maximo) }}" required>
                            @error('kilometraje_maximo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="relacion_horas_km" class="required">Relación Horas-Km</label>
                            <input type="number" name="relacion_horas_km" id="relacion_horas_km" 
                                   class="form-control @error('relacion_horas_km') is-invalid @enderror" 
                                   min="50" value="{{ old('relacion_horas_km', $planMantenimiento->relacion_horas_km) }}" required>
                            <small class="form-text text-muted">Horas por cada 5000 km</small>
                            @error('relacion_horas_km')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Parámetros de Costeo y Configuraciones -->
        <div class="row mt-3">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Parámetros de Costeo</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tarifa_mano_obra">Tarifa de Mano de Obra</label>
                                    <div class="input-group">
                                        <input type="number" name="tarifa_mano_obra" id="tarifa_mano_obra" 
                                               class="form-control @error('tarifa_mano_obra') is-invalid @enderror" 
                                               min="0" step="0.01" value="{{ old('tarifa_mano_obra', $planMantenimiento->tarifa_mano_obra) }}">
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
                                           min="0" max="100" step="0.01" value="{{ old('impuestos', $planMantenimiento->impuestos) }}" required>
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
                                           min="0" max="100" step="0.01" value="{{ old('margen_beneficio', $planMantenimiento->margen_beneficio) }}">
                                    @error('margen_beneficio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuraciones Adicionales -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Configuraciones Adicionales</h4>
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
                                                   {{ old('moneda_principal', $planMantenimiento->moneda_principal) == 'PEN' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="moneda_pen">Soles (PEN)</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type="radio" name="moneda_principal" value="USD" id="moneda_usd" 
                                                   class="form-check-input"
                                                   {{ old('moneda_principal', $planMantenimiento->moneda_principal) == 'USD' ? 'checked' : '' }}>
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
                                                    {{ old('proveedor_predeterminado_id', $planMantenimiento->proveedor_predeterminado_id) == $proveedor->id ? 'selected' : '' }}>
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
                                               {{ old('mostrar_precios', $planMantenimiento->mostrar_precios) ? 'checked' : '' }}>
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
                                               {{ old('activo', $planMantenimiento->activo) ? 'checked' : '' }}>
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

        <!-- Información sobre componentes existentes -->
        @if($planMantenimiento->componentesPlan->count() > 0)
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Componentes Actuales del Plan</h4>
                        <small class="text-info">
                            <i class="fas fa-info-circle mr-1"></i>
                            Para modificar los componentes y sus intervalos, será necesario crear un nuevo plan o contactar al administrador.
                        </small>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Componente</th>
                                        <th>Cantidad</th>
                                        <th>Acción</th>
                                        <th>Proveedor</th>
                                        <th>Precio Base</th>
                                        <th>Intervalos que Aplican</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($planMantenimiento->componentesPlan as $componente)
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold">{{ $componente->parte->nombre }}</div>
                                            @if($componente->parte->codigo)
                                                <small class="text-muted">{{ $componente->parte->codigo }}</small>
                                            @endif
                                        </td>
                                        <td>{{ number_format($componente->cantidad, 2) }} {{ $componente->unidad_medida }}</td>
                                        <td>
                                            <span class="badge badge-secondary">
                                                {{ $componente->accion_texto }} - {{ $componente->accion }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($componente->proveedor)
                                                {{ $componente->proveedor->nombre_completo }}
                                            @else
                                                <em class="text-muted">Predeterminado</em>
                                            @endif
                                        </td>
                                        <td>{{ $componente->precio_formateado }}</td>
                                        <td>
                                            @php
                                                $intervalosAplican = $componente->intervalos->where('aplica', true);
                                            @endphp
                                            @if($intervalosAplican->count() > 0)
                                                @foreach($intervalosAplican as $intervalo)
                                                    <span class="badge badge-info mr-1">{{ number_format($intervalo->kilometraje) }}km</span>
                                                @endforeach
                                            @else
                                                <em class="text-muted">Ninguno</em>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Botones de acción -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="text-center">
                    <a href="{{ route('admin.planes-mantenimiento.show', $planMantenimiento) }}" class="btn btn-secondary btn-lg mr-3">
                        <i class="fas fa-times mr-1"></i>
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save mr-1"></i>
                        Actualizar Plan de Mantenimiento
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('styles')
<style>
    .required::after {
        content: ' *';
        color: red;
    }
</style>
@endsection