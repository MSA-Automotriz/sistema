<div class="componente-item" id="componente_{INDEX}">
    <div class="componente-header">
        <h5 class="componente-nombre">Componente {INDEX_DISPLAY}</h5>
        <button type="button" class="btn btn-danger btn-sm" onclick="eliminarComponente({INDEX})">
            <i class="fas fa-trash"></i>
        </button>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="required">Componente/Parte</label>
                <select name="componentes[{INDEX}][parte_id]" id="componente_{INDEX}_parte_id" 
                        class="form-control" required onchange="actualizarComponenteNombre({INDEX})">
                    <option value="">Seleccionar componente</option>
                    @foreach($partes as $parte)
                        <option value="{{ $parte->id }}">
                            {{ $parte->nombre }} 
                            @if($parte->marca)
                                - {{ $parte->marca }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label class="required">Cantidad</label>
                <input type="number" name="componentes[{INDEX}][cantidad]" 
                       class="form-control" min="0.01" step="0.01" value="1" required>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label class="required">Unidad</label>
                <select name="componentes[{INDEX}][unidad_medida]" class="form-control" required>
                    <option value="Unidades">Unidades</option>
                    <option value="Litros">Litros</option>
                    <option value="Galones">Galones</option>
                    <option value="Lb">Libras</option>
                    <option value="Kg">Kilogramos</option>
                    <option value="Metros">Metros</option>
                    <option value="Pies">Pies</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label class="required">Acción</label>
                <select name="componentes[{INDEX}][accion]" class="form-control" required>
                    <option value="Reemplazar">Reemplazar (R)</option>
                    <option value="Inspeccionar">Inspeccionar (I)</option>
                    <option value="Lubricar">Lubricar (L)</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label class="required">Moneda</label>
                <select name="componentes[{INDEX}][moneda]" class="form-control" required>
                    <option value="PEN">Soles (PEN)</option>
                    <option value="USD">Dólares (USD)</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Proveedor</label>
                <select name="componentes[{INDEX}][proveedor_id]" id="componente_{INDEX}_proveedor_id" 
                        class="form-control">
                    <option value="">Usar proveedor predeterminado</option>
                    @foreach($proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}">
                            {{ $proveedor->nombre_completo }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Precio Base</label>
                <input type="number" name="componentes[{INDEX}][precio_base]" 
                       class="form-control" min="0" step="0.01" placeholder="0.00">
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label>Observaciones</label>
                <textarea name="componentes[{INDEX}][observaciones]" class="form-control" rows="2" 
                          placeholder="Notas adicionales sobre este componente"></textarea>
            </div>
        </div>
    </div>
</div>