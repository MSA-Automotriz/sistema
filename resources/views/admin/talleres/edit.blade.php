@extends('admin.layouts.app')

@section('title', 'Editar Taller')

@section('header', 'Editar Taller')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h2 class="h4 fw-bold mb-4">Editar Taller</h2>
        <form method="POST" action="{{ route('admin.talleres.update', $taller) }}" class="needs-validation" novalidate>
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="nombre_taller" class="form-label">Nombre del Taller</label>
                    <input type="text" name="nombre_taller" id="nombre_taller" class="form-control @error('nombre_taller') is-invalid @enderror" value="{{ old('nombre_taller', $taller->nombre_taller) }}" required>
                    @error('nombre_taller') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="departamento" class="form-label">Departamento</label>
                    <input type="text" name="departamento" id="departamento" class="form-control @error('departamento') is-invalid @enderror" value="{{ old('departamento', $taller->departamento) }}" required>
                    @error('departamento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="provincia" class="form-label">Provincia</label>
                    <input type="text" name="provincia" id="provincia" class="form-control @error('provincia') is-invalid @enderror" value="{{ old('provincia', $taller->provincia) }}" required>
                    @error('provincia') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="distrito" class="form-label">Distrito</label>
                    <input type="text" name="distrito" id="distrito" class="form-control @error('distrito') is-invalid @enderror" value="{{ old('distrito', $taller->distrito) }}" required>
                    @error('distrito') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label for="direccion" class="form-label">Dirección</label>
                    <input type="text" name="direccion" id="direccion" class="form-control @error('direccion') is-invalid @enderror" value="{{ old('direccion', $taller->direccion) }}" required>
                    @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Ubicación en el Mapa</label>
                    <div id="map" style="height: 300px; width: 100%;"></div>
                    <input type="hidden" name="coordenadas" id="coordenadas" value="{{ old('coordenadas', $taller->coordenadas) }}" required>
                    @error('coordenadas') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="mt-4 text-end">
                <a href="{{ route('admin.talleres.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Actualizar Taller</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    window.addEventListener('load', function () {
        var coords = document.getElementById('coordenadas').value.split(',');
        var lat = coords[0] || -12.0464;
        var lng = coords[1] || -77.0428;
        var map = L.map('map').setView([lat, lng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var marker = L.marker([lat, lng]).addTo(map);
        map.on('click', function(e) {
            map.removeLayer(marker);
            marker = L.marker(e.latlng).addTo(map);
            document.getElementById('coordenadas').value = e.latlng.lat + ',' + e.latlng.lng;
        });

        var forms = document.getElementsByClassName('needs-validation');
        Array.prototype.filter.call(forms, function (form) {
            form.addEventListener('submit', function (event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    });
</script>
@endpush