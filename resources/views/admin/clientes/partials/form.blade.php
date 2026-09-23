<div x-data="{ 
    darkMode: false, 
    tipoCliente: 'natural', 
    documento: '', 
    telefonos: [''], 
    celulares: [''], 
    categorias: JSON.parse('@json($categorias)'), 
    categoriaSeleccionada: '', 
    mostrarNuevaCategoria: false, 
    nuevaCategoria: '', 
    agregarNuevaCategoria() {
        if (!this.nuevaCategoria.trim()) {
            alert('Ingrese un nombre para la nueva categoría');
            return;
        }
        const nuevoID = Math.floor(Math.random() * 1000000);
        this.categorias.push({ id: nuevoID, nombre: this.nuevaCategoria });
        this.categoriaSeleccionada = nuevoID;
        this.mostrarNuevaCategoria = false;
        this.nuevaCategoria = '';
    }
}" x-init="
    tipoCliente = document.getElementById('tipo_cliente')?.value || 'natural';
    if (categorias.length > 0) {
        categoriaSeleccionada = categorias[0].id;
    }
">
    <div class="card border-0 shadow-sm" :class="darkMode ? 'bg-dark-subtle text-light' : ''">
        <div class="card-body p-4">
            <!-- Mostrar errores de validación -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Formulario principal -->
            <form method="POST" action="{{ route('admin.clientes.store') }}" class="needs-validation">
                @csrf
                <div class="row g-3">
                    <!-- Tipo de Cliente + Documento -->
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <select x-model="tipoCliente" name="tipo_cliente" id="tipo_cliente" class="form-select" 
                                    :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" required>
                                <option value="natural">Persona Natural (DNI)</option>
                                <option value="juridica">Persona Jurídica (RUC)</option>
                            </select>
                            <label for="tipo_cliente" class="text-muted">Tipo de Cliente</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating mb-3 d-flex align-items-center">
                            <input x-model="documento" 
                                   type="text" 
                                   name="documento_identidad" 
                                   id="documento_identidad" 
                                   class="form-control" 
                                   :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''"
                                   :placeholder="tipoCliente === 'natural' ? 'DNI' : 'RUC'" 
                                   :maxlength="tipoCliente === 'natural' ? 8 : 11" 
                                   required>
                            <label for="documento_identidad" class="text-muted">
                                <span x-text="tipoCliente === 'natural' ? 'Documento (DNI)' : 'Documento (RUC)'"></span>
                            </label>
                            <button type="button" class="btn btn-outline-primary ms-2" @click="validarDocumento">
                                Validar
                            </button>
                        </div>
                    </div>

                    <!-- Departamento, Provincia, Distrito -->
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input type="text" 
                                   name="departamento" 
                                   id="departamento" 
                                   class="form-control" 
                                   :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                   placeholder="Ingrese departamento" 
                                   required>
                            <label for="departamento" class="text-muted">Departamento</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input type="text" 
                                   name="provincia" 
                                   id="provincia" 
                                   class="form-control" 
                                   :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                   placeholder="Ingrese provincia" 
                                   required>
                            <label for="provincia" class="text-muted">Provincia</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input type="text" 
                                   name="distrito" 
                                   id="distrito" 
                                   class="form-control" 
                                   :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                   placeholder="Ingrese distrito" 
                                   required>
                            <label for="distrito" class="text-muted">Distrito</label>
                        </div>
                    </div>

                    <!-- Correo -->
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="email" 
                                   name="correo" 
                                   id="correo" 
                                   class="form-control" 
                                   :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                   placeholder="Ingrese correo">
                            <label for="correo" class="text-muted">Correo Electrónico</label>
                        </div>
                    </div>

                    <!-- Categoría de Cliente (con opción de nueva categoría) -->
                    <div class="col-md-6" x-data="{ mostrandoNueva: false, nuevaCat: '' }">
                        <div class="form-floating mb-3 position-relative">
                            <select name="categoria_cliente_id" 
                                    id="categoria_cliente_id" 
                                    class="form-select" 
                                    :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                    x-model="$parent.categoriaSeleccionada" 
                                    required>
                                <template x-for="cat in $parent.categorias" :key="cat.id">
                                    <option :value="cat.id" x-text="cat.nombre"></option>
                                </template>
                            </select>
                            <label for="categoria_cliente_id" class="text-muted">Categoría</label>
                            <button type="button" 
                                    class="btn btn-sm btn-outline-success position-absolute" 
                                    style="top: 0.75rem; right: 0.75rem;" 
                                    @click="mostrandoNueva = !mostrandoNueva">
                                Nueva
                            </button>
                        </div>
                        <div class="card border-0 p-2 mb-2" 
                            x-show="mostrandoNueva" 
                            x-transition 
                            style="background: #f8f9fa; border-radius: 0.5rem;">
                            <label class="form-label small mb-1">Nombre de nueva categoría</label>
                            <input type="text" 
                                  x-model="nuevaCat" 
                                  class="form-control mb-2" 
                                  :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                  placeholder="Ej. VIP, Frecuente, etc.">
                            <div class="text-end">
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger me-1" 
                                        @click="mostrandoNueva = false; nuevaCat='';">
                                    Cancelar
                                </button>
                                <button type="button" 
                                        class="btn btn-sm btn-primary" 
                                        @click="
                                            if(!nuevaCat.trim()){
                                                alert('Ingrese un nombre válido'); 
                                                return;
                                            }
                                            $parent.categoriaSeleccionada = 'nueva';
                                            mostrandoNueva = false;
                                        ">
                                    Guardar
                                </button>
                            </div>
                        </div>
                        <!-- Campo oculto para enviar el nombre de la nueva categoría -->
                        <input type="hidden" name="nueva_categoria" x-model="nuevaCat" x-show="$parent.categoriaSeleccionada === 'nueva'">
                    </div>

                    <!-- Canal de Captación -->
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <select name="canal_captacion_id" 
                                    id="canal_captacion_id" 
                                    class="form-select" 
                                    :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                    required>
                                @foreach ($canales as $canal)
                                    <option value="{{ $canal->id }}">{{ $canal->nombre }}</option>
                                @endforeach
                            </select>
                            <label for="canal_captacion_id" class="text-muted">Canal de Captación</label>
                        </div>
                    </div>

                    <!-- Información de contacto (teléfonos y celulares) -->
                    <div class="col-md-6">
                        <div class="card border-0 bg-light h-100" :class="darkMode ? 'bg-dark-subtle border-secondary' : ''">
                            <div class="card-body p-3">
                                <h6 class="fw-medium mb-3" :class="darkMode ? 'text-light' : ''">Información de Contacto</h6>
                                
                                <!-- Teléfonos -->
                                <div class="mb-3">
                                    <label class="form-label small text-muted">Teléfonos</label>
                                    <template x-for="(telefono, i) in telefonos" :key="'tel-'+i">
                                        <div class="input-group input-group-sm mb-2">
                                            <input type="text" 
                                                   name="telefonos[]" 
                                                   x-model="telefonos[i]" 
                                                   class="form-control" 
                                                   :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                                   placeholder="Número telefónico">
                                            <button type="button" 
                                                    @click="telefonos.splice(i, 1)" 
                                                    class="btn btn-outline-danger" 
                                                    :class="darkMode ? 'border-secondary' : ''">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash" viewBox="0 0 16 16">
                                                    <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                    <button type="button" 
                                            @click="telefonos.push('')" 
                                            class="btn btn-sm btn-outline-primary" 
                                            :class="darkMode ? 'border-secondary' : ''">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3H4a.5.5 0 0 1 0-1h3V4a.5.5 0 0 1 .5-.5z"/>
                                        </svg>
                                        Agregar Teléfono
                                    </button>
                                </div>
                                
                                <!-- Celulares -->
                                <div class="mb-3">
                                    <label class="form-label small text-muted">Celulares</label>
                                    <template x-for="(celular, i) in celulares" :key="'cel-'+i">
                                        <div class="input-group input-group-sm mb-2">
                                            <input type="text" 
                                                   name="celulares[]" 
                                                   x-model="celulares[i]" 
                                                   class="form-control" 
                                                   :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                                   placeholder="Número celular">
                                            <button type="button" 
                                                    @click="celulares.splice(i, 1)" 
                                                    class="btn btn-outline-danger" 
                                                    :class="darkMode ? 'border-secondary' : ''">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash" viewBox="0 0 16 16">
                                                    <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                    <button type="button" 
                                            @click="celulares.push('')" 
                                            class="btn btn-sm btn-outline-primary" 
                                            :class="darkMode ? 'border-secondary' : ''">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3H4a.5.5 0 0 1 0-1h3V4a.5.5 0 0 1 .5-.5z"/>
                                        </svg>
                                        Agregar Celular
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- Fin row g-3 -->

                <!-- Botones de acción -->
                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top" :class="darkMode ? 'border-secondary' : ''">
                    <a href="{{ route('admin.clientes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2 me-2" viewBox="0 0 16 16">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
                        </svg>
                        Guardar Cliente
                    </button>
                </div>
            </form>
            <!-- Fin del formulario -->
        </div>
    </div>
</div>