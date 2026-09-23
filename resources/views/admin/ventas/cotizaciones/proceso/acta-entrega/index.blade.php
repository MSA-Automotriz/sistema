<div class="card border shadow-none">
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-semibold">
                <i class="fas fa-clipboard-check me-2 text-primary"></i> Acta de Entrega de Vehículo
            </h5>
            <div>
                @if(isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega)
                <a href="#', $cotizacion) }}" target="_blank" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i> Generar PDF
                </a>
                @endif
            </div>
        </div>
    </div>
    <div class="card-body">
        @if(isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega)
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
        @else
            <form action="{{ route('admin.ventas.cotizaciones.acta-entrega.store', $cotizacion) }}" method="POST" enctype="multipart/form-data">
                @csrf
        @endif
                <div class="row g-4">
                    <div class="col-md-4">
                        <label for="fecha_entrega" class="form-label">Fecha de entrega</label>
                        <input type="date" class="form-control" id="fecha_entrega" name="fecha_entrega" value="{{ isset($cotizacion->acta_entrega) ? $cotizacion->acta_entrega->fecha_entrega->format('Y-m-d') : date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="persona_entrega" class="form-label">Persona que entrega</label>
                        <input type="text" class="form-control" id="persona_entrega" name="persona_entrega" value="{{ isset($cotizacion->acta_entrega) ? $cotizacion->acta_entrega->persona_entrega : auth()->user()->name }}" required>
                    </div>
                    
                    <!--div class="col-md-12">
                        <label for="vehiculo_detalle" class="form-label">Detalle del vehículo</label>
                        <input type="text" class="form-control" id="vehiculo_detalle" name="vehiculo_detalle" 
                            value="{{ isset($cotizacion->acta_entrega) ? $cotizacion->acta_entrega->vehiculo_detalle : ($cotizacion->detalles && $cotizacion->detalles->where('tipo', 'vehiculos')->first() ? $cotizacion->detalles->where('tipo', 'vehiculos')->first()->descripcion : '') }}" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="placa" class="form-label">Número de placa</label>
                        <input type="text" class="form-control" id="placa" name="placa" value="{{ isset($cotizacion->acta_entrega) ? $cotizacion->acta_entrega->placa : '' }}">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="kilometraje" class="form-label">Kilometraje</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="kilometraje" name="kilometraje" value="{{ isset($cotizacion->acta_entrega) ? $cotizacion->acta_entrega->kilometraje : 0 }}" min="0" required>
                            <span class="input-group-text">Km</span>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="nivel_combustible" class="form-label">Nivel de combustible</label>
                        <select class="form-select" id="nivel_combustible" name="nivel_combustible" required>
                            <option value="0" {{ isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->nivel_combustible == 0 ? 'selected' : '' }}>Vacío</option>
                            <option value="25" {{ isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->nivel_combustible == 25 ? 'selected' : '' }}>1/4</option>
                            <option value="50" {{ isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->nivel_combustible == 50 ? 'selected' : '' }}>1/2</option>
                            <option value="75" {{ isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->nivel_combustible == 75 ? 'selected' : '' }}>3/4</option>
                            <option value="100" {{ isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->nivel_combustible == 100 ? 'selected' : '' }}>Lleno</option>
                        </select>
                    </div-->
                    
                    <div class="col-md-4">
                        <label for="estado" class="form-label">Estado del acta</label>
                        <select class="form-select" id="estado" name="estado" required>
                            <option value="En proceso" {{ isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->estado === 'En proceso' ? 'selected' : '' }}>En proceso</option>
                            <option value="Completada" {{ isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->estado === 'Completada' ? 'selected' : '' }}>Completada</option>
                        </select>
                    </div>
                    
                    <div class="col-md-12">
                        <h6 class="fw-semibold mb-3 border-bottom pb-2">HERRAMIENTAS - Checklist de entrega</h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0 fw-semibold">Documentación</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="check_manual" name="check_manual" value="1" {{ isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->check_manual ? 'checked' : '' }}>
                                                <label class="form-check-label" for="check_manual">Manual del propietario</label>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="check_garantia" name="check_garantia" value="1" {{ isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->check_garantia ? 'checked' : '' }}>
                                                <label class="form-check-label" for="check_garantia">Libreta de garantía</label>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="check_tarjeta" name="check_tarjeta" value="1" {{ isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->check_tarjeta ? 'checked' : '' }}>
                                                <label class="form-check-label" for="check_tarjeta">Tarjeta de propiedad</label>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="check_soat" name="check_soat" value="1" {{ isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->check_soat ? 'checked' : '' }}>
                                                <label class="form-check-label" for="check_soat">SOAT vigente</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0 fw-semibold">Accesorios</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="check_llave" name="check_llave" value="1" {{ isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->check_llave ? 'checked' : '' }}>
                                                <label class="form-check-label" for="check_llave">Juego de llaves</label>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="check_gata" name="check_gata" value="1" {{ isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->check_gata ? 'checked' : '' }}>
                                                <label class="form-check-label" for="check_gata">Gata hidráulica</label>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="check_rueda" name="check_rueda" value="1" {{ isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->check_rueda ? 'checked' : '' }}>
                                                <label class="form-check-label" for="check_rueda">Rueda de repuesto</label>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="check_herramientas" name="check_herramientas" value="1" {{ isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->check_herramientas ? 'checked' : '' }}>
                                                <label class="form-check-label" for="check_herramientas">Kit de herramientas</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0 fw-semibold">Estado exterior</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="check_carroceria" name="check_carroceria" value="1" {{ isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->check_carroceria ? 'checked' : '' }}>
                                                <label class="form-check-label" for="check_carroceria">Carrocería sin golpes</label>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="check_pintura" name="check_pintura" value="1" {{ isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->check_pintura ? 'checked' : '' }}>
                                                <label class="form-check-label" for="check_pintura">Pintura en buen estado</label>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="check_lunas" name="check_lunas" value="1" {{ isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->check_lunas ? 'checked' : '' }}>
                                                <label class="form-check-label" for="check_lunas">Lunas y parabrisas sin daños</label>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="check_llantas" name="check_llantas" value="1" {{ isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->check_llantas ? 'checked' : '' }}>
                                                <label class="form-check-label" for="check_llantas">Llantas en buen estado</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0 fw-semibold">Estado interior</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="check_asientos" name="check_asientos" value="1" {{ isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->check_asientos ? 'checked' : '' }}>
                                                <label class="form-check-label" for="check_asientos">Asientos en buen estado</label>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="check_tablero" name="check_tablero" value="1" {{ isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->check_tablero ? 'checked' : '' }}>
                                                <label class="form-check-label" for="check_tablero">Tablero sin daños</label>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="check_radio" name="check_radio" value="1" {{ isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->check_radio ? 'checked' : '' }}>
                                                <label class="form-check-label" for="check_radio">Radio/sistema multimedia funcional</label>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="check_climatizacion" name="check_climatizacion" value="1" {{ isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->check_climatizacion ? 'checked' : '' }}>
                                                <label class="form-check-label" for="check_climatizacion">Aire acondicionado/calefacción</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <div class="card border">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0 fw-semibold">Funcionamiento</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="check_motor" name="check_motor" value="1" {{ isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->check_motor ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="check_motor">Motor en buen estado</label>
                                                    </div>
                                                </div>
                                                <div class="mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="check_luces" name="check_luces" value="1" {{ isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->check_luces ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="check_luces">Sistema de luces completo</label>
                                                    </div>
                                                </div>
                                                <div class="mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="check_frenos" name="check_frenos" value="1" {{ isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->check_frenos ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="check_frenos">Sistema de frenos</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="check_direccion" name="check_direccion" value="1" {{ isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->check_direccion ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="check_direccion">Dirección</label>
                                                    </div>
                                                </div>
                                                <div class="mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="check_bateria" name="check_bateria" value="1" {{ isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->check_bateria ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="check_bateria">Batería</label>
                                                    </div>
                                                </div>
                                                <div class="mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="check_arranque" name="check_arranque" value="1" {{ isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->check_arranque ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="check_arranque">Arranque</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3">{{ isset($cotizacion->acta_entrega) ? $cotizacion->acta_entrega->observaciones : '' }}</textarea>
                    </div>
                    
                    <div class="col-md-12">
                        <label for="documento_firmado" class="form-label">Acta de entrega firmada (opcional)</label>
                        <input type="file" class="form-control" id="documento_firmado" name="documento_firmado" accept=".pdf,.jpg,.jpeg,.png">
                        <div class="form-text">Puede subir el acta firmada por el cliente en formato PDF o imagen.</div>
                        
                        @if(isset($cotizacion->acta_entrega) && $cotizacion->acta_entrega->documento_firmado)
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
                                <i class="fas fa-save me-1"></i> {{ isset($cotizacion->acta_entrega) ? 'Actualizar Acta de Entrega' : 'Guardar Acta de Entrega' }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
    </div>
</div>