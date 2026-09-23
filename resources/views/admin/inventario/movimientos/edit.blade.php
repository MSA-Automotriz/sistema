@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="fw-bold">{{ isset($movimiento) ? 'Editar' : 'Nuevo' }} Movimiento</h2>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ isset($movimiento) ? route('admin.inventario.movimientos.update', $movimiento) : route('admin.inventario.movimientos.store') }}" method="POST">
                @csrf
                @if(isset($movimiento))
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tipo_movimiento_id" class="form-label">Tipo de Movimiento</label>
                        <select class="form-select" id="tipo_movimiento_id" name="tipo_movimiento_id" required>
                            <option value="">Seleccione...</option>
                            @foreach($tiposMovimiento as $tipo)
                            <option value="{{ $tipo->id }}" {{ (isset($movimiento) && $movimiento->tipo_movimiento_id == $tipo->id) ? 'selected' : '' }}>
                                {{ $tipo->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="parte_id" class="form-label">Parte/Producto</label>
                        <select class="form-select" id="parte_id" name="parte_id" required>
                            <option value="">Seleccione...</option>
                            @foreach($partes as $parte)
                            <option value="{{ $parte->id }}" {{ (isset($movimiento) && $movimiento->parte_id == $parte->id) ? 'selected' : '' }}>
                                {{ $parte->codigo }} - {{ $parte->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="almacen_id" class="form-label">Almacén</label>
                        <select class="form-select" id="almacen_id" name="almacen_id" required>
                            <option value="">Seleccione...</option>
                            @foreach($almacenes as $almacen)
                            <option value="{{ $almacen->id }}" {{ (isset($movimiento) && $movimiento->almacen_id == $almacen->id) ? 'selected' : '' }}>
                                {{ $almacen->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="centro_costo_id" class="form-label">Centro de Costo</label>
                        <select class="form-select" id="centro_costo_id" name="centro_costo_id" required>
                            <option value="">Seleccione...</option>
                            @foreach($centrosCostos as $centro)
                            <option value="{{ $centro->id }}" {{ (isset($movimiento) && $movimiento->centro_costo_id == $centro->id) ? 'selected' : '' }}>
                                {{ $centro->codigo }} - {{ $centro->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="cantidad" class="form-label">Cantidad</label>
                        <input type="number" class="form-control" id="cantidad" name="cantidad"
                               value="{{ $movimiento->cantidad ?? old('cantidad') }}" min="1" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="documento_referencia" class="form-label">Documento Referencia</label>
                        <input type="text" class="form-control" id="documento_referencia" name="documento_referencia"
                               value="{{ $movimiento->documento_referencia ?? old('documento_referencia') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="fecha_movimiento" class="form-label">Fecha Movimiento</label>
                        <input type="datetime-local" class="form-control" id="fecha_movimiento" name="fecha_movimiento"
                               value="{{ isset($movimiento) ? (\Carbon\Carbon::parse($movimiento->fecha_movimiento)->format('Y-m-d\TH:i')) : old('fecha_movimiento', now()->format('Y-m-d\TH:i')) }}" required>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3">{{ $movimiento->observaciones ?? old('observaciones') }}</textarea>
                    </div>
                </div>

                <div class="text-end">
                    <a href="{{ route('admin.inventario.movimientos.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        {{ isset($movimiento) ? 'Actualizar' : 'Guardar' }} Movimiento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
