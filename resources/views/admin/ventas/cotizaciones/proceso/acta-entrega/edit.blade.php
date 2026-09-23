<div class="card border shadow-none">
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-semibold">
                <i class="fas fa-clipboard-check me-2 text-primary"></i> Acta de Entrega de Vehículo
            </h5>
            <div>
                <a href="{{ route('admin.ventas.cotizaciones.acta-entrega.pdf', $cotizacion) }}" target="_blank" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i> Generar PDF
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="mb-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h6 class="fw-semibold mb-1">Acta #{{ $cotizacion->acta_entrega->codigo }}</h6>
                    <p class="text-muted mb-0">Fecha: {{ $cotizacion->acta_entrega->fecha_entrega->format('d/m/Y') }}</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="badge bg-{{ $cotizacion->acta_entrega->estado === 'Completada' ? 'success' : 'warning' }} p-2">
                        {{ $cotizacion->acta_entrega->estado }}
                    </span>
                </div>
            </div>
        </div>
        
        <form action="{{ route('admin.ventas.cotizaciones.acta-entrega.update', $cotizacion) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row g-4">
                <div class="col-md-6">
                    <label for="fecha_entrega" class="form-label">Fecha de entrega</label>
                    <input type="date" class="form-control" id="fecha_entrega" name="fecha_entrega" 
                        value="{{ $cotizacion->acta_entrega->fecha_entrega->format('Y-m-d') }}" required>
                </div>
                
                <div class="col-md-6">
                    <label for="persona_entrega" class="form-label">Persona que entrega</label>
                    <input type="text" class="form-control" id="persona_entrega" name="persona_entrega" 
                        value="{{ $cotizacion->acta_entrega->persona_entrega }}" required>
                </div>
                
                <div class="col-md-12">
                    <label for="vehiculo_detalle" class="form-label">Detalle del vehículo</label>
                    <input type="text" class="form-control" id="vehiculo_detalle" name="vehiculo_detalle" 
                        value="{{ $cotizacion->acta_entrega->vehiculo_detalle }}" required>
                </div>
                
                <div class="col-md-6">
                    <label for="placa" class="form-label">Número de placa</label>
                    <input type="text" class="form-control" id="placa" name="placa" 
                        value="{{ $cotizacion->acta_entrega->placa }}">
                </div>
                
                <div class="col-md-6">
                    <label for="kilometraje" class="form-label">Kilometraje</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="kilometraje" name="kilometraje" 
                            value="{{ $cotizacion->acta_entrega->kilometraje }}" min="0" required>
                        <span class="input-group-text">Km</span>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <label for="nivel_combustible" class="form-label">Nivel de combustible</label>
                    <select class="form-select" id="nivel_combustible" name="nivel_combustible" required>
                        <option value="0" {{ $cotizacion->acta_entrega->nivel_combustible == 0 ? 'selected' : '' }}>Vacío</option>
                        <option value="25" {{ $cotizacion->acta_entrega->nivel_combustible == 25 ? 'selected' : '' }}>1/4</option>
                        <option value="50" {{ $cotizacion->acta_entrega->nivel_combustible == 50 ? 'selected' : '' }}>1/2</option>
                        <option value="75" {{ $cotizacion->acta_entrega->nivel_combustible == 75 ? 'selected' : '' }}>3/4</option>
                        <option value="100" {{ $cotizacion->acta_entrega->nivel_combustible == 100 ? 'selected' : '' }}>Lleno</option>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label for="estado" class="form-label">Estado del acta</label>
                    <select class="form-select" id="estado" name="estado" required>
                        <option value="En proceso" {{ $cotizacion->acta_entrega->estado === 'En proceso' ? 'selected' : '' }}>En proceso</option>
                        <option value="Completada" {{ $cotizacion->acta_entrega->estado === 'Completada' ? 'selected' : '' }}>Completada</option>
                    </select>
                </div>
                
                <!-- Aquí iría el resto del formulario con los checklist -->
                <!-- Mantén la misma estructura que en tu código original de paste-2.txt -->
                
                <div class="col-md-12">
                    <label for="observaciones" class="form-label">Observaciones</label>
                    <textarea class="form-control" id="observaciones" name="observaciones" rows="3">{{ $cotizacion->acta_entrega->observaciones }}</textarea>
                </div>
                
                <div class="col-md-12">
                    <label for="documento_firmado" class="form-label">Acta de entrega firmada (opcional)</label>
                    <input type="file" class="form-control" id="documento_firmado" name="documento_firmado" accept=".pdf,.jpg,.jpeg,.png">
                    <div class="form-text">Puede subir el acta firmada por el cliente en formato PDF o imagen.</div>
                    
                    @if($cotizacion->acta_entrega->documento_firmado)
                    <div class="mt-2">
                        <div class="d-flex align-items-center">
                            <div class="form-check me-2">
                                <input class="form-check-input" type="checkbox" id="mantener_documento" name="mantener_documento" value="1" checked>
                                <label class="form-check-label" for="mantener_documento">Mantener documento actual</label>
                            </div>
                            <a href="{{ asset('storage/'.$cotizacion->acta_entrega->documento_firmado) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-file me-1"></i> Ver documento
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="col-md-12">
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-1"></i> Actualizar Acta de Entrega
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>