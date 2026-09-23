<div class="modal fade" id="editarOrdenModal" tabindex="-1" aria-labelledby="editarOrdenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.ventas.cotizaciones.orden-trabajo.update', ['cotizacion' => $cotizacion, 'orden' => $orden]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editarOrdenModalLabel">Editar Orden de Trabajo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Contenido del formulario de edición -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Estado *</label>
                                <select class="form-select" name="estado" required>
                                    <option value="pendiente" {{ $orden->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="en_progreso" {{ $orden->estado == 'en_progreso' ? 'selected' : '' }}>En progreso</option>
                                    <option value="completado" {{ $orden->estado == 'completado' ? 'selected' : '' }}>Completado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Vehículo</label>
                                @if($cotizacion->detalles && $cotizacion->detalles->isNotEmpty() && $cotizacion->detalles->first()->vehiculo)
                                    @php
                                        $detalle = $cotizacion->detalles->first();
                                        $vehiculo = $detalle->vehiculo;
                                    @endphp
                                    <div class="form-control bg-light">
                                        {{ $vehiculo->marca->nombre ?? 'Sin marca' }} {{ $vehiculo->modelo->nombre ?? 'Sin modelo' }}
                                        ({{ $detalle->color->nombre ?? 'Color no especificado' }})
                                    </div>
                                    <input type="hidden" name="vehiculo_id" value="{{ $vehiculo->id }}">
                                @else
                                    <div class="alert alert-warning py-2">
                                        No hay vehículo asociado a esta cotización
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fecha inicio</label>
                                <input type="datetime-local" class="form-control" name="fecha_inicio" value="{{ $orden->fecha_inicio ? $orden->fecha_inicio->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i') }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fecha estimada fin</label>
                                <input type="datetime-local" class="form-control" name="fecha_fin_estimada" value="{{ $orden->fecha_fin_estimada ? $orden->fecha_fin_estimada->format('Y-m-d\TH:i') : '' }}">
                            </div>
                        </div>
                    </div>
                   
                    <div class="mb-3">
                        <label class="form-label">Descripción *</label>
                        <textarea class="form-control" name="descripcion" rows="4" required>{{ $orden->descripcion }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea class="form-control" name="observaciones" rows="3">{{ $orden->observaciones }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Orden</button>
                </div>
            </form>
        </div>
    </div>
</div>