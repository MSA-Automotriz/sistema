@extends('admin.layouts.app')

@section('title', 'Editar Clasificación')

@section('header', 'Editar Clasificación')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h2 class="h4 fw-bold mb-4">Editar Clasificación: {{ $clasificacionVehiculo->nombre }}</h2>
                <form method="POST" action="{{ route('admin.configuracion.maestros.clasificacion_vehiculos.update', $clasificacionVehiculo) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre *</label>
                        <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $clasificacionVehiculo->nombre) }}" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.configuracion.maestros.clasificacion_vehiculos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection