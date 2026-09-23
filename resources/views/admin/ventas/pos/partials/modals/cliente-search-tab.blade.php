{{-- resources/views/admin/ventas/pos/partials/modals/cliente-search-tab.blade.php --}}
<div class="tab-pane fade show active" id="search-cliente" role="tabpanel" aria-labelledby="search-tab">
    <div class="mb-3">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0">
                <i class="fas fa-search text-primary"></i>
            </span>
            <input type="text" class="form-control border-start-0 border-end-0" id="cliente-search" 
                   placeholder="Buscar por nombre, documento, correo...">
            <button class="btn btn-primary" type="button" id="btn-search-cliente">
                <i class="fas fa-search me-1"></i>Buscar
            </button>
        </div>
        <small class="text-muted">
            <i class="fas fa-info-circle me-1"></i>
            Escriba al menos 2 caracteres para buscar o deje vacío para ver clientes recientes
        </small>
    </div>
    
    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
        <table class="table table-hover table-sm mb-0">
            <thead class="table-light sticky-top">
                <tr>
                    <th style="width: 20%;">Documento</th>
                    <th style="width: 30%;">Cliente</th>
                    <th style="width: 15%;">Tipo</th>
                    <th style="width: 25%;">Contacto</th>
                    <th style="width: 10%;">Acción</th>
                </tr>
            </thead>
            <tbody id="cliente-results">
                <!-- Los resultados se cargarán aquí -->
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        <i class="fas fa-search fa-2x mb-2 opacity-50"></i>
                        <br>Cargando clientes recientes...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="mt-3 text-center">
        <small class="text-muted">
            ¿No encuentra el cliente? 
            <a href="#" class="text-primary" onclick="$('#create-tab').click()">Crear nuevo cliente</a>
        </small>
    </div>
</div>