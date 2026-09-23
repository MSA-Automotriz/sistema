@php
    use App\Models\PlacaInfo;
@endphp

<div class="modal fade" id="modalEditarPlaca{{ $placa->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.ventas.cotizaciones.placas.update', [$cotizacion, $placa]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar Placa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="tipo_placa_edit" class="form-label">Tipo de Placa</label>
                            <select class="form-select" id="tipo_placa_edit" name="tipo_placa" required>
                                <option value="rotativa" {{ $placa->tipo_placa == 'rotativa' ? 'selected' : '' }}>Rotativa</option>
                                <option value="definitiva" {{ $placa->tipo_placa == 'definitiva' ? 'selected' : '' }}>Definitiva</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="paso_actual_edit" class="form-label">Estado Actual</label>
                            <select class="form-select" id="paso_actual_edit" name="paso_actual" required>
                                @foreach(PlacaInfo::ESTADOS as $paso => $estado)
                                <option value="{{ $paso }}" {{ $placa->paso_actual == $paso ? 'selected' : '' }}>
                                    {{ $paso }}. {{ $estado }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="numero_placa_edit" class="form-label">Número de Placa</label>
                            <input type="text" class="form-control" id="numero_placa_edit" name="numero_placa"
                                value="{{ $placa->numero_placa }}" placeholder="Ej: ABC-123">
                        </div>
                        <div class="col-md-6">
                            <label for="fecha_emision_edit" class="form-label">Fecha de Emisión</label>
                            <input type="date" class="form-control" id="fecha_emision_edit" name="fecha_emision"
                                value="{{ $placa->fecha_emision ? $placa->fecha_emision->format('Y-m-d') : '' }}">
                        </div>
                        <div class="col-12">
                            <label for="observaciones_edit" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="observaciones_edit" name="observaciones" rows="2">{{ $placa->observaciones }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Placa</button>
                </div>
            </form>
        </div>
    </div>
</div>