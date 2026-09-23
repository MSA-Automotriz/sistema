@extends('admin.layouts.app')

@section('title', 'Nuevo Taller')

@section('header', 'Nuevo Taller')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h2 class="h4 fw-bold mb-4">Crear Nuevo Taller</h2>
        <form method="POST" action="{{ route('admin.talleres.store') }}" class="needs-validation" novalidate>
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="nombre_taller" class="form-label">Nombre del Taller</label>
                    <input type="text" name="nombre_taller" id="nombre_taller" class="form-control @error('nombre_taller') is-invalid @enderror" value="{{ old('nombre_taller') }}" required>
                    @error('nombre_taller') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-2">
                    <label for="departamento" class="form-label">Departamento</label>
                    <input type="text" name="departamento" id="departamento" class="form-control @error('departamento') is-invalid @enderror" value="{{ old('departamento') }}" required>
                    @error('departamento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-2">
                    <label for="provincia" class="form-label">Provincia</label>
                    <input type="text" name="provincia" id="provincia" class="form-control @error('provincia') is-invalid @enderror" value="{{ old('provincia') }}" required>
                    @error('provincia') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-2">
                    <label for="distrito" class="form-label">Distrito</label>
                    <input type="text" name="distrito" id="distrito" class="form-control @error('distrito') is-invalid @enderror" value="{{ old('distrito') }}" required>
                    @error('distrito') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label for="direccion" class="form-label">Dirección</label>
                    <input type="text" name="direccion" id="direccion" class="form-control @error('direccion') is-invalid @enderror" value="{{ old('direccion') }}" required>
                    @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Ubicación en el Mapa</label>
                    <div id="map" style="height: 300px; width: 100%;"></div>
                    <input type="hidden" name="coordenadas" id="coordenadas" value="{{ old('coordenadas') }}" required>
                    @error('coordenadas') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="mt-4 text-end">
                <a href="{{ route('admin.talleres.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar Taller</button>
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
        // Inicializar el mapa con coordenadas por defecto (Lima, Perú)
        var map = L.map('map').setView([-12.0464, -77.0428], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var marker;

        // Permitir al usuario hacer clic en el mapa para colocar un marcador
        map.on('click', function(e) {
            if (marker) map.removeLayer(marker);
            marker = L.marker(e.latlng).addTo(map);
            document.getElementById('coordenadas').value = e.latlng.lat + ',' + e.latlng.lng;
        });

        // Función para geocodificar la dirección
        function buscarDireccion() {
            var direccion = document.getElementById('direccion').value;
            var departamento = document.getElementById('departamento').value;
            var provincia = document.getElementById('provincia').value;
            var distrito = document.getElementById('distrito').value;

            // Construir la consulta combinando todos los campos
            var query = `${direccion}, ${distrito}, ${provincia}, ${departamento}, Peru`;

            // Realizar la petición a Nominatim
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        var lat = data[0].lat;
                        var lon = data[0].lon;
                        map.setView([lat, lon], 15); // Centrar el mapa en la ubicación encontrada
                        if (marker) map.removeLayer(marker); // Eliminar marcador anterior
                        marker = L.marker([lat, lon]).addTo(map); // Colocar nuevo marcador
                        document.getElementById('coordenadas').value = lat + ',' + lon; // Actualizar campo oculto
                    } else {
                        alert('No se encontró la dirección. Por favor, verifica los datos ingresados.');
                    }
                })
                .catch(error => {
                    console.error('Error en la geocodificación:', error);
                    alert('Ocurrió un error al buscar la dirección. Intenta nuevamente.');
                });
        }

        // Ejecutar la búsqueda automáticamente al perder el foco en el campo de dirección
        document.getElementById('direccion').addEventListener('blur', function() {
            if (this.value.trim() !== '') { // Solo buscar si hay texto
                buscarDireccion();
            }
        });

        // Validación del formulario
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