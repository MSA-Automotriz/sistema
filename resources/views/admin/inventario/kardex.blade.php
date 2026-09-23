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
            <form action="{{ route('admin.inventario.kardex.reporte') }}" method="GET">
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
    });
</script>
@endpush