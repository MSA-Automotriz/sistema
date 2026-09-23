<!-- resources/views/admin/mantenimiento/ordenes/edit.blade.php -->
@extends('admin.layouts.app')

@section('title', 'Editar Orden de Trabajo')

@push('styles')
    <!-- Datepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Select2 para mejorar los selectores -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Editar Orden de Trabajo #{{ $orden->id }} - {{ $orden->codigo_orden }}</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('admin.mantenimiento.ordenes.show', $orden) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.mantenimiento.ordenes.update', $orden) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row mb-4">
            <!-- Información de Estado y Progreso -->
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Estado de la Orden</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="estado" class="form-label">Estado Actual</label>
                                <select name="estado" id="estado" class="form-select" required>
                                    <option value="diagnostico" {{ $orden->estado === 'diagnostico' ? 'selected' : '' }}>Diagnóstico</option>
                                    <option value="espera_aprobacion" {{ $orden->estado === 'espera_aprobacion' ? 'selected' : '' }}>Esperando Aprobación</option>
                                    <option value="en_progreso" {{ $orden->estado === 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                                    <option value="finalizado" {{ $orden->estado === 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                                    <option value="facturado" {{ $orden->estado === 'facturado' ? 'selected' : '' }}>Facturado</option>
                                    <option value="entregado" {{ $orden->estado === 'entregado' ? 'selected' : '' }}>Entregado</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="tecnico_asignado_id" class="form-label">Técnico Asignado</label>
                                <select name="tecnico_asignado_id" id="tecnico_asignado_id" class="form-select">
                                    <option value="">Sin asignar</option>
                                    @foreach(\App\Models\User::whereHas('roles', function($query) {
                                        $query->where('name', 'tecnico');
                                    })->get() as $tecnico)
                                        <option value="{{ $tecnico->id }}" {{ $orden->tecnico_asignado_id === $tecnico->id ? 'selected' : '' }}>
                                            {{ $tecnico->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="fecha_proxima_revision" class="form-label">Próxima Revisión</label>
                                <input type="date" name="fecha_proxima_revision" id="fecha_proxima_revision" class="form-control" value="{{ $orden->fecha_proxima_revision ? date('Y-m-d', strtotime($orden->fecha_proxima_revision)) : '' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <!-- Información del Cliente y Vehículo (Solo lectura) -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i> Datos del Cliente</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Cliente:</div>
                            <div class="col-md-8">
                                @if($orden->cliente->tipo_cliente == 'persona')
                                    {{ $orden->cliente->nombres }} {{ $orden->cliente->apellido_paterno }} {{ $orden->cliente->apellido_materno }}
                                @else
                                    {{ $orden->cliente->razon_social }}
                                @endif
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Documento:</div>
                            <div class="col-md-8">
                                {{ $orden->cliente->tipo_cliente == 'persona' ? 'DNI: ' : 'RUC: ' }}
                                {{ $orden->cliente->documento_identidad }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Contacto:</div>
                            <div class="col-md-8">
                                {{ $orden->cliente->correo ?? 'No registrado' }}
                                @if($orden->cliente->telefonos->count() > 0)
                                    <br>{{ $orden->cliente->telefonos->first()->numero }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-car me-2"></i> Datos del Vehículo</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Vehículo:</div>
                            <div class="col-md-8">
                                {{ $orden->vehiculo->marca->nombre ?? 'N/A' }} 
                                {{ $orden->vehiculo->modelo->nombre ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Placa:</div>
                            <div class="col-md-8">{{ $orden->vehiculo->nro_placa }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Kilometraje:</div>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="number" name="kilometraje_ingreso" class="form-control" value="{{ $orden->kilometraje_ingreso }}" min="0">
                                    <span class="input-group-text">km</span>
                                </div>
                                <small class="text-muted">Kilometraje al ingreso</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Detalles del servicio -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i> Detalles del Servicio</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="descripcion_problema" class="form-label">Descripción del Problema</label>
                    <textarea name="descripcion_problema" id="descripcion_problema" class="form-control" rows="3">{{ $orden->descripcion_problema }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="diagnostico" class="form-label">Diagnóstico</label>
                    <textarea name="diagnostico" id="diagnostico" class="form-control" rows="4">{{ $orden->diagnostico }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="recomendaciones" class="form-label">Recomendaciones</label>
                    <textarea name="recomendaciones" id="recomendaciones" class="form-control" rows="3">{{ $orden->recomendaciones }}</textarea>
                </div>
            </div>
        </div>
        
        <div class="d-grid gap-2 mb-4">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
@endsection

@push('scripts')
    <!-- Datepicker -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar Select2
            $('.form-select').select2({
                theme: 'bootstrap-5'
            });
            
            // Inicializar Datepicker
            flatpickr("#fecha_proxima_revision", {
                dateFormat: "Y-m-d",
                locale: "es",
                minDate: "today"
            });
        });
    </script>
@endpush