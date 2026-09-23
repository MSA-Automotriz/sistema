@extends('admin.layouts.app')

@section('title', 'Editar Vehículo Catálogo')

@section('header', 'Editar Vehículo Catálogo')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h2 class="h4 fw-bold mb-4">Editar Vehículo Catálogo</h2>
                <form method="POST" action="{{ route('admin.productos-servicios.vehiculos.caracteristicas.vehiculo.update', $Vehiculo) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="marca_id" class="form-label">Marca *</label>
                            <select name="marca_id" id="marca_id" class="form-select @error('marca_id') is-invalid @enderror" required>
                                <option value="">Seleccione una marca</option>
                                @foreach ($marcas as $marca)
                                    <option value="{{ $marca->id }}" {{ old('marca_id', $Vehiculo->marca_id) == $marca->id ? 'selected' : '' }}>{{ $marca->nombre }}</option>
                                @endforeach
                            </select>
                            @error('marca_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="modelo_id" class="form-label">Modelo *</label>
                            <select name="modelo_id" id="modelo_id" class="form-select @error('modelo_id') is-invalid @enderror" required>
                                <option value="">Seleccione un modelo</option>
                                @foreach ($modelos as $modelo)
                                    <option value="{{ $modelo->id }}" {{ old('modelo_id', $Vehiculo->modelo_id) == $modelo->id ? 'selected' : '' }}>{{ $modelo->nombre }}</option>
                                @endforeach
                            </select>
                            @error('modelo_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="version_id" class="form-label">Versión *</label>
                            <select name="version_id" id="version_id" class="form-select @error('version_id') is-invalid @enderror" required>
                                <option value="">Seleccione una versión</option>
                                @foreach ($versiones as $version)
                                    <option value="{{ $version->id }}" {{ old('version_id', $Vehiculo->version_id) == $version->id ? 'selected' : '' }}>{{ $version->nombre }}</option>
                                @endforeach
                            </select>
                            @error('version_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="anio_modelo_id" class="form-label">Año del Modelo *</label>
                            <select name="anio_modelo_id" id="anio_modelo_id" class="form-select @error('anio_modelo_id') is-invalid @enderror" required>
                                <option value="">Seleccione un año</option>
                                @foreach ($aniosModelo as $anioModelo)
                                    <option value="{{ $anioModelo->id }}" {{ old('anio_modelo_id', $Vehiculo->anio_modelo_id) == $anioModelo->id ? 'selected' : '' }}>{{ $anioModelo->anio }}</option>
                                @endforeach
                            </select>
                            @error('anio_modelo_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="fotografia" class="form-label">Fotografía</label>
                            <input type="file" name="fotografia" id="fotografia" class="form-control @error('fotografia') is-invalid @enderror" accept="image/*">
                            @if($Vehiculo->fotografia)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $Vehiculo->fotografia) }}" alt="Fotografía del vehículo" style="max-width: 200px; height: auto;">
                                </div>
                            @endif
                            @error('fotografia') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.productos-servicios.vehiculos.index') }}">Volver</a>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection