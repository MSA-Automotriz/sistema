<div class="col-md-6">
                <label for="persona_entrega" class="form-label">Persona que entrega</label>
                <input type="text" class="form-control" id="persona_entrega" name="persona_entrega" value="{{ auth()->user()->name }}" required>
            </div>
            
            <div class="col-md-12">
                <label for="vehiculo_detalle" class="form-label">Detalle del vehículo</label>
                <input type="text" class="form-control" id="vehiculo_detalle" name="vehiculo_detalle" 
                    value="{{ $cotizacion->detalles && $cotizacion->detalles->where('tipo', 'vehiculos')->first() ? $cotizacion->detalles->where('tipo', 'vehiculos')->first()->descripcion : '' }}" required>
            </div>
            
            <div class="col-md-6">
                <label for="placa" class="form-label">Número de placa</label>
                <input type="text" class="form-control" id="placa" name="placa">
            </div>
            
            <div class="col-md-6">
                <label for="kilometraje" class="form-label">Kilometraje</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="kilometraje" name="kilometraje" value="0" min="0" required>
                    <span class="input-group-text">Km</span>
                </div>
            </div>
            
            <div class="col-md-6">
                <label for="nivel_combustible" class="form-label">Nivel de combustible</label>
                <select class="form-select" id="nivel_combustible" name="nivel_combustible" required>
                    <option value="0">Vacío</option>
                    <option value="25">1/4</option>
                    <option value="50">1/2</option>
                    <option value="75">3/4</option>
                    <option value="100">Lleno</option>
                </select>
            </div>
            
            <div class="col-md-6">
                <label for="estado" class="form-label">Estado del acta</label>
                <select class="form-select" id="estado" name="estado" required>
                    <option value="En proceso">En proceso</option>
                    <option value="Completada">Completada</option>
                </select>
            </div>
            
            <!-- Aquí iría el resto del formulario con los checklist -->
            <!-- Mantén la misma estructura que en tu código original de paste-2.txt -->
            
            <div class="col-md-12">
                <label for="observaciones" class="form-label">Observaciones</label>
                <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
            </div>
            
            <div class="col-md-12">
                <label for="documento_firmado" class="form-label">Acta de entrega firmada (opcional)</label>
                <input type="file" class="form-control" id="documento_firmado" name="documento_firmado" accept=".pdf,.jpg,.jpeg,.png">
                <div class="form-text">Puede subir el acta firmada por el cliente en formato PDF o imagen.</div>
            </div>
            
            <div class="col-md-12">
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-1"></i> Guardar Acta de Entrega
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>