<form method="POST" action="{{ route('admin.ventas.cotizaciones.seguimiento.agregar', $cotizacion) }}" id="formGestionCotizacion">
    @csrf

    <!-- Tipo de seguimiento -->
    <div class="mb-3">
        <label class="form-label fw-medium">Tipo de seguimiento</label>
        <div class="d-flex flex-wrap gap-2">
            <input type="radio" class="btn-check" name="tipo" id="tipoNota" value="nota" autocomplete="off" checked>
            <label class="btn btn-outline-warning" for="tipoNota">
                <i class="fas fa-sticky-note me-1"></i> Nota
            </label>
            <input type="radio" class="btn-check" name="tipo" id="tipoLlamada" value="llamada" autocomplete="off">
            <label class="btn btn-outline-success" for="tipoLlamada">
                <i class="fas fa-phone-alt me-1"></i> Llamada
            </label>
            <input type="radio" class="btn-check" name="tipo" id="tipoReunion" value="reunion" autocomplete="off">
            <label class="btn btn-outline-primary" for="tipoReunion">
                <i class="fas fa-handshake me-1"></i> Reunión
            </label>
            <input type="radio" class="btn-check" name="tipo" id="tipoEmail" value="email" autocomplete="off">
            <label class="btn btn-outline-info" for="tipoEmail">
                <i class="fas fa-envelope me-1"></i> Email
            </label>
            <input type="radio" class="btn-check" name="tipo" id="tipoOtro" value="otro" autocomplete="off">
            <label class="btn btn-outline-secondary" for="tipoOtro">
                <i class="fas fa-comment me-1"></i> Otro
            </label>
        </div>
    </div>

    <!-- Fecha de seguimiento -->
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label for="fecha_seguimiento_date" class="form-label fw-medium">Fecha de seguimiento</label>
            <input type="date" class="form-control" id="fecha_seguimiento_date" name="fecha_seguimiento_date" 
                   value="{{ now()->format('Y-m-d') }}" required>
        </div>
        <div class="col-md-6">
            <label for="fecha_seguimiento_time" class="form-label fw-medium">Hora</label>
            <input type="time" class="form-control" id="fecha_seguimiento_time" name="fecha_seguimiento_time" 
                   value="{{ now()->format('H:i') }}" required>
        </div>
    </div>

    <!-- Campo de comentarios -->
    <div class="mb-3">
        <label for="contenido" class="form-label fw-medium">Comentarios</label>
        <textarea class="form-control" id="contenido" name="contenido" rows="4" 
                  placeholder="Ingrese sus comentarios sobre la gestión..." required></textarea>
    </div>

    <!-- Sección de recordatorio -->
    <div class="card mb-3 border-warning">
        <div class="card-header bg-warning-subtle">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="habilitarRecordatorio" name="recordatorio" value="1">
                <label class="form-check-label fw-medium" for="habilitarRecordatorio">
                    <i class="fas fa-bell me-1"></i> Crear recordatorio para seguimiento
                </label>
            </div>
        </div>
        <div class="card-body" id="seccionRecordatorio" style="display: none;">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="fecha_recordatorio" class="form-label">Fecha de recordatorio</label>
                    <input type="date" class="form-control" id="fecha_recordatorio" name="fecha_recordatorio" 
                           value="{{ now()->addDays(3)->format('Y-m-d') }}">
                </div>
                <div class="col-md-6">
                    <label for="hora_recordatorio" class="form-label">Hora</label>
                    <input type="time" class="form-control" id="hora_recordatorio" name="hora_recordatorio" 
                           value="10:00">
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <a href="{{ route('admin.ventas.cotizaciones.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
        <button type="submit" class="btn btn-primary btn-lg px-4">
            <i class="fas fa-save me-1"></i> Guardar Gestión
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Manejar recordatorio
    $('#habilitarRecordatorio').change(function() {
        if($(this).is(':checked')) {
            $('#seccionRecordatorio').slideDown();
        } else {
            $('#seccionRecordatorio').slideUp();
        }
    });

    // Validar formulario antes de enviar
    $('#formGestionCotizacion').submit(function(e) {
        // Validar contenido
        if (!$('#contenido').val().trim()) {
            e.preventDefault();
            toastr.error('El campo comentarios es obligatorio.');
            return;
        }

        // Validar recordatorio
        if ($('#habilitarRecordatorio').is(':checked')) {
            if (!$('#fecha_recordatorio').val() || !$('#hora_recordatorio').val()) {
                e.preventDefault();
                toastr.error('Complete la fecha y hora del recordatorio.');
            }
        }

        // Validar fecha y hora de seguimiento
        if (!$('#fecha_seguimiento_date').val() || !$('#fecha_seguimiento_time').val()) {
            e.preventDefault();
            toastr.error('Complete la fecha y hora de seguimiento.');
        }
    });
});
</script>