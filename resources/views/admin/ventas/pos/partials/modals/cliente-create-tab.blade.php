{{-- resources/views/admin/ventas/pos/partials/modals/cliente-create-tab.blade.php --}}
<div class="tab-pane fade" id="create-cliente" role="tabpanel" aria-labelledby="create-tab">
    <form id="form-nuevo-cliente" novalidate>
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">
                    <i class="fas fa-user-tag me-1"></i>Tipo de Cliente
                </label>
                <div class="btn-group w-100" role="group" aria-label="Tipo de cliente">
                    <input type="radio" class="btn-check" name="tipo_cliente" id="tipo-natural" value="natural" checked>
                    <label class="btn btn-outline-primary" for="tipo-natural">
                        <i class="fas fa-user me-1"></i>Persona Natural
                    </label>
                    
                    <input type="radio" class="btn-check" name="tipo_cliente" id="tipo-juridico" value="juridico">
                    <label class="btn btn-outline-primary" for="tipo-juridico">
                        <i class="fas fa-building me-1"></i>Empresa
                    </label>
                </div>
            </div>
            <div class="col-md-8">
                <label for="documento_identidad" class="form-label fw-bold">
                    <i class="fas fa-id-card me-1"></i>Documento de Identidad *
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="fas fa-id-card text-primary"></i>
                    </span>
                    <input type="text" class="form-control" id="documento_identidad" 
                           name="documento_identidad" required 
                           placeholder="DNI, RUC, Carnet de Extranjería..."
                           maxlength="20">
                    <div class="invalid-feedback">
                        Este campo es requerido
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Datos para persona natural -->
        <div id="datos-natural">
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="nombres" class="form-label fw-bold">
                        <i class="fas fa-user me-1"></i>Nombres *
                    </label>
                    <input type="text" class="form-control" id="nombres" name="nombres" 
                           required placeholder="Nombres completos" maxlength="100">
                    <div class="invalid-feedback">
                        Los nombres son requeridos
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="apellido_paterno" class="form-label fw-bold">
                        Apellido Paterno *
                    </label>
                    <input type="text" class="form-control" id="apellido_paterno" 
                           name="apellido_paterno" required maxlength="50">
                    <div class="invalid-feedback">
                        El apellido paterno es requerido
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="apellido_materno" class="form-label fw-bold">
                        Apellido Materno
                    </label>
                    <input type="text" class="form-control" id="apellido_materno" 
                           name="apellido_materno" maxlength="50">
                </div>
            </div>
        </div>
        
        <!-- Datos para persona jurídica -->
        <div id="datos-juridico" class="d-none">
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="razon_social" class="form-label fw-bold">
                        <i class="fas fa-building me-1"></i>Razón Social *
                    </label>
                    <input type="text" class="form-control" id="razon_social" 
                           name="razon_social" placeholder="Razón social completa" maxlength="200">
                    <div class="invalid-feedback">
                        La razón social es requerida
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Datos de contacto -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="telefono" class="form-label fw-bold">
                    <i class="fas fa-phone me-1"></i>Teléfono
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="fas fa-phone text-primary"></i>
                    </span>
                    <input type="tel" class="form-control" id="telefono" 
                           name="telefono" placeholder="999 999 999" maxlength="15">
                </div>
            </div>
            <div class="col-md-6">
                <label for="correo" class="form-label fw-bold">
                    <i class="fas fa-envelope me-1"></i>Correo Electrónico
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="fas fa-envelope text-primary"></i>
                    </span>
                    <input type="email" class="form-control" id="correo" 
                           name="correo" placeholder="correo@ejemplo.com" maxlength="100">
                </div>
            </div>
        </div>
        
        <!-- Dirección opcional -->
        <div class="row mb-3">
            <div class="col-md-12">
                <label for="direccion" class="form-label fw-bold">
                    <i class="fas fa-map-marker-alt me-1"></i>Dirección
                </label>
                <textarea class="form-control" id="direccion" name="direccion" 
                          rows="2" placeholder="Dirección completa (opcional)" maxlength="250"></textarea>
            </div>
        </div>
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Información:</strong> Los campos marcados con (*) son obligatorios. 
            Una vez creado, el cliente será seleccionado automáticamente para la venta.
        </div>
        
        <div class="d-grid">
            <button type="submit" class="btn btn-success btn-lg">
                <i class="fas fa-save me-2"></i>Crear y Seleccionar Cliente
            </button>
        </div>
    </form>
</div>