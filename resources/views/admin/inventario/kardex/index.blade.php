@extends('admin.layouts.app')
@section('title', 'Kardex')
@section('header', 'Kardex')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>Generar Reporte Kardex</h3>
        </div>
       
        <div class="card-body">
            <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="partes-tab" data-bs-toggle="tab" data-bs-target="#partes" type="button" role="tab" aria-controls="partes" aria-selected="true">Partes</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="vehiculos-tab" data-bs-toggle="tab" data-bs-target="#vehiculos" type="button" role="tab" aria-controls="vehiculos" aria-selected="false">Vehículos</button>
                </li>
            </ul>
            
            <div class="tab-content" id="myTabContent">
                <!-- Tab de Partes -->
                <div class="tab-pane fade show active" id="partes" role="tabpanel" aria-labelledby="partes-tab">
                    <form action="{{ route('admin.inventario.kardex.reporte') }}" method="GET">
                        <input type="hidden" name="tipo_reporte" value="parte">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="parte_id">Producto/Parte:</label>
                                    <select name="parte_id" id="parte_id" class="form-control select2" required>
                                        <option value="">Seleccione un producto</option>
                                        @foreach($partes as $parte)
                                        <option value="{{ $parte->id }}">{{ $parte->codigo }} - {{ $parte->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                           
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="almacen_id">Almacén (opcional):</label>
                                    <select name="almacen_id" id="almacen_id" class="form-control select2">
                                        <option value="">Todos los almacenes</option>
                                        @foreach($almacenes as $almacen)
                                        <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                       
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_inicio">Fecha Inicio:</label>
                                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control">
                                </div>
                            </div>
                           
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_fin">Fecha Fin:</label>
                                    <input type="date" name="fecha_fin" id="fecha_fin" class="form-control">
                                </div>
                            </div>
                        </div>
                       
                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-primary">Generar Reporte</button>
                        </div>
                    </form>
                </div>
                
                <!-- Tab de Vehículos -->
                <div class="tab-pane fade" id="vehiculos" role="tabpanel" aria-labelledby="vehiculos-tab">
                    <form action="{{ route('admin.inventario.kardex.reporte') }}" method="GET">
                        <input type="hidden" name="tipo_reporte" value="vehiculo">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vehiculo_id">Vehículo:</label>
                                    <select name="vehiculo_id" id="vehiculo_id" class="form-control select2" required>
                                        <option value="">Seleccione un vehículo</option>
                                        @foreach($vehiculos as $vehiculo)
                                        <option value="{{ $vehiculo->id }}">
                                            {{ $vehiculo->marca->nombre ?? 'N/A' }} - 
                                            {{ $vehiculo->modelo->nombre ?? 'N/A' }} - 
                                            {{ $vehiculo->version->nombre ?? 'N/A' }} - 
                                            {{ $vehiculo->anioModelo->nombre ?? 'N/A' }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                           
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="almacen_id_vehiculo">Almacén (opcional):</label>
                                    <select name="almacen_id" id="almacen_id_vehiculo" class="form-control select2">
                                        <option value="">Todos los almacenes</option>
                                        @foreach($almacenes as $almacen)
                                        <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                       
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_inicio_vehiculo">Fecha Inicio:</label>
                                    <input type="date" name="fecha_inicio" id="fecha_inicio_vehiculo" class="form-control">
                                </div>
                            </div>
                           
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_fin_vehiculo">Fecha Fin:</label>
                                    <input type="date" name="fecha_fin" id="fecha_fin_vehiculo" class="form-control">
                                </div>
                            </div>
                        </div>
                       
                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-primary">Generar Reporte</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Seleccione una opción",
            allowClear: true
        });
        
        // Cambiar el tipo de reporte según la pestaña activa
        $('#myTab button').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("id");
            if (target === 'vehiculos-tab') {
                $('input[name="tipo_reporte"]').val('vehiculo');
            } else {
                $('input[name="tipo_reporte"]').val('parte');
            }
        });
    });
</script>
@endpush