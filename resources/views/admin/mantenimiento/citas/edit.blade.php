<!-- resources/views/admin/mantenimiento/citas/edit.blade.php -->
@extends('admin.layouts.app')

@section('title', 'Editar Cita de Mantenimiento')

@push('styles')
    <!-- Estilos para Datepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Select2 para mejorar los selectores -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Editar Cita #{{ $cita->id }}</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('admin.mantenimiento.citas.show', $cita) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.mantenimiento.citas.update', $cita) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row mb-4">
            <!-- Información del Cliente -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i> Datos del Cliente</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Cliente:</div>
                            <div class="col-md-8">
                                @if($cita->cliente->tipo_cliente == 'persona')
                                    {{ $cita->cliente->nombres }} {{ $cita->cliente->apellido_paterno }} {{ $cita->cliente->apellido_materno }}
                                @else
                                    {{ $cita->cliente->razon_social }}
                                @endif
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Documento:</div>
                            <div class="col-md-8">
                                {{ $cita->cliente->tipo_cliente == 'persona' ? 'DNI: ' : 'RUC: ' }}
                                {{ $cita->cliente->documento_identidad }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Contacto:</div>
                            <div class="col-md-8">
                                {{ $cita->cliente->correo ?? 'No registrado' }}
                                @if($cita->cliente->telefonos->count() > 0)
                                    <br>{{ $cita->cliente->telefonos->first()->numero }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Información del Vehículo -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-car me-2"></i> Datos del Vehículo</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Vehículo:</div>
                            <div class="col-md-8">
                                {{ $cita->vehiculo->marca->nombre ?? 'N/A' }} 
                                {{ $cita->vehiculo->modelo->nombre ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Placa:</div>
                            <div class="col-md-8">{{ $cita->vehiculo->nro_placa }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Kilometraje:</div>
                            <div class="col-md-8">{{ number_format($cita->vehiculo->kilometraje, 0, '.', ',') }} km</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Datos de la Cita -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i> Datos de la Cita</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="fecha_hora_cita" class="form-label">Fecha y Hora de la Cita</label>
                        <input type="text" name="fecha_hora_cita" id="fecha_hora_cita" class="form-control" value="{{ $cita->fecha_hora_cita }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select name="estado" id="estado" class="form-select" required>
                            <option value="pendiente" {{ $cita->estado === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="confirmada" {{ $cita->estado === 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                            <option value="en_progreso" {{ $cita->estado === 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                            <option value="completada" {{ $cita->estado === 'completada' ? 'selected' : '' }}>Completada</option>
                            <option value="cancelada" {{ $cita->estado === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="tecnico_id" class="form-label">Técnico Asignado</label>
                        <select name="tecnico_id" id="tecnico_id" class="form-select">
                            <option value="">Sin asignar</option>
                            @foreach($tecnicos as $tecnico)
                                <option value="{{ $tecnico->id }}" {{ $cita->tecnico_id === $tecnico->id ? 'selected' : '' }}>{{ $tecnico->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="motivo_visita" class="form-label">Motivo de la Visita</label>
                        <input type="text" name="motivo_visita" id="motivo_visita" class="form-control" value="{{ $cita->motivo_visita }}" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="descripcion_problema" class="form-label">Descripción del Problema</label>
                        <textarea name="descripcion_problema" id="descripcion_problema" class="form-control" rows="3">{{ $cita->descripcion_problema }}</textarea>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="notas_adicionales" class="form-label">Observaciones</label>
                        <textarea name="notas_adicionales" id="notas_adicionales" class="form-control" rows="2">{{ $cita->notas_adicionales }}</textarea>
                    </div>
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
            // Inicializar Datepicker
            flatpickr("#fecha_hora_cita", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                locale: "es",
                time_24hr: true
            });
            
            // Inicializar Select2
            $('.form-select').select2({
                theme: 'bootstrap-5'
            });
        });
    </script>
@endpush