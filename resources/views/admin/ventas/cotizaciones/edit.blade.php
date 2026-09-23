<!-- resources/views/admin/ventas/cotizaciones/edit.blade.php -->
@extends('admin.layouts.app')

@section('title', 'Editar Cotización')

@section('header')
@endsection

@section('content')
<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-edit text-info me-2"></i> Editar Cotización
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6 d-flex align-items-center">Cotización #{{ $cotizacion->codigo }}</h2>
                <p class="text-white-50 mb-0">Actualización de datos y elementos cotizados.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.ventas.cotizaciones.index') }}" class="btn bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105" style="border: 1px solid rgba(255,255,255,0.8);">
                    <i class="fas fa-arrow-left me-2 text-primary"></i> Volver al Directorio
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-lg-4 position-relative" style="top: -3.5rem; z-index: 10;">
<div class="row justify-content-center">
    <div class="col-12 col-lg-10">
        <div class="card dashboard-card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.ventas.cotizaciones.update', $cotizacion) }}" id="form-cotizacion" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')
                    
                    <!-- Sección de Cliente -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted fw-semibold mb-3">Información del Cliente</h6>
                        </div>
                        
                        <div class="col-md-5">
                            <label for="documento_identidad" class="form-label small text-muted mb-1">RUC / DNI *</label>
                            <div class="input-group mb-3">
                                <input type="text" id="documento_identidad" class="form-control form-control-sm" value="{{ $cotizacion->cliente->documento_identidad }}" autocomplete="off" required>
                                <input type="hidden" name="cliente_id" id="cliente_id" value="{{ $cotizacion->cliente_id }}" required>
                                <button class="btn btn-sm btn-primary" type="button" id="buscar-cliente">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="col-md-7">
                            <label for="nombre_cliente" class="form-label small text-muted mb-1">Nombre / Razón Social</label>
                            <div class="input-group mb-3">
                                <input type="text" id="nombre_cliente" class="form-control form-control-sm" value="{{ $cotizacion->cliente->tipo_cliente === 'natural' ? $cotizacion->cliente->nombres . ' ' . $cotizacion->cliente->apellido_paterno . ' ' . $cotizacion->cliente->apellido_materno : $cotizacion->cliente->razon_social }}" readonly>
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#modalNuevoCliente">
                                    Nuevo Cliente
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sección de Datos Generales -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted fw-semibold mb-3">Datos Generales</h6>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="almacen_id" class="form-label small text-muted mb-1">Almacén Origen *</label>
                            <select name="almacen_id" id="almacen_id" class="form-select form-select-sm @error('almacen_id') is-invalid @enderror" required>
                                <option value="">Seleccione</option>
                                @foreach($almacenes as $almacen)
                                    <option value="{{ $almacen->id }}" {{ $cotizacion->almacen_id == $almacen->id ? 'selected' : '' }}>
                                        {{ $almacen->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('almacen_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                       </div>
                       
                       <div class="col-md-4">
                           <label class="form-label small text-muted mb-1">Condición *</label>
                           <div class="btn-group w-100" role="group">
                               <input type="radio" class="btn-check" name="condicion" id="condicion_nuevo" value="Nuevo" {{ $cotizacion->condicion === 'Nuevo' ? 'checked' : '' }}>
                               <label class="btn btn-outline-primary btn-sm" for="condicion_nuevo">Nuevo</label>
                               
                               <input type="radio" class="btn-check" name="condicion" id="condicion_usado" value="Usado" {{ $cotizacion->condicion === 'Usado' ? 'checked' : '' }}>
                               <label class="btn btn-outline-primary btn-sm" for="condicion_usado">Usado</label>
                           </div>
                       </div>
                       
                       <div class="col-md-4">
                           <label for="canal" class="form-label small text-muted mb-1">Canal *</label>
                           <select name="canal" id="canal" class="form-select form-select-sm @error('canal') is-invalid @enderror" required>
                               <option value="">Seleccione</option>
                               @foreach($canales as $canal)
                                   <option value="{{ $canal }}" {{ $cotizacion->canal == $canal ? 'selected' : '' }}>
                                       {{ $canal }}
                                   </option>
                               @endforeach
                           </select>
                           @error('canal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                       </div>
                       
                       <div class="col-md-4 mt-3">
                           <label class="form-label small text-muted mb-1">Moneda *</label>
                           <div class="btn-group w-100" role="group">
                               <input type="radio" class="btn-check" name="moneda" id="moneda_soles" value="Soles" {{ $cotizacion->moneda === 'Soles' ? 'checked' : '' }}>
                               <label class="btn btn-outline-primary btn-sm" for="moneda_soles">Soles</label>
                               
                               <input type="radio" class="btn-check" name="moneda" id="moneda_dolares" value="Dólares" {{ $cotizacion->moneda === 'Dólares' ? 'checked' : '' }}>
                               <label class="btn btn-outline-primary btn-sm" for="moneda_dolares">Dólares</label>
                           </div>
                       </div>
                       
                       <div class="col-md-4 mt-3">
                           <label class="form-label small text-muted mb-1">Forma de Pago *</label>
                           <div class="btn-group w-100" role="group">
                               <input type="radio" class="btn-check" name="forma_pago" id="forma_pago_contado" value="Contado" {{ $cotizacion->forma_pago === 'Contado' ? 'checked' : '' }}>
                               <label class="btn btn-outline-primary btn-sm" for="forma_pago_contado">Contado</label>
                               
                               <input type="radio" class="btn-check" name="forma_pago" id="forma_pago_credito" value="Crédito" {{ $cotizacion->forma_pago === 'Crédito' ? 'checked' : '' }}>
                               <label class="btn btn-outline-primary btn-sm" for="forma_pago_credito">Crédito</label>
                           </div>
                       </div>
                       
                       <div class="col-md-4 mt-3">
                           <label for="fecha_validez" class="form-label small text-muted mb-1">Fecha de Validez</label>
                           <input type="date" name="fecha_validez" id="fecha_validez" class="form-control form-control-sm" value="{{ $cotizacion->fecha_validez ? $cotizacion->fecha_validez->format('Y-m-d') : now()->addDays(30)->format('Y-m-d') }}">
                       </div>
                       
                       <div class="col-md-12 mt-3">
                           <label for="datos_adicionales" class="form-label small text-muted mb-1">Datos Adicionales</label>
                           <textarea name="datos_adicionales" id="datos_adicionales" class="form-control form-control-sm" rows="3">{{ $cotizacion->datos_adicionales }}</textarea>
                       </div>
                   </div>
                   
                   <!-- Sección de Vehículos -->
                   <div class="row mb-4">
                       <div class="col-12">
                           <h6 class="text-muted fw-semibold mb-3">Detalle de Vehículos</h6>
                       </div>
                       
                       <div class="col-md-5">
                           <label for="vehiculo_buscar" class="form-label small text-muted mb-1">Código de Vehículo</label>
                           <input type="text" id="vehiculo_buscar" class="form-control form-control-sm" placeholder="Buscar por marca, modelo, versión..." autocomplete="off">
                           <input type="hidden" id="vehiculo_id">
                       </div>
                       
                       <div class="col-md-3">
                           <label for="color_id" class="form-label small text-muted mb-1">Color</label>
                           <select id="color_id" class="form-select form-select-sm" disabled>
                               <option value="">Seleccione un vehículo primero</option>
                           </select>
                       </div>
                       
                       <div class="col-md-2">
                           <label for="precio_unitario" class="form-label small text-muted mb-1">Precio</label>
                           <input type="number" id="precio_unitario" class="form-control form-control-sm" step="0.01" min="0">
                       </div>
                       
                       <div class="col-md-2">
                           <label for="descuento" class="form-label small text-muted mb-1">Descuento %</label>
                           <input type="number" id="descuento" class="form-control form-control-sm" step="0.01" min="0" max="100" value="0">
                       </div>
                       
                       <div class="col-md-2 mt-3">
                           <label for="cantidad" class="form-label small text-muted mb-1">Cantidad</label>
                           <input type="number" id="cantidad" class="form-control form-control-sm" min="1" value="1">
                       </div>
                       
                       <div class="col-md-3 mt-3">
                           <label class="form-label small text-muted mb-1">Stock</label>
                           <div class="form-check">
                               <input class="form-check-input" type="checkbox" id="sin_stock">
                               <label class="form-check-label small" for="sin_stock">
                                   Sin stock (genera req. de compra)
                               </label>
                           </div>
                       </div>
                       
                       <div class="col-md-4 mt-3 align-self-end">
                           <button type="button" id="agregar-vehiculo" class="btn btn-primary btn-sm mt-4">
                               <i class="fas fa-plus me-1"></i> Agregar Vehículo
                           </button>
                       </div>
                   </div>
                   
                   <!-- Tabla de Vehículos Agregados -->
                   <div class="row mb-4">
                       <div class="col-12">
                           <div class="table-responsive">
                               <table class="table table-hover table-sm">
                                   <thead class="table-light">
                                       <tr>
                                           <th>Código</th>
                                           <th>Vehículo</th>
                                           <th>Color</th>
                                           <th>Cantidad</th>
                                           <th>Precio Venta</th>
                                           <th>Dcto %</th>
                                           <th>Total</th>
                                           <th></th>
                                       </tr>
                                   </thead>
                                   <tbody id="vehiculos-tabla">
                                       <!-- Se cargará dinámicamente con JS -->
                                   </tbody>
                                   <tfoot>
                                       <tr>
                                           <td colspan="6" class="text-end fw-bold">Subtotal:</td>
                                           <td class="fw-bold">
                                               <span id="subtotal-moneda">{{ $cotizacion->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}</span>
                                               <span id="subtotal-valor">{{ number_format($cotizacion->subtotal, 2) }}</span>
                                           </td>
                                           <td></td>
                                       </tr>
                                       <tr>
                                           <td colspan="6" class="text-end fw-bold">IGV (18%):</td>
                                           <td class="fw-bold">
                                               <span id="igv-moneda">{{ $cotizacion->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}</span>
                                               <span id="igv-valor">{{ number_format($cotizacion->impuestos, 2) }}</span>
                                           </td>
                                           <td></td>
                                       </tr>
                                       <tr>
                                           <td colspan="6" class="text-end fw-bold">Total:</td>
                                           <td class="fw-bold">
                                               <span id="total-moneda">{{ $cotizacion->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}</span>
                                               <span id="total-valor">{{ number_format($cotizacion->total, 2) }}</span>
                                           </td>
                                           <td></td>
                                       </tr>
                                   </tfoot>
                               </table>
                           </div>
                       </div>
                   </div>
                   
                   <!-- Botones de Acción -->
                   <div class="d-flex justify-content-end gap-2 mt-4">
                       <a href="{{ route('admin.ventas.cotizaciones.show', $cotizacion) }}" class="btn btn-outline-secondary btn-sm">
                           Cancelar
                       </a>
                       <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center">
                           <svg xmlns="http://www.w3.org/2000/svg" class="me-2" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                           </svg>
                           Actualizar Cotización
                       </button>
                   </div>
               </form>
           </div>
       </div>
   </div>
</div>

<!-- Modal para Nuevo Cliente -->
<div class="modal fade" id="modalNuevoCliente" tabindex="-1" aria-labelledby="modalNuevoClienteLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg">
       <div class="modal-content">
           <div class="modal-header">
               <h5 class="modal-title" id="modalNuevoClienteLabel">Registrar Nuevo Cliente</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">
               <!-- Incluir formulario de crear cliente -->
               <iframe id="iframe-nuevo-cliente" src="{{ route('admin.clientes.create') }}" style="width: 100%; height: 500px; border: none;"></iframe>
           </div>
       </div>
   </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<script>
   $(document).ready(function() {
       // Variables globales
       let vehiculosAgregados = [];
       let contadorVehiculos = 0;
       
       // Cargar vehículos existentes
       @foreach($cotizacion->detalles as $detalle)
           contadorVehiculos++;
           vehiculosAgregados.push({
               id: contadorVehiculos,
               vehiculo_catalogo_id: {{ $detalle->vehiculo_catalogo_id }},
               vehiculo_nombre: "{{ $detalle->vehiculo->marca->nombre }} {{ $detalle->vehiculo->modelo->nombre }} {{ $detalle->vehiculo->version->nombre }} {{ $detalle->vehiculo->anioModelo->nombre }}",
               color_id: {{ $detalle->color_id }},
               color_nombre: "{{ $detalle->color->nombre }}",
               precio_unitario: {{ $detalle->precio_unitario }},
               descuento: {{ $detalle->descuento }},
               cantidad: {{ $detalle->cantidad }},
               subtotal: {{ $detalle->subtotal }},
               total: {{ $detalle->total }},
               detalle_id: {{ $detalle->id }}
           });
       @endforeach
       
       // Actualizar tabla al cargar
       actualizarTablaVehiculos();
       
       // Configurar autocompletado para buscar clientes
       $("#documento_identidad").autocomplete({
           source: function(request, response) {
               $.ajax({
                   url: "{{ route('admin.ventas.cotizaciones.buscar-clientes') }}",
                   dataType: "json",
                   data: {
                       term: request.term
                   },
                   success: function(data) {
                       response(data);
                   }
               });
           },
           minLength: 2,
           select: function(event, ui) {
               $("#cliente_id").val(ui.item.id);
               
               // Formatear el nombre según tipo de cliente
               if (ui.item.tipo_cliente === 'natural') {
                   $("#nombre_cliente").val(ui.item.nombres + ' ' + ui.item.apellido_paterno + ' ' + ui.item.apellido_materno);
               } else {
                   $("#nombre_cliente").val(ui.item.razon_social);
               }
               
               return false;
           }
       }).autocomplete("instance")._renderItem = function(ul, item) {
           return $("<li>")
               .append("<div>" + item.label + "</div>")
               .appendTo(ul);
       };
       
       // Configurar autocompletado para buscar vehículos
       $("#vehiculo_buscar").autocomplete({
           source: function(request, response) {
               $.ajax({
                   url: "{{ route('admin.ventas.cotizaciones.buscar-vehiculos') }}",
                   dataType: "json",
                   data: {
                       term: request.term
                   },
                   success: function(data) {
                       response(data);
                   }
               });
           },
           minLength: 2,
           select: function(event, ui) {
               $("#vehiculo_id").val(ui.item.id);
               $("#vehiculo_buscar").val(ui.item.label);
               
               // Cargar colores disponibles para este vehículo
               cargarColores(ui.item.id);
               
               return false;
           }
       }).autocomplete("instance")._renderItem = function(ul, item) {
           return $("<li>")
               .append("<div>" + item.label + "</div>")
               .appendTo(ul);
       };
       
       // Función para cargar colores según el vehículo seleccionado
       function cargarColores(vehiculoId) {
           $.ajax({
               url: "/admin/ventas/cotizaciones/vehiculos/" + vehiculoId + "/colores",
               dataType: "json",
               success: function(data) {
                   const selectColor = $("#color_id");
                   selectColor.empty();
                   selectColor.append('<option value="">Seleccione color</option>');
                   
                   $.each(data, function(index, color) {
                       selectColor.append('<option value="' + color.id + '" data-color="' + color.hexadecimal + '">' + color.nombre + '</option>');
                   });
                   
                   selectColor.prop("disabled", false);
               }
           });
       }
       
       // Evento al cambiar la moneda
       $('input[name="moneda"]').change(function() {
           actualizarSimboloMoneda();
           recalcularTotales();
       });
       
       // Función para actualizar el símbolo de moneda en la tabla
       function actualizarSimboloMoneda() {
           const moneda = $('input[name="moneda"]:checked').val();
           const simbolo = moneda === 'Soles' ? 'S/ ' : 'US$ ';
           
           $('#subtotal-moneda').text(simbolo);
           $('#igv-moneda').text(simbolo);
           $('#total-moneda').text(simbolo);
           
           // Actualizar símbolos en filas existentes
           $('.precio-simbolo').text(simbolo);
           $('.total-simbolo').text(simbolo);
       }
       
       // Botón para agregar vehículo a la tabla
       $("#agregar-vehiculo").click(function() {
           const vehiculoId = $("#vehiculo_id").val();
           const vehiculoNombre = $("#vehiculo_buscar").val();
           const colorId = $("#color_id").val();
           const colorNombre = $("#color_id option:selected").text();
           const precioUnitario = parseFloat($("#precio_unitario").val()) || 0;
           const descuento = parseFloat($("#descuento").val()) || 0;
           const cantidad = parseInt($("#cantidad").val()) || 1;
           const sinStock = $("#sin_stock").prop("checked");
           
           // Validar campos
           if (!vehiculoId) {
               alert("Debe seleccionar un vehículo");
               return;
           }
           
           if (!colorId) {
               alert("Debe seleccionar un color");
               return;
           }
           
           if (precioUnitario <= 0) {
               alert("El precio debe ser mayor a cero");
               return;
           }
           
           // Calcular valores
           const subtotal = precioUnitario * cantidad;
           const totalConDescuento = subtotal * (1 - descuento / 100);
           
           // Añadir a la lista de vehículos
           contadorVehiculos++;
           const vehiculoObj = {
               id: contadorVehiculos,
               vehiculo_catalogo_id: vehiculoId,
               vehiculo_nombre: vehiculoNombre,
               color_id: colorId,
               color_nombre: colorNombre,
               precio_unitario: precioUnitario,
               descuento: descuento,
               cantidad: cantidad,
               subtotal: subtotal,
               total: totalConDescuento,
               sin_stock: sinStock
           };
           
           vehiculosAgregados.push(vehiculoObj);
           
           // Actualizar la tabla
           actualizarTablaVehiculos();
           
           // Limpiar campos
           $("#vehiculo_id").val("");
           $("#vehiculo_buscar").val("");
           $("#color_id").empty().append('<option value="">Seleccione un vehículo primero</option>').prop("disabled", true);
           $("#precio_unitario").val("");
           $("#descuento").val("0");
           $("#cantidad").val("1");
           $("#sin_stock").prop("checked", false);
       });
       
       // Función para actualizar la tabla de vehículos
       function actualizarTablaVehiculos() {
           const tabla = $("#vehiculos-tabla");
           const moneda = $('input[name="moneda"]:checked').val();
           const simbolo = moneda === 'Soles' ? 'S/ ' : 'US$ ';
           
           // Limpiar tabla
           tabla.empty();
           
           if (vehiculosAgregados.length === 0) {
               tabla.append('<tr id="no-vehiculos"><td colspan="8" class="text-center py-3">No se han agregado vehículos</td></tr>');
               return;
           }
           
           // Agregar filas
           $.each(vehiculosAgregados, function(index, vehiculo) {
               // Preparar los inputs para el envío del formulario
               let inputsHidden = `
                   <input type="hidden" name="vehiculos[${index}][vehiculo_catalogo_id]" value="${vehiculo.vehiculo_catalogo_id}">
                   <input type="hidden" name="vehiculos[${index}][color_id]" value="${vehiculo.color_id}">
                   <input type="hidden" name="vehiculos[${index}][precio_unitario]" value="${vehiculo.precio_unitario}">
                   <input type="hidden" name="vehiculos[${index}][descuento]" value="${vehiculo.descuento}">
                   <input type="hidden" name="vehiculos[${index}][cantidad]" value="${vehiculo.cantidad}">
               `;
               
               // Si tiene ID de detalle existente, incluirlo
               if (vehiculo.detalle_id) {
                   inputsHidden += `<input type="hidden" name="vehiculos[${index}][id]" value="${vehiculo.detalle_id}">`;
               }
               
               // Si es sin stock, incluir el indicador
               if (vehiculo.sin_stock) {
                   inputsHidden += `<input type="hidden" name="vehiculos[${index}][sin_stock]" value="1">`;
               }
               
               const fila = `
                   <tr data-id="${vehiculo.id}">
                       <td>${vehiculo.vehiculo_catalogo_id}</td>
                       <td>${vehiculo.vehiculo_nombre}</td>
                       <td>${vehiculo.color_nombre}</td>
                       <td>${vehiculo.cantidad}</td>
                       <td>
                           <span class="precio-simbolo">${simbolo}</span>
                           ${vehiculo.precio_unitario.toFixed(2)}
                       </td>
                       <td>${vehiculo.descuento.toFixed(2)}%</td>
                       <td>
                           <span class="total-simbolo">${simbolo}</span>
                           ${vehiculo.total.toFixed(2)}
                       </td>
                       <td>
                           <button type="button" class="btn btn-sm btn-outline-danger eliminar-vehiculo" data-id="${vehiculo.id}">
                               <i class="fas fa-times"></i>
                           </button>
                           ${inputsHidden}
                       </td>
                   </tr>
               `;
               tabla.append(fila);
           });
           
           // Recalcular totales
           recalcularTotales();
       }
       
       // Función para recalcular totales
       function recalcularTotales() {
           let subtotal = 0;
           
           vehiculosAgregados.forEach(function(vehiculo) {
               subtotal += vehiculo.total;
           });
           
           const igv = subtotal * 0.18;
           const total = subtotal + igv;
           
           $("#subtotal-valor").text(subtotal.toFixed(2));
           $("#igv-valor").text(igv.toFixed(2));
           $("#total-valor").text(total.toFixed(2));
       }
       
       // Evento para eliminar vehículo de la tabla
       $(document).on("click", ".eliminar-vehiculo", function() {
           const id = $(this).data("id");
           vehiculosAgregados = vehiculosAgregados.filter(function(vehiculo) {
               return vehiculo.id !== id;
           });
           
           actualizarTablaVehiculos();
       });
       
       // Validar formulario antes de enviar
       $("#form-cotizacion").submit(function(event) {
           if (vehiculosAgregados.length === 0) {
               alert("Debe agregar al menos un vehículo a la cotización");
               event.preventDefault();
               return false;
           }
           
           if (!$("#cliente_id").val()) {
               alert("Debe seleccionar un cliente");
               event.preventDefault();
               return false;
           }
           
           return true;
       });
       
       // Inicializar
       actualizarSimboloMoneda();
   });
</script>
@endpush