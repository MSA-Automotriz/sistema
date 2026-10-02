<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Ventas\CotizacionController;
use App\Http\Controllers\Admin\Ventas\OportunidadController;
use App\Http\Controllers\Admin\Configuracion\Maestros\FabricanteController;
use App\Http\Controllers\Admin\Configuracion\Maestros\EstandarMantenimientoController;
use App\Http\Controllers\Admin\Configuracion\Maestros\CargoController;
use App\Http\Controllers\Admin\Almacenes\Categorias\ClasificacionVehiculoController;
use App\Http\Controllers\Admin\Almacenes\Categorias\CategoriasPartesController;
use App\Http\Controllers\Admin\Configuracion\Categorias\CategoriasServiciosController;
use App\Http\Controllers\Admin\Configuracion\Maestros\BancoController;
use App\Http\Controllers\Admin\Configuracion\CentroCostoController;
use App\Http\Controllers\Admin\Mantenimiento\VehiculoMantenimientoController;
use App\Http\Controllers\Admin\Mantenimiento\CitaMantenimientoController;
use App\Http\Controllers\Admin\Mantenimiento\OrdenTrabajoMantenimientoController;
use App\Http\Controllers\Admin\Mantenimiento\FacturaController;
use App\Http\Controllers\Admin\Almacenes\VehiculoController;
use App\Http\Controllers\Admin\Almacenes\AlmacenController;
use App\Http\Controllers\Admin\Almacenes\ParteController;
use App\Http\Controllers\Admin\Configuracion\UnidadController;
use App\Http\Controllers\Admin\Almacenes\ServicioController;
use App\Http\Controllers\Admin\Almacenes\MarcaController;
use App\Http\Controllers\Admin\Almacenes\ModeloController;
use App\Http\Controllers\Admin\Almacenes\VersionController;
use App\Http\Controllers\Admin\Almacenes\AnioModeloController;
use App\Http\Controllers\Admin\Almacenes\ColorController;
use App\Http\Controllers\Admin\Compras\ProveedorController;
use App\Http\Controllers\Admin\Compras\RequerimientoCompraController;
use App\Http\Controllers\Admin\Compras\OrdenCompraController;
use App\Http\Controllers\Admin\Compras\CategoriaProveedorController;
use App\Http\Controllers\Admin\Compras\RecepcionController;
use App\Http\Controllers\Admin\Compras\GuiaEntregaController;
use App\Http\Controllers\Admin\Compras\DevolucionController;
use App\Http\Controllers\Admin\Clientes\ClienteController;
use App\Http\Controllers\Admin\Clientes\CategoriaClienteController;
use App\Http\Controllers\Admin\Inventario\TrasladoController;
use App\Http\Controllers\Admin\Inventario\ReporteController as InventarioReporteController;
use App\Http\Controllers\Admin\Inventario\DevolucionProveedorController;
use App\Http\Controllers\Admin\Inventario\KardexController;
use App\Http\Controllers\Admin\Reportes\ReporteController;
use App\Http\Controllers\Admin\Usuarios\UsuarioController;
use App\Http\Controllers\Admin\Usuarios\RolController;
use App\Http\Controllers\Admin\Almacenes\InventarioController;
use App\Http\Controllers\Admin\Almacenes\MovimientoController;
use App\Http\Controllers\Admin\Talleres\TallerController;
use App\Http\Controllers\Admin\Establecimientos\EstablecimientoController;
use App\Http\Controllers\Admin\ventas\NotaPedidoController;
use App\Http\Controllers\Admin\Ventas\DocumentoSunarpController;
use App\Http\Controllers\Admin\Ventas\PlacaController;
use App\Http\Controllers\Admin\Ventas\DocumentoController;
use App\Http\Controllers\Admin\Ventas\PagoController;
use App\Http\Controllers\Admin\Ventas\ComprobanteController;
use App\Http\Controllers\Admin\Ventas\ActaEntregaController;
use App\Http\Controllers\Admin\Ventas\EstadoCotizacionController;
use App\Http\Controllers\Admin\Ventas\CotizacionOrdenTrabajoController;
use App\Http\Controllers\Admin\Ventas\POSController;
use App\Http\Controllers\Admin\Ventas\PagoVentaController;
use App\Http\Controllers\Admin\TipoCambioController;
use App\Http\Controllers\Admin\PlanMantenimientoController;
use App\Http\Middleware\CheckAdminAccess;


### Rutas públicas
Route::get('/', function () {
    return redirect('/admin/dashboard');
});

Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);

// Rutas públicas de seguimiento
Route::get('/tracking', [App\Http\Controllers\TrackingController::class, 'index'])->name('tracking.index');
Route::post('/tracking/buscar', [App\Http\Controllers\TrackingController::class, 'buscarVenta'])->name('tracking.buscar');

### Rutas protegidas por autenticación y acceso al admin
Route::middleware(['auth', CheckAdminAccess::class])->group(function () {
    # Dashboard, Perfil y Logout
    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard');
    })->name('dashboard');
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard')->middleware('permission:dashboard');
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('user.profile');
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
    Route::get('/admin/refresh-csrf-token', function() {
        return response()->json(['token' => csrf_token()]);
    })->name('admin.refresh-csrf-token');

    # Módulo Ventas - Cotizaciones
    Route::prefix('admin/ventas/cotizaciones')->name('admin.ventas.cotizaciones.')->middleware('permission:ventas')->group(function () {
        // Rutas existentes
        Route::get('/', [CotizacionController::class, 'index'])->name('index');
        Route::get('/create', [CotizacionController::class, 'create'])->name('create');
        Route::post('/', [CotizacionController::class, 'store'])->name('store')->middleware('validar.cotizacion.cliente');
        Route::get('/{cotizacion}', [CotizacionController::class, 'show'])->name('show');
        Route::get('/{cotizacion}/pdf', [CotizacionController::class, 'generarPDF'])->name('pdf');
        Route::get('/{cotizacion}/edit', [CotizacionController::class, 'edit'])->name('edit');
        Route::put('/{cotizacion}', [CotizacionController::class, 'update'])->name('update');
        Route::delete('/{cotizacion}', [CotizacionController::class, 'destroy'])->name('destroy');
        Route::post('/{cotizacion}/cambiar-estado', [EstadoCotizacionController::class, 'cambiarEstado'])
            ->name('cambiar-estado');
        Route::get('/buscar-servicios', [CotizacionController::class, 'buscarServicios'])->name('buscarServicios');
        Route::get('/buscar-clientes', [CotizacionController::class, 'buscarClientes'])->name('buscar-clientes');
        Route::get('/buscar-vehiculos', [CotizacionController::class, 'buscarVehiculos'])->name('buscar-vehiculos');
        // Ruta existente para gestión
        Route::get('/{cotizacion}/gestionar', [CotizacionController::class, 'gestionar'])->name('gestionar');
        Route::post('/{cotizacion}/seguimiento', [App\Http\Controllers\Admin\Ventas\SeguimientoCotizacionController::class, 'agregar'])->name('seguimiento.agregar');
        Route::get('/{cotizacion}/seguimientos', [CotizacionController::class, 'getSeguimientos'])->name('seguimientos');
        Route::post('/{cotizacion}/actualizar-gestion', [CotizacionController::class, 'actualizarGestion'])->name('actualizar-gestion');

        // Rutas para el toggle de realizado
        Route::post('/seguimientos/{seguimiento}/toggle-realizado', 
            [App\Http\Controllers\Admin\Ventas\SeguimientoCotizacionController::class, 'toggleRealizado'])
            ->name('seguimientos.toggle-realizado');

        // Rutas para comentarios de seguimiento
        Route::get('/seguimientos/{seguimiento}/comentarios', 
            [App\Http\Controllers\Admin\Ventas\ComentarioSeguimientoController::class, 'index'])
            ->name('seguimientos.comentarios.index');

        Route::post('/seguimientos/{seguimiento}/comentarios', 
            [App\Http\Controllers\Admin\Ventas\ComentarioSeguimientoController::class, 'store'])
            ->name('seguimientos.comentarios.store');

        Route::put('/seguimientos/{seguimiento}/comentarios/{comentario}', 
            [App\Http\Controllers\Admin\Ventas\ComentarioSeguimientoController::class, 'update'])
            ->name('seguimientos.comentarios.update');

        Route::delete('/seguimientos/{seguimiento}/comentarios/{comentario}', 
            [App\Http\Controllers\Admin\Ventas\ComentarioSeguimientoController::class, 'destroy'])
            ->name('seguimientos.comentarios.destroy');
        
        // Nuevas rutas para las secciones
        Route::get('/{cotizacion}/pagos', [PagoController::class, 'index'])->name('pagos');
        Route::get('/{cotizacion}/comprobantes', [ComprobanteController::class, 'index'])->name('comprobantes');
        Route::get('/{cotizacion}/nota-pedido', [NotaPedidoController::class, 'index'])->name('nota-pedido');
        Route::get('/{cotizacion}/orden-trabajo', [CotizacionOrdenTrabajoController::class, 'index'])->name('orden-trabajo');
        Route::get('/{cotizacion}/acta-entrega', [ActaEntregaController::class, 'index'])->name('acta-entrega');
        Route::get('/{cotizacion}/sunarp', [DocumentoSunarpController::class, 'index'])->name('sunarp');
        Route::get('/{cotizacion}/placas', [PlacaController::class, 'index'])->name('placas');
        Route::get('/{cotizacion}/documentos', [DocumentoController::class, 'index'])->name('documentos');
        
        // Rutas existentes para gestión de pagos
        Route::post('/{cotizacion}/pagos', [PagoController::class, 'store'])->name('pagos.store');
        Route::put('/{cotizacion}/pagos/{pago}', [PagoController::class, 'update'])->name('pagos.update');
        Route::delete('/{cotizacion}/pagos/{pago}', [PagoController::class, 'destroy'])->name('pagos.destroy');
        
        // Rutas existentes para gestión de comprobantes
        Route::post('/{cotizacion}/comprobantes', [ComprobanteController::class, 'store'])->name('comprobantes.store');
        Route::put('/{cotizacion}/comprobantes/{comprobante}', [ComprobanteController::class, 'update'])->name('comprobantes.update');
        Route::delete('/{cotizacion}/comprobantes/{comprobante}', [ComprobanteController::class, 'destroy'])->name('comprobantes.destroy');
        
        // Rutas existentes para gestión de nota de pedido
        Route::post('/{cotizacion}/nota-pedido/items', [NotaPedidoController::class, 'storeItem'])->name('nota-pedido.store-item');
        Route::put('/{cotizacion}/nota-pedido/items/{item}', [NotaPedidoController::class, 'updateItem'])->name('nota-pedido.update-item');
        Route::delete('/{cotizacion}/nota-pedido/items/{item}', [NotaPedidoController::class, 'destroyItem'])->name('nota-pedido.destroy-item');
        Route::put('/{cotizacion}/nota-pedido/observaciones', [NotaPedidoController::class, 'updateObservaciones'])->name('nota-pedido.update-observaciones');
        Route::get('/{cotizacion}/nota-pedido/pdf', [NotaPedidoController::class, 'generarPDF'])->name('nota-pedido.pdf');

        // Rutas existentes para gestión de órdenes de trabajo
        Route::get('/{cotizacion}/orden-trabajo/crear', [CotizacionOrdenTrabajoController::class, 'create'])
            ->name('admin.ventas.cotizaciones.orden-trabajo.crear');
        Route::get('/admin/ventas/cotizaciones/{cotizacion}/orden-trabajo/{orden}/edit', 
            [CotizacionOrdenTrabajoController::class, 'edit'])
            ->name('admin.ventas.cotizaciones.orden-trabajo.edit');
        Route::post('/{cotizacion}/orden-trabajo', [CotizacionOrdenTrabajoController::class, 'store'])->name('orden-trabajo.store');
        Route::put('/{cotizacion}/orden-trabajo/{orden}', [CotizacionOrdenTrabajoController::class, 'update'])->name('orden-trabajo.update');
        
        // Rutas existentes para gestión de acta de entrega
        Route::post('/{cotizacion}/acta-entrega', [ActaEntregaController::class, 'store'])->name('acta-entrega.store');
        Route::put('/{cotizacion}/acta-entrega', [ActaEntregaController::class, 'update'])->name('acta-entrega.update');
        Route::get('/{cotizacion}/acta-entrega/pdf', [ActaEntregaController::class, 'generarPDF'])->name('acta-entrega.pdf');
        
        // Rutas existentes para gestión de documentos SUNARP
        Route::post('/{cotizacion}/sunarp', [DocumentoSunarpController::class, 'store'])->name('sunarp.store');
        Route::put('/{cotizacion}/sunarp/{documento}', [DocumentoSunarpController::class, 'update'])->name('sunarp.update');
        Route::delete('/{cotizacion}/sunarp/{documento}', [DocumentoSunarpController::class, 'destroy'])->name('sunarp.destroy');
        
       // Placas
        Route::prefix('{cotizacion}/placas')->name('placas.')->group(function() {
            Route::get('/', [PlacaController::class, 'index'])->name('index');
            Route::post('/', [PlacaController::class, 'storePlaca'])->name('store');
            Route::put('/{placa}', [PlacaController::class, 'updatePlaca'])->name('update');
            Route::delete('/{placa}', [PlacaController::class, 'destroyPlaca'])->name('destroy');
            
            // Gestión de documentos por placa
            Route::post('/{placa}/documentos', [PlacaController::class, 'storeDocumento'])->name('documentos.store');
            Route::put('/{placa}/documentos/{documento}', [PlacaController::class, 'updateDocumento'])->name('documentos.update');
            Route::delete('/{placa}/documentos/{documento}', [PlacaController::class, 'destroyDocumento'])->name('documentos.destroy');
            
            // Gestión de comentarios por placa
            Route::post('/{placa}/comentarios', [PlacaController::class, 'storeComentario'])->name('comentarios.store');
            Route::delete('/{placa}/comentarios/{comentario}', [PlacaController::class, 'destroyComentario'])->name('comentarios.destroy');
        });
        
        // Rutas existentes para gestión de documentos adicionales
        Route::post('/{cotizacion}/documentos', [DocumentoController::class, 'store'])->name('documentos.store');
        Route::put('/{cotizacion}/documentos/{documento}', [DocumentoController::class, 'update'])->name('documentos.update');
        Route::delete('/{cotizacion}/documentos/{documento}', [DocumentoController::class, 'destroy'])->name('documentos.destroy');
        
        //generar requerimiento desde compra
        Route::post('/{cotizacion}/generar-requerimiento', [CotizacionController::class, 'generarRequerimiento'])
            ->name('generar-requerimiento');
    });

    # Módulo Ventas - Dashboard/Vista General (resources\views\admin\ventas\index.blade.php)
    Route::prefix('admin/ventas')->name('admin.ventas.')->middleware('permission:ventas')->group(function () {
        // Dashboard principal de ventas
        Route::get('/', [POSController::class, 'ventas'])->name('index');
        
        // Rutas para gestión de pagos de ventas
        Route::prefix('{venta}/pagos')->name('pagos.')->group(function () {
            Route::get('/', [PagoVentaController::class, 'index'])->name('index');
            Route::post('/', [PagoVentaController::class, 'store'])->name('store');
            Route::put('{pago}/validar', [PagoVentaController::class, 'validar'])->name('validate');
            Route::delete('{pago}', [PagoVentaController::class, 'destroy'])->name('destroy');
        });
        
        // Ruta para cuentas por cobrar
        Route::get('/cuentas-por-cobrar', [PagoVentaController::class, 'cuentasPorCobrar'])->name('cuentas-por-cobrar');
    });

    # Módulo Ventas - Oportunidades
    Route::prefix('admin/ventas/oportunidades')->name('admin.ventas.oportunidades.')->middleware('permission:ventas')->group(function () {
        Route::get('/', [OportunidadController::class, 'index'])->name('index');
        Route::get('/create', [OportunidadController::class, 'create'])->name('create');
        Route::post('/', [OportunidadController::class, 'store'])->name('store');
        Route::get('/{oportunidad}', [OportunidadController::class, 'show'])->name('show');
        Route::get('/{oportunidad}/edit', [OportunidadController::class, 'edit'])->name('edit');
        Route::put('/{oportunidad}', [OportunidadController::class, 'update'])->name('update');
        Route::delete('/{oportunidad}', [OportunidadController::class, 'destroy'])->name('destroy');
        Route::post('/{oportunidad}/seguimiento', [OportunidadController::class, 'agregarSeguimiento'])->name('seguimiento');
        Route::get('/{oportunidad}/seguimientos', [OportunidadController::class, 'getSeguimientos'])->name('seguimientos');
    });

    # Módulo Clientes
    Route::prefix('admin/clientes')->name('admin.clientes.')->middleware('permission:clientes')->group(function () {
        Route::post('/validar-documento', [ClienteController::class, 'validarDocumento'])->name('validar-documento');
        Route::get('/provincias', [ClienteController::class, 'getProvinciasAjax'])->name('provincias');
        Route::get('/distritos', [ClienteController::class, 'getDistritosAjax'])->name('distritos');
        Route::get('/categorias', [CategoriaClienteController::class, 'index'])->name('categorias.index');
        Route::get('/categorias/create', [CategoriaClienteController::class, 'create'])->name('categorias.create');
        Route::post('/categorias', [CategoriaClienteController::class, 'store'])->name('categorias.store');
        Route::get('/categorias/{categoriaCliente}/edit', [CategoriaClienteController::class, 'edit'])->name('categorias.edit');
        Route::put('/categorias/{categoriaCliente}', [CategoriaClienteController::class, 'update'])->name('categorias.update');
        Route::delete('/categorias/{categoriaCliente}', [CategoriaClienteController::class, 'destroy'])->name('categorias.destroy');
        Route::get('/', [ClienteController::class, 'index'])->name('index');
        Route::get('/create', [ClienteController::class, 'create'])->name('create');
        Route::post('/', [ClienteController::class, 'store'])->name('store');
        Route::get('/{cliente}', [ClienteController::class, 'show'])->name('show');
        Route::get('/{cliente}/edit', [ClienteController::class, 'edit'])->name('edit');
        Route::put('/{cliente}', [ClienteController::class, 'update'])->name('update');
        Route::delete('/{cliente}', [ClienteController::class, 'destroy'])->name('destroy');
    });

    # Módulo Configuración - Maestros
    Route::prefix('admin/configuracion/maestros')->name('admin.configuracion.maestros.')->middleware('permission:configuracion')->group(function () {
        Route::prefix('fabricantes')->name('fabricantes.')->group(function () {
            Route::get('/', [FabricanteController::class, 'index'])->name('index');
            Route::get('/create', [FabricanteController::class, 'create'])->name('create');
            Route::post('/', [FabricanteController::class, 'store'])->name('store');
            Route::get('/{fabricante}/edit', [FabricanteController::class, 'edit'])->name('edit');
            Route::put('/{fabricante}', [FabricanteController::class, 'update'])->name('update');
            Route::delete('/{fabricante}', [FabricanteController::class, 'destroy'])->name('destroy');
        });
        Route::prefix('estandar_mantenimiento')->name('estandar_mantenimiento.')->group(function () {
            Route::get('/', [EstandarMantenimientoController::class, 'index'])->name('index');
            Route::get('/create', [EstandarMantenimientoController::class, 'create'])->name('create');
            Route::post('/', [EstandarMantenimientoController::class, 'store'])->name('store');
            Route::get('/{estandarMantenimiento}/edit', [EstandarMantenimientoController::class, 'edit'])->name('edit');
            Route::put('/{estandarMantenimiento}', [EstandarMantenimientoController::class, 'update'])->name('update');
            Route::delete('/{estandarMantenimiento}', [EstandarMantenimientoController::class, 'destroy'])->name('destroy');
        });
        Route::prefix('cargos')->name('cargos.')->group(function () {
            Route::get('/', [CargoController::class, 'index'])->name('index');
            Route::get('/create', [CargoController::class, 'create'])->name('create');
            Route::post('/', [CargoController::class, 'store'])->name('store');
            Route::get('/{cargo}/edit', [CargoController::class, 'edit'])->name('edit');
            Route::put('/{cargo}', [CargoController::class, 'update'])->name('update');
            Route::delete('/{cargo}', [CargoController::class, 'destroy'])->name('destroy');
        });
        Route::prefix('bancos')->name('bancos.')->group(function () {
            Route::get('/', [BancoController::class, 'index'])->name('index');
            Route::get('/create', [BancoController::class, 'create'])->name('create');
            Route::post('/', [BancoController::class, 'store'])->name('store');
            Route::get('/{banco}/edit', [BancoController::class, 'edit'])->name('edit');
            Route::put('/{banco}', [BancoController::class, 'update'])->name('update');
            Route::delete('/{banco}', [BancoController::class, 'destroy'])->name('destroy');
        });
    });

    # Módulo Partes - Categorías Partes
    Route::prefix('admin/almacenes/partes/categorias')->name('admin.almacenes.partes.categorias.')->middleware('permission:almacenes')->group(function () {
        Route::get('/', [CategoriasPartesController::class, 'index'])->name('index');
        Route::get('/create', [CategoriasPartesController::class, 'create'])->name('create');
        Route::post('/', [CategoriasPartesController::class, 'store'])->name('store');
        Route::get('/{categoriaParte}/edit', [CategoriasPartesController::class, 'edit'])->name('edit');
        Route::put('/{categoriaParte}', [CategoriasPartesController::class, 'update'])->name('update');
        Route::delete('/{categoriaParte}', [CategoriasPartesController::class, 'destroy'])->name('destroy');
    });

    # Módulo Almacenes - Categorías de Servicios Tercerizados
    Route::prefix('admin/productos-servicios/servicios')->name('admin.productos-servicios.servicios.')->group(function () {
        // Route::get('/', [ServiciosController::class, 'index'])->name('index');
        // Route::get('/create', [ServiciosController::class, 'create'])->name('create');
        // Route::post('/', [ServiciosController::class, 'store'])->name('store');
        // Route::get('/{servicioTercerizado}/edit', [ServiciosController::class, 'edit'])->name('edit');
        // Route::put('/{servicioTercerizado}', [ServiciosController::class, 'update'])->name('update');
        // Route::delete('/{servicioTercerizado}', [ServiciosController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('/admin/productos-servicios/servicios/categorias')->name('admin.productos-servicios.servicios.categorias.')->middleware('permission:almacenes')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\Almacenes\Categorias\CategoriasServiciosController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\Almacenes\Categorias\CategoriasServiciosController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\Almacenes\Categorias\CategoriasServiciosController::class, 'store'])->name('store');
        Route::get('/{categoria}/edit', [App\Http\Controllers\Admin\Almacenes\Categorias\CategoriasServiciosController::class, 'edit'])->name('edit');
        Route::put('/{categoria}', [App\Http\Controllers\Admin\Almacenes\Categorias\CategoriasServiciosController::class, 'update'])->name('update');
        Route::delete('/{categoria}', [App\Http\Controllers\Admin\Almacenes\Categorias\CategoriasServiciosController::class, 'destroy'])->name('destroy');
    });
    

    # Módulo Configuración - Centros de Costos
    Route::prefix('admin/configuracion/centros_costos')->name('admin.configuracion.centros_costos.')->middleware('permission:configuracion')->group(function () {
        Route::get('/', [CentroCostoController::class, 'index'])->name('index');
        Route::get('/create', [CentroCostoController::class, 'create'])->name('create');
        Route::post('/', [CentroCostoController::class, 'store'])->name('store');
        Route::get('/{centroCosto}/edit', [CentroCostoController::class, 'edit'])->name('edit');
        Route::put('/{centroCosto}', [CentroCostoController::class, 'update'])->name('update');
        Route::delete('/{centroCosto}', [CentroCostoController::class, 'destroy'])->name('destroy');
    });

// Rutas para el módulo de mantenimiento
Route::middleware(['auth', 'admin.access', 'permission:mantenimiento'])->prefix('admin/mantenimiento')->name('admin.mantenimiento.')->group(function () {
    // Dashboard
    Route::get('/', [App\Http\Controllers\Admin\Mantenimiento\DashboardController::class, 'index'])
        ->name('dashboard');
    Route::get('/dashboard/datos', [App\Http\Controllers\Admin\Mantenimiento\DashboardController::class, 'obtenerDatosPorPeriodo'])
        ->name('dashboard.datos');
    
    // Rutas para citas
    Route::resource('citas', App\Http\Controllers\Admin\Mantenimiento\CitaMantenimientoController::class);
    
    // Ruta para búsqueda de clientes (AJAX)
    Route::get('clientes/buscar', [App\Http\Controllers\Admin\Clientes\ClienteController::class, 'buscarParaMantenimiento'])
        ->name('clientes.buscar');
    
    // Ruta para guardar cliente nuevo (AJAX)
    Route::post('clientes/guardar', [App\Http\Controllers\Admin\Clientes\ClienteController::class, 'guardarParaMantenimiento'])
        ->name('clientes.guardar');
    
    // Ruta para debug (Solo en desarrollo)
    Route::post('clientes/debug', [App\Http\Controllers\Admin\Clientes\ClienteController::class, 'debugRequest'])
        ->name('clientes.debug');
    
    // Ruta para búsqueda de vehículos (AJAX)
    Route::get('vehiculos/buscar', [App\Http\Controllers\Admin\Mantenimiento\VehiculoMantenimientoController::class, 'buscar'])
        ->name('vehiculos.buscar');
    
    // Ruta para guardar vehículo nuevo (AJAX)
    Route::post('vehiculos/guardar', [App\Http\Controllers\Admin\Mantenimiento\VehiculoMantenimientoController::class, 'guardar'])
        ->name('vehiculos.guardar');
    
    // Rutas para marcas, modelos y combustibles (AJAX)
    Route::get('vehiculos/marcas', [App\Http\Controllers\Admin\Mantenimiento\VehiculoMantenimientoController::class, 'marcas'])
        ->name('vehiculos.marcas');
    Route::get('vehiculos/modelos', [App\Http\Controllers\Admin\Mantenimiento\VehiculoMantenimientoController::class, 'modelos'])
        ->name('vehiculos.modelos');
    Route::get('vehiculos/combustibles', [App\Http\Controllers\Admin\Mantenimiento\VehiculoMantenimientoController::class, 'combustibles'])
        ->name('vehiculos.combustibles');
    
    // Rutas para ordenes de trabajo
    Route::resource('ordenes', App\Http\Controllers\Admin\Mantenimiento\OrdenTrabajoMantenimientoController::class)
    ->parameters([
        'ordenes' => 'orden'
    ]);
    #SEGUIMIENTOS ORDENES DE TRABAJO
    Route::post('ordenes/{orden}/seguimientos', [App\Http\Controllers\Admin\Mantenimiento\SeguimientoOrdenTrabajoController::class, 'store'])
        ->name('ordenes.seguimientos.store');

    Route::post('ordenes/seguimientos/{seguimiento}/toggle-realizado', [App\Http\Controllers\Admin\Mantenimiento\SeguimientoOrdenTrabajoController::class, 'toggleRealizado'])
        ->name('ordenes.seguimientos.toggle-realizado');

    Route::delete('ordenes/seguimientos/{seguimiento}', [App\Http\Controllers\Admin\Mantenimiento\SeguimientoOrdenTrabajoController::class, 'destroy'])
        ->name('ordenes.seguimientos.destroy');

    Route::get('ordenes/seguimientos/{seguimiento}/comentarios', [App\Http\Controllers\Admin\Mantenimiento\SeguimientoOrdenTrabajoController::class, 'getComentarios'])
        ->name('ordenes.seguimientos.comentarios.index');

    // Rutas para comentarios de seguimientos
    Route::post('ordenes/seguimientos/{seguimiento}/comentarios', [App\Http\Controllers\Admin\Mantenimiento\ComentarioSeguimientoOrdenController::class, 'store'])
        ->name('ordenes.seguimientos.comentarios.store');

    Route::put('ordenes/seguimientos/{seguimiento}/comentarios/{comentario}', [App\Http\Controllers\Admin\Mantenimiento\ComentarioSeguimientoOrdenController::class, 'update'])
        ->name('ordenes.seguimientos.comentarios.update');

    Route::delete('ordenes/seguimientos/{seguimiento}/comentarios/{comentario}', [App\Http\Controllers\Admin\Mantenimiento\ComentarioSeguimientoOrdenController::class, 'destroy'])
        ->name('ordenes.seguimientos.comentarios.destroy');
    
    // Ruta para confirmar cita y generar orden de trabajo
    Route::post('citas/{cita}/confirmar', [App\Http\Controllers\Admin\Mantenimiento\CitaMantenimientoController::class, 'confirmar'])
        ->name('citas.confirmar');
        
    // Ruta para registrar adelanto de dinero
    Route::post('citas/{cita}/adelanto', [App\Http\Controllers\Admin\Mantenimiento\CitaMantenimientoController::class, 'registrarAdelanto'])
        ->name('citas.adelanto');
    
    // Rutas para gestionar órdenes de trabajo
    Route::post('ordenes/{orden}/registrar-diagnostico', [App\Http\Controllers\Admin\Mantenimiento\OrdenTrabajoMantenimientoController::class, 'registrarDiagnostico'])
        ->name('ordenes.registrar-diagnostico');
    
    Route::post('ordenes/{orden}/registrar-aprobacion', [App\Http\Controllers\Admin\Mantenimiento\OrdenTrabajoMantenimientoController::class, 'registrarAprobacion'])
        ->name('ordenes.registrar-aprobacion');
    
    Route::post('ordenes/{orden}/finalizar-trabajo', [App\Http\Controllers\Admin\Mantenimiento\OrdenTrabajoMantenimientoController::class, 'finalizarTrabajo'])
        ->name('ordenes.finalizar-trabajo');
    
    Route::post('ordenes/{orden}/generar-factura', [App\Http\Controllers\Admin\Mantenimiento\OrdenTrabajoMantenimientoController::class, 'generarFactura'])
        ->name('ordenes.generar-factura');
    
    Route::post('ordenes/{orden}/registrar-pago', [App\Http\Controllers\Admin\Mantenimiento\OrdenTrabajoMantenimientoController::class, 'registrarPago'])
        ->name('ordenes.registrar-pago');
    
    // Rutas para agregar repuestos y servicios a una orden
    Route::post('ordenes/{orden}/agregar-repuesto', [App\Http\Controllers\Admin\Mantenimiento\OrdenTrabajoMantenimientoController::class, 'agregarRepuesto'])
        ->name('ordenes.agregar-repuesto');
    
    Route::post('ordenes/{orden}/agregar-servicio', [App\Http\Controllers\Admin\Mantenimiento\OrdenTrabajoMantenimientoController::class, 'agregarServicio'])
        ->name('ordenes.agregar-servicio');

    // Rutas para el escáner de partes (carrito → taller)
    Route::get('ordenes/buscar-activas', [App\Http\Controllers\Admin\Mantenimiento\OrdenTrabajoMantenimientoController::class, 'buscarActivas'])
        ->name('ordenes.buscar-activas');
    Route::post('ordenes/{orden}/agregar-repuestos-lote', [App\Http\Controllers\Admin\Mantenimiento\OrdenTrabajoMantenimientoController::class, 'agregarRepuestosLote'])
        ->name('ordenes.agregar-repuestos-lote');
    
    // Rutas para eliminar repuestos y servicios
    Route::delete('ordenes/repuesto/{detalle}', [App\Http\Controllers\Admin\Mantenimiento\OrdenTrabajoMantenimientoController::class, 'eliminarRepuesto'])
        ->name('ordenes.eliminar-repuesto');
    
    Route::delete('ordenes/servicio/{detalle}', [App\Http\Controllers\Admin\Mantenimiento\OrdenTrabajoMantenimientoController::class, 'eliminarServicio'])
        ->name('ordenes.eliminar-servicio');
    
    // Ruta para imprimir orden/factura
    Route::get('ordenes/{orden}/imprimir', [App\Http\Controllers\Admin\Mantenimiento\OrdenTrabajoMantenimientoController::class, 'imprimirOrden'])
        ->name('ordenes.imprimir');
    Route::get('ordenes/{orden}/pdf', [App\Http\Controllers\Admin\Mantenimiento\OrdenTrabajoMantenimientoController::class, 'generarPDF'])
        ->name('ordenes.pdf');

});
Route::prefix('admin/mantenimiento/ordenes/seguimientos')->middleware(['auth', 'admin.access', 'permission:mantenimiento'])->group(function () {
    Route::get('{seguimiento}/sidebar', [App\Http\Controllers\Admin\Mantenimiento\SeguimientoOrdenTrabajoController::class, 'sidebar'])
        ->name('admin.mantenimiento.ordenes.seguimientos.sidebar');
    Route::get('{seguimiento}/comentarios', [App\Http\Controllers\Admin\Mantenimiento\SeguimientoOrdenTrabajoController::class, 'getComentarios'])
        ->name('admin.mantenimiento.ordenes.seguimientos.comentarios');
    Route::post('{seguimiento}/comentarios', [App\Http\Controllers\Admin\Mantenimiento\ComentarioSeguimientoOrdenController::class, 'store'])
        ->name('admin.mantenimiento.ordenes.seguimientos.comentarios.store');
    Route::delete('{seguimiento}/comentarios/{comentario}', [App\Http\Controllers\Admin\Mantenimiento\ComentarioSeguimientoOrdenController::class, 'destroy'])
        ->name('admin.mantenimiento.ordenes.seguimientos.comentarios.destroy');
    Route::post('{seguimiento}/toggle-realizado', [App\Http\Controllers\Admin\Mantenimiento\SeguimientoOrdenTrabajoController::class, 'toggleRealizado'])
        ->name('admin.mantenimiento.ordenes.seguimientos.toggle-realizado');
});

// Rutas para Planes de Mantenimiento
Route::middleware(['auth', 'admin.access', 'permission:mantenimiento'])->prefix('admin/planes-mantenimiento')->name('admin.planes-mantenimiento.')->group(function () {
    Route::get('/', [PlanMantenimientoController::class, 'index'])->name('index');
    Route::get('/create', [PlanMantenimientoController::class, 'create'])->name('create');
    Route::post('/', [PlanMantenimientoController::class, 'store'])->name('store');
    Route::get('/{planMantenimiento}', [PlanMantenimientoController::class, 'show'])->name('show');
    Route::get('/{planMantenimiento}/edit', [PlanMantenimientoController::class, 'edit'])->name('edit');
    Route::put('/{planMantenimiento}', [PlanMantenimientoController::class, 'update'])->name('update');
    Route::delete('/{planMantenimiento}', [PlanMantenimientoController::class, 'destroy'])->name('destroy');
    Route::patch('/{planMantenimiento}/toggle-status', [PlanMantenimientoController::class, 'toggleStatus'])->name('toggle-status');
    Route::post('/{planMantenimiento}/duplicate', [PlanMantenimientoController::class, 'duplicate'])->name('duplicate');
});

# Módulo Mantenimiento - Técnicos
Route::middleware(['auth', 'admin.access', 'permission:mantenimiento'])->prefix('admin/mantenimiento/tecnicos')->name('admin.mantenimiento.tecnicos.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\Mantenimiento\TecnicoController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\Admin\Mantenimiento\TecnicoController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\Admin\Mantenimiento\TecnicoController::class, 'store'])->name('store');
    Route::get('/{tecnico}', [App\Http\Controllers\Admin\Mantenimiento\TecnicoController::class, 'show'])->name('show');
    Route::get('/{tecnico}/edit', [App\Http\Controllers\Admin\Mantenimiento\TecnicoController::class, 'edit'])->name('edit');
    Route::put('/{tecnico}', [App\Http\Controllers\Admin\Mantenimiento\TecnicoController::class, 'update'])->name('update');
    Route::delete('/{tecnico}', [App\Http\Controllers\Admin\Mantenimiento\TecnicoController::class, 'destroy'])->name('destroy');
});

    Route::prefix('admin/almacenes')->name('admin.almacenes.')->middleware('permission:almacenes')->group(function () {
        // Rutas de almacenes
        Route::get('/', [AlmacenController::class, 'index'])->name('index');
        Route::get('/create', [AlmacenController::class, 'create'])->name('create');
        Route::post('/', [AlmacenController::class, 'store'])->name('store');
        Route::get('/{almacen}/edit', [AlmacenController::class, 'edit'])->name('edit');
        Route::put('/{almacen}', [AlmacenController::class, 'update'])->name('update');
        Route::delete('/{almacen}', [AlmacenController::class, 'destroy'])->name('destroy');
    
        // Rutas de inventario dentro del grupo de almacenes
        Route::prefix('inventario')->name('inventario.')->group(function () {
            Route::get('/', [InventarioController::class, 'index'])->name('index');
            Route::get('/{inventario}/kardex', [InventarioController::class, 'kardex'])->name('kardex');
        });
    });
    # Módulo Almacenes - Partes
    Route::prefix('admin/almacenes/partes')->name('admin.almacenes.partes.')->middleware('permission:almacenes')->group(function () {
        Route::get('/', [ParteController::class, 'index'])->name('index');
        Route::get('/create', [ParteController::class, 'create'])->name('create');
        Route::post('/', [ParteController::class, 'store'])->name('store');
        Route::get('/buscar-codigo', [ParteController::class, 'buscarPorCodigo'])->name('buscar-codigo');
        Route::get('/{parte}/edit', [ParteController::class, 'edit'])->name('edit');
        Route::put('/{parte}', [ParteController::class, 'update'])->name('update');
        Route::delete('/{parte}', [ParteController::class, 'destroy'])->name('destroy');
    });
    
    # Servicios
    Route::prefix('admin/productos-servicios/servicios')->name('admin.productos-servicios.servicios.')->middleware('permission:almacenes')->group(function () {
        Route::get('/', [ServicioController::class, 'index'])->name('index');
        Route::get('/create', [ServicioController::class, 'create'])->name('create');
        Route::post('/', [ServicioController::class, 'store'])->name('store');
        Route::get('/{servicioTercerizado}/edit', [ServicioController::class, 'edit'])->name('edit');
        Route::put('/{servicioTercerizado}', [ServicioController::class, 'update'])->name('update');
        Route::delete('/{servicioTercerizado}', [ServicioController::class, 'destroy'])->name('destroy');
    });

    # Módulo Configuración - Unidades
    Route::prefix('admin/configuracion/unidades')->name('admin.configuracion.unidades.')->middleware('permission:configuracion')->group(function () {
        Route::get('/', [UnidadController::class, 'index'])->name('index');
        Route::get('/create', [UnidadController::class, 'create'])->name('create');
        Route::post('/', [UnidadController::class, 'store'])->name('store');
        Route::get('/{unidad}/edit', [UnidadController::class, 'edit'])->name('edit');
        Route::put('/{unidad}', [UnidadController::class, 'update'])->name('update');
        Route::delete('/{unidad}', [UnidadController::class, 'destroy'])->name('destroy');
    });

    # Módulo Configuración - Reglas de Vencimiento de Cotizaciones
    Route::prefix('admin/configuracion/reglas-vencimiento-cotizaciones')->name('admin.configuracion.reglas-vencimiento-cotizaciones.')->middleware('permission:configuracion')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\Configuracion\ReglaVencimientoCotizacionController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\Configuracion\ReglaVencimientoCotizacionController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\Configuracion\ReglaVencimientoCotizacionController::class, 'store'])->name('store');
        Route::get('/{regla}/edit', [App\Http\Controllers\Admin\Configuracion\ReglaVencimientoCotizacionController::class, 'edit'])->name('edit');
        Route::put('/{regla}', [App\Http\Controllers\Admin\Configuracion\ReglaVencimientoCotizacionController::class, 'update'])->name('update');
        Route::delete('/{regla}', [App\Http\Controllers\Admin\Configuracion\ReglaVencimientoCotizacionController::class, 'destroy'])->name('destroy');
        Route::patch('/{regla}/toggle-activo', [App\Http\Controllers\Admin\Configuracion\ReglaVencimientoCotizacionController::class, 'toggleActivo'])->name('toggle-activo');
    });

    # Módulo Configuración - Tipos de Cambio
    Route::prefix('admin/configuracion/tipos-cambio')->name('admin.configuracion.tipos-cambio.')->middleware('permission:configuracion')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\TipoCambioController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\TipoCambioController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\TipoCambioController::class, 'store'])->name('store');
        Route::get('/{tipoCambio}', [App\Http\Controllers\Admin\TipoCambioController::class, 'show'])->name('show');
        Route::get('/{tipoCambio}/edit', [App\Http\Controllers\Admin\TipoCambioController::class, 'edit'])->name('edit');
        Route::put('/{tipoCambio}', [App\Http\Controllers\Admin\TipoCambioController::class, 'update'])->name('update');
        Route::delete('/{tipoCambio}', [App\Http\Controllers\Admin\TipoCambioController::class, 'destroy'])->name('destroy');
        Route::post('/sunat', [App\Http\Controllers\Admin\TipoCambioController::class, 'obtenerDeSunat'])->name('sunat');
        Route::post('/{tipoCambio}/toggle', [App\Http\Controllers\Admin\TipoCambioController::class, 'toggleActivo'])->name('toggle');
    });

    # API para tipos de cambio (para uso en POS y otros módulos)
    Route::get('api/tipo-cambio', [App\Http\Controllers\Admin\TipoCambioController::class, 'api'])->name('api.tipo-cambio');

    # Módulo Almacenes - Servicios Tercerizados
    Route::prefix('admin/almacenes/servicios-terceros')->name('admin.almacenes.servicios-terceros.')->middleware('permission:almacenes')->group(function () {
        Route::get('/', [ServicioController::class, 'index'])->name('index');
        Route::get('/create', [ServicioController::class, 'create'])->name('create');
        Route::post('/', [ServicioController::class, 'store'])->name('store');
        Route::get('/{servicioTercerizado}/edit', [ServicioController::class, 'edit'])->name('edit');
        Route::put('/{servicioTercerizado}', [ServicioController::class, 'update'])->name('update');
        Route::delete('/{servicioTercerizado}', [ServicioController::class, 'destroy'])->name('destroy');
    });

    #Módulo Talleres
    Route::prefix('admin/talleres')->name('admin.talleres.')->middleware('permission:mantenimiento')->group(function () {
        Route::get('/', [TallerController::class, 'index'])->name('index');
        Route::get('/create', [TallerController::class, 'create'])->name('create');
        Route::post('/', [TallerController::class, 'store'])->name('store');
        Route::get('/{taller}/edit', [TallerController::class, 'edit'])->name('edit');
        Route::put('/{taller}', [TallerController::class, 'update'])->name('update');
        Route::delete('/{taller}', [TallerController::class, 'destroy'])->name('destroy');
    });
    # Módulo Vehículos
    Route::prefix('admin/productos-servicios/vehiculos')->name('admin.productos-servicios.vehiculos.')->middleware('permission:almacenes')->group(function () {
        // Ruta que carga la vista pestanas.blade.php
        Route::get('/', [VehiculoController::class, 'vehiculosIndex'])->name('index');
    
        // Rutas de CRUD para vehículo catálogo
        Route::prefix('caracteristicas/vehiculo')->name('caracteristicas.vehiculo.')->group(function () {
            Route::get('/', [VehiculoController::class, 'index'])->name('index');
            Route::get('/create', [VehiculoController::class, 'create'])->name('create');
            Route::post('/', [VehiculoController::class, 'store'])->name('store');
            Route::get('/{Vehiculo}/edit', [VehiculoController::class, 'edit'])->name('edit');
            Route::put('/{Vehiculo}', [VehiculoController::class, 'update'])->name('update');
            Route::delete('/{Vehiculo}', [VehiculoController::class, 'destroy'])->name('destroy');
        });
        
        Route::prefix('caracteristicas/marcas')->name('caracteristicas.marcas.')->group(function () {
            Route::get('/', [MarcaController::class, 'index'])->name('index');
            Route::get('/create', [MarcaController::class, 'create'])->name('create');
            Route::post('/', [MarcaController::class, 'store'])->name('store');
            Route::get('/{marca}/edit', [MarcaController::class, 'edit'])->name('edit');
            Route::put('/{marca}', [MarcaController::class, 'update'])->name('update');
            Route::delete('/{marca}', [MarcaController::class, 'destroy'])->name('destroy');
        });
        Route::prefix('caracteristicas/modelos')->name('caracteristicas.modelos.')->group(function () {
            Route::get('/', [ModeloController::class, 'index'])->name('index');
            Route::get('/create', [ModeloController::class, 'create'])->name('create');
            Route::post('/', [ModeloController::class, 'store'])->name('store');
            Route::get('/{modelo}/edit', [ModeloController::class, 'edit'])->name('edit');
            Route::put('/{modelo}', [ModeloController::class, 'update'])->name('update');
            Route::delete('/{modelo}', [ModeloController::class, 'destroy'])->name('destroy');
        });
        Route::prefix('caracteristicas/versiones')->name('caracteristicas.versiones.')->group(function () {
            Route::get('/', [VersionController::class, 'index'])->name('index');
            Route::get('/create', [VersionController::class, 'create'])->name('create');
            Route::post('/', [VersionController::class, 'store'])->name('store');
            Route::get('/{version}/edit', [VersionController::class, 'edit'])->name('edit');
            Route::put('/{version}', [VersionController::class, 'update'])->name('update');
            Route::delete('/{version}', [VersionController::class, 'destroy'])->name('destroy');
        });
        Route::prefix('caracteristicas/anios-modelo')->name('caracteristicas.anios-modelo.')->group(function () {
            Route::get('/', [AnioModeloController::class, 'index'])->name('index');
            Route::get('/create', [AnioModeloController::class, 'create'])->name('create');
            Route::post('/', [AnioModeloController::class, 'store'])->name('store');
            Route::get('/{anioModelo}/edit', [AnioModeloController::class, 'edit'])->name('edit');
            Route::put('/{anioModelo}', [AnioModeloController::class, 'update'])->name('update');
            Route::delete('/{anioModelo}', [AnioModeloController::class, 'destroy'])->name('destroy');
        });
        Route::prefix('caracteristicas/colores')->name('caracteristicas.colores.')->group(function () {
            Route::get('/', [ColorController::class, 'index'])->name('index');
            Route::get('/create', [ColorController::class, 'create'])->name('create');
            Route::post('/', [ColorController::class, 'store'])->name('store');
            Route::get('/{color}/edit', [ColorController::class, 'edit'])->name('edit');
            Route::put('/{color}', [ColorController::class, 'update'])->name('update');
            Route::delete('/{color}', [ColorController::class, 'destroy'])->name('destroy');
        });
    });

    # Módulo Compras - Proveedores
    Route::prefix('admin/compras/proveedores')->name('admin.compras.proveedores.')->middleware('permission:compras')->group(function () {
        // Rutas de proveedores
        Route::get('/', [ProveedorController::class, 'index'])->name('index');
        Route::get('/create', [ProveedorController::class, 'create'])->name('create');
        Route::post('/', [ProveedorController::class, 'store'])->name('store');
        Route::get('/{proveedor}/edit', [ProveedorController::class, 'edit'])->name('edit');
        Route::put('/{proveedor}', [ProveedorController::class, 'update'])->name('update');
        Route::delete('/{proveedor}', [ProveedorController::class, 'destroy'])->name('destroy');
        Route::post('/validar-documento', [ProveedorController::class, 'validarDocumento'])->name('validar.documento');
        Route::get('/provincias', [ProveedorController::class, 'getProvinciasAjax'])->name('provincias');
        Route::get('/distritos', [ProveedorController::class, 'getDistritosAjax'])->name('distritos');
        Route::post('/{proveedor}/cuentas', [ProveedorController::class, 'storeCuenta'])->name('cuentas.store');
        Route::get('/{proveedor}/cuentas/{cuenta}/edit', [ProveedorController::class, 'editCuenta'])->name('cuentas.edit');
        Route::put('/{proveedor}/cuentas/{cuenta}', [ProveedorController::class, 'updateCuenta'])->name('cuentas.update');
        Route::delete('/{proveedor}/cuentas/{cuenta}', [ProveedorController::class, 'destroyCuenta'])->name('cuentas.destroy');

        // Rutas de categorías de proveedor
        Route::prefix('categorias')->name('categorias.')->group(function () {
            Route::get('/', [CategoriaProveedorController::class, 'index'])->name('index');
            Route::get('/create', [CategoriaProveedorController::class, 'create'])->name('create');
            Route::post('/', [CategoriaProveedorController::class, 'store'])->name('store');
            Route::get('/{categoriaProveedor}/edit', [CategoriaProveedorController::class, 'edit'])->name('edit');
            Route::put('/{categoriaProveedor}', [CategoriaProveedorController::class, 'update'])->name('update');
            Route::delete('/{categoriaProveedor}', [CategoriaProveedorController::class, 'destroy'])->name('destroy');
        });
    });

    # Módulo Compras - Requerimientos y Órdenes
    Route::prefix('admin/compras')->name('admin.compras.')->middleware('permission:compras')->group(function () {
        Route::resource('ordenes', OrdenCompraController::class);
        Route::post('ordenes/{orden}/aprobar', [OrdenCompraController::class, 'aprobar'])->name('ordenes.aprobar');
        Route::post('ordenes/{orden}/rechazar', [OrdenCompraController::class, 'rechazar'])->name('ordenes.rechazar');
        Route::get('requerimientos/search-partes', [RequerimientoCompraController::class, 'searchPartes'])->name('requerimientos.search-partes');
        Route::get('requerimientos/search-proveedores', [RequerimientoCompraController::class, 'searchProveedores'])->name('requerimientos.search-proveedores'); 
        Route::get('requerimientos', [RequerimientoCompraController::class, 'index'])->name('requerimientos.index');
        Route::get('requerimientos/create', [RequerimientoCompraController::class, 'create'])->name('requerimientos.create');
        Route::post('requerimientos', [RequerimientoCompraController::class, 'store'])->name('requerimientos.store');
        Route::get('requerimientos/{id}', [RequerimientoCompraController::class, 'show'])->name('requerimientos.show');
        Route::get('requerimientos/{id}/edit', [RequerimientoCompraController::class, 'edit'])->name('requerimientos.edit');
        Route::put('requerimientos/{id}', [RequerimientoCompraController::class, 'update'])->name('requerimientos.update');
        Route::delete('requerimientos/{id}', [RequerimientoCompraController::class, 'destroy'])->name('requerimientos.destroy');
    });

    # Módulo Inventario - Traslados
    Route::prefix('admin/inventario/traslados')->name('admin.inventario.traslados.')->middleware('permission:inventario')->group(function () {
        Route::get('/', [TrasladoController::class, 'index'])->name('index');
        Route::get('/create', [TrasladoController::class, 'create'])->name('create');
        Route::post('/', [TrasladoController::class, 'store'])->name('store');
        Route::get('/{traslado}', [TrasladoController::class, 'show'])->name('show');
        Route::post('/{traslado}/cambiar-estado', [TrasladoController::class, 'cambiarEstado'])->name('cambiar-estado');
        Route::get('/get-stock', [TrasladoController::class, 'getStock'])->name('get-stock');
    });

    # Módulo Establecimientos
    Route::prefix('admin/establecimientos')->name('admin.establecimientos.')->middleware('permission:configuracion')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\Establecimientos\EstablecimientoController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\Establecimientos\EstablecimientoController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\Establecimientos\EstablecimientoController::class, 'store'])->name('store');
        Route::get('/{establecimiento}/edit', [App\Http\Controllers\Admin\Establecimientos\EstablecimientoController::class, 'edit'])->name('edit');
        Route::put('/{establecimiento}', [App\Http\Controllers\Admin\Establecimientos\EstablecimientoController::class, 'update'])->name('update');
        Route::delete('/{establecimiento}', [App\Http\Controllers\Admin\Establecimientos\EstablecimientoController::class, 'destroy'])->name('destroy');
    });

    #KARDEX
    Route::prefix('admin/inventario/kardex')->name('admin.inventario.kardex.')->middleware('permission:inventario')->group(function () {
        Route::get('/', [KardexController::class, 'index'])->name('form');
        Route::get('/reporte', [KardexController::class, 'reporte'])->name('reporte');
        Route::get('/consulta', [KardexController::class, 'consulta'])->name('consulta');
        Route::get('/movimientos', [KardexController::class, 'movimientos'])->name('movimientos');
    });
    #INVENTARIOS
    Route::prefix('admin/inventario')->name('admin.inventario.')->middleware(['auth', 'permission:inventario'])->group(function () {
    // Ruta raíz
        Route::get('/', [InventarioController::class, 'index'])->name('index');
    
        // Ruta para mostrar el formulario de kardex
        Route::get('/kardex', [InventarioController::class, 'kardexForm'])->name('kardex.form');
    
        // Ruta para generar el reporte de kardex
        Route::get('/kardex/reporte', [InventarioController::class, 'kardexReporte'])->name('kardex.reporte');
    
        // Ruta existente para el kardex de un inventario específico
        Route::get('/{inventario}/kardex', [InventarioController::class, 'kardex'])->name('kardex');
    
        // Movimientos
        Route::prefix('movimientos')->name('movimientos.')->group(function () {
            Route::get('/', [MovimientoController::class, 'index'])->name('index');
            Route::get('/create', [MovimientoController::class, 'create'])->name('create');
            Route::post('/', [MovimientoController::class, 'store'])->name('store');
            Route::get('/{movimiento}', [MovimientoController::class, 'show'])->name('show');
            Route::get('/{movimiento}/edit', [MovimientoController::class, 'edit'])->name('edit');
            Route::put('/{movimiento}', [MovimientoController::class, 'update'])->name('update');
            Route::delete('/{movimiento}', [MovimientoController::class, 'destroy'])->name('destroy');
        });
    
        // Reporte de inventario
        Route::get('/reporte-inventario', [InventarioReporteController::class, 'inventario'])->name('reporte-inventario');
    
        // Devoluciones
        Route::get('devoluciones/buscar', [DevolucionProveedorController::class, 'buscarItems'])->name('devoluciones.buscar-items');
        Route::get('devoluciones', [DevolucionProveedorController::class, 'index'])->name('devoluciones.index');
        Route::get('devoluciones/create', [DevolucionProveedorController::class, 'create'])->name('devoluciones.create');
        Route::post('devoluciones', [DevolucionProveedorController::class, 'store'])->name('devoluciones.store');
        Route::get('devoluciones/{id}', [DevolucionProveedorController::class, 'show'])->name('devoluciones.show');
        Route::get('devoluciones/{id}/edit', [DevolucionProveedorController::class, 'edit'])->name('devoluciones.edit');
        Route::put('devoluciones/{id}', [DevolucionProveedorController::class, 'update'])->name('devoluciones.update');
        Route::delete('devoluciones/{id}', [DevolucionProveedorController::class, 'destroy'])->name('devoluciones.destroy');
        Route::put('devoluciones/{id}/confirmar', [DevolucionProveedorController::class, 'confirmar'])->name('devoluciones.confirmar');
    
        // Traslados
        Route::prefix('traslados')->name('traslados.')->group(function () {
            Route::get('/', [TrasladoController::class, 'index'])->name('index');
            Route::get('/create', [TrasladoController::class, 'create'])->name('create');
            Route::post('/', [TrasladoController::class, 'store'])->name('store');
            Route::get('/{traslado}', [TrasladoController::class, 'show'])->name('show');
            Route::post('/{traslado}/cambiar-estado', [TrasladoController::class, 'cambiarEstado'])->name('cambiar-estado');
            Route::get('/get-stock', [TrasladoController::class, 'getStock'])->name('get-stock');
        });
    });

    # Módulo Usuarios
    Route::prefix('admin/usuarios')->name('admin.usuarios.')->middleware('permission:usuarios')->group(function () {
        Route::prefix('usuarios')->name('usuarios.')->group(function () {
            Route::get('/', [UsuarioController::class, 'index'])->name('index');
            Route::get('/create', [UsuarioController::class, 'create'])->name('create');
            Route::post('/', [UsuarioController::class, 'store'])->name('store');
            Route::get('/{usuario}', [UsuarioController::class, 'show'])->name('show');
            Route::get('/{usuario}/edit', [UsuarioController::class, 'edit'])->name('edit');
            Route::put('/{usuario}', [UsuarioController::class, 'update'])->name('update');
            Route::delete('/{usuario}', [UsuarioController::class, 'destroy'])->name('destroy');
        });
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RolController::class, 'index'])->name('index');
            Route::get('/create', [RolController::class, 'create'])->name('create');
            Route::post('/', [RolController::class, 'store'])->name('store');
            Route::get('/{rol}', [RolController::class, 'show'])->name('show');
            Route::get('/{rol}/edit', [RolController::class, 'edit'])->name('edit');
            Route::put('/{rol}', [RolController::class, 'update'])->name('update');
            Route::delete('/{rol}', [RolController::class, 'destroy'])->name('destroy');
        });
    });

    # Módulo Reportes
    Route::prefix('admin/reportes')->name('admin.reportes.')->middleware('permission:reportes')->group(function () {
        Route::get('/', [ReporteController::class, 'index'])->name('index');
        Route::get('/ventas', [ReporteController::class, 'ventas'])->name('ventas');
        Route::get('/compras', [ReporteController::class, 'compras'])->name('compras');
        Route::get('/inventario', [ReporteController::class, 'inventario'])->name('inventario');
        Route::get('/mantenimiento', [ReporteController::class, 'mantenimiento'])->name('mantenimiento');
    });

    # Módulo Ventas - Ventas
    Route::prefix('admin/ventas/ventas')->name('admin.ventas.ventas.')->middleware('permission:ventas')->group(function () {
        // Route::get('/guias-entrega', [App\Http\Controllers\Admin\Ventas\GuiaEntregaController::class, 'index'])->name('guias-entrega.index');
        // Route::get('/guias-entrega/create', [App\Http\Controllers\Admin\Ventas\GuiaEntregaController::class, 'create'])->name('guias-entrega.create');
        // Route::post('/guias-entrega', [App\Http\Controllers\Admin\Ventas\GuiaEntregaController::class, 'store'])->name('guias-entrega.store');
        // Route::get('/guias-entrega/{guia}', [App\Http\Controllers\Admin\Ventas\GuiaEntregaController::class, 'show'])->name('guias-entrega.show');
        // Route::get('/guias-entrega/{guia}/edit', [App\Http\Controllers\Admin\Ventas\GuiaEntregaController::class, 'edit'])->name('guias-entrega.edit');
        // Route::put('/guias-entrega/{guia}', [App\Http\Controllers\Admin\Ventas\GuiaEntregaController::class, 'update'])->name('guias-entrega.update');
        // Route::delete('/guias-entrega/{guia}', [App\Http\Controllers\Admin\Ventas\GuiaEntregaController::class, 'destroy'])->name('guias-entrega.destroy');
        // Route::get('/vales-devolucion', [App\Http\Controllers\Admin\Ventas\ValesDevolucionController::class, 'index'])->name('vales-devolucion.index');
        // Route::get('/vales-devolucion/create', [App\Http\Controllers\Admin\Ventas\ValesDevolucionController::class, 'create'])->name('vales-devolucion.create');
        // Route::post('/vales-devolucion', [App\Http\Controllers\Admin\Ventas\ValesDevolucionController::class, 'store'])->name('vales-devolucion.store');
        // Route::get('/vales-devolucion/{vale}', [App\Http\Controllers\Admin\Ventas\ValesDevolucionController::class, 'show'])->name('vales-devolucion.show');
        // Route::get('/vales-devolucion/{vale}/edit', [App\Http\Controllers\Admin\Ventas\ValesDevolucionController::class, 'edit'])->name('vales-devolucion.edit');
        // Route::put('/vales-devolucion/{vale}', [App\Http\Controllers\Admin\Ventas\ValesDevolucionController::class, 'update'])->name('vales-devolucion.update');
        // Route::delete('/vales-devolucion/{vale}', [App\Http\Controllers\Admin\Ventas\ValesDevolucionController::class, 'destroy'])->name('vales-devolucion.destroy');
        // Route::get('/recepcion', [App\Http\Controllers\Admin\Ventas\RecepcionController::class, 'index'])->name('recepcion.index');
        // Route::get('/recepcion/create', [App\Http\Controllers\Admin\Ventas\RecepcionController::class, 'create'])->name('recepcion.create');
        // Route::post('/recepcion', [App\Http\Controllers\Admin\Ventas\RecepcionController::class, 'store'])->name('recepcion.store');
        // Route::get('/recepcion/{recepcion}', [App\Http\Controllers\Admin\Ventas\RecepcionController::class, 'show'])->name('recepcion.show');
        // Route::get('/recepcion/{recepcion}/edit', [App\Http\Controllers\Admin\Ventas\RecepcionController::class, 'edit'])->name('recepcion.edit');
        // Route::put('/recepcion/{recepcion}', [App\Http\Controllers\Admin\Ventas\RecepcionController::class, 'update'])->name('recepcion.update');
        // Route::delete('/recepcion/{recepcion}', [App\Http\Controllers\Admin\Ventas\RecepcionController::class, 'destroy'])->name('recepcion.destroy');
        // Route::get('/inventarios', [App\Http\Controllers\Admin\Ventas\InventarioController::class, 'index'])->name('inventarios.index');
        // Route::get('/inventarios/create', [App\Http\Controllers\Admin\Ventas\InventarioController::class, 'create'])->name('inventarios.create');
        // Route::post('/inventarios', [App\Http\Controllers\Admin\Ventas\InventarioController::class, 'store'])->name('inventarios.store');
        // Route::get('/inventarios/{inventario}', [App\Http\Controllers\Admin\Ventas\InventarioController::class, 'show'])->name('inventarios.show');
        // Route::get('/inventarios/{inventario}/edit', [App\Http\Controllers\Admin\Ventas\InventarioController::class, 'edit'])->name('inventarios.edit');
        // Route::put('/inventarios/{inventario}', [App\Http\Controllers\Admin\Ventas\InventarioController::class, 'update'])->name('inventarios.update');
        // Route::delete('/inventarios/{inventario}', [App\Http\Controllers\Admin\Ventas\InventarioController::class, 'destroy'])->name('inventarios.destroy');
    });

    # Módulo Compras - Documentos
    Route::prefix('admin/compras/documentos')->name('admin.compras.documentos.')->middleware('permission:compras')->group(function () {
        Route::get('/guias-entrega', [App\Http\Controllers\Admin\Compras\GuiaEntregaController::class, 'index'])->name('guias-entrega.index');
        Route::get('/guias-entrega/create', [App\Http\Controllers\Admin\Compras\GuiaEntregaController::class, 'create'])->name('guias-entrega.create');
        Route::post('/guias-entrega', [App\Http\Controllers\Admin\Compras\GuiaEntregaController::class, 'store'])->name('guias-entrega.store');
        Route::get('/guias-entrega/{guia}', [App\Http\Controllers\Admin\Compras\GuiaEntregaController::class, 'show'])->name('guias-entrega.show');
        Route::get('/guias-entrega/{guia}/edit', [App\Http\Controllers\Admin\Compras\GuiaEntregaController::class, 'edit'])->name('guias-entrega.edit');
        Route::put('/guias-entrega/{guia}', [App\Http\Controllers\Admin\Compras\GuiaEntregaController::class, 'update'])->name('guias-entrega.update');
        Route::delete('/guias-entrega/{guia}', [App\Http\Controllers\Admin\Compras\GuiaEntregaController::class, 'destroy'])->name('guias-entrega.destroy');
        // Route::get('/vales-devolucion', [App\Http\Controllers\Admin\Compras\ValesDevolucionController::class, 'index'])->name('vales-devolucion.index');
        // Route::get('/vales-devolucion/create', [App\Http\Controllers\Admin\Compras\ValesDevolucionController::class, 'create'])->name('vales-devolucion.create');
        // Route::post('/vales-devolucion', [App\Http\Controllers\Admin\Compras\ValesDevolucionController::class, 'store'])->name('vales-devolucion.store');
        // Route::get('/vales-devolucion/{vale}', [App\Http\Controllers\Admin\Compras\ValesDevolucionController::class, 'show'])->name('vales-devolucion.show');
        // Route::get('/vales-devolucion/{vale}/edit', [App\Http\Controllers\Admin\Compras\ValesDevolucionController::class, 'edit'])->name('vales-devolucion.edit');
        // Route::put('/vales-devolucion/{vale}', [App\Http\Controllers\Admin\Compras\ValesDevolucionController::class, 'update'])->name('vales-devolucion.update');
        // Route::delete('/vales-devolucion/{vale}', [App\Http\Controllers\Admin\Compras\ValesDevolucionController::class, 'destroy'])->name('vales-devolucion.destroy');
        Route::get('/recepcion', [App\Http\Controllers\Admin\Compras\RecepcionController::class, 'index'])->name('recepcion.index');
        Route::get('/recepcion/create', [App\Http\Controllers\Admin\Compras\RecepcionController::class, 'create'])->name('recepcion.create');
        Route::post('/recepcion', [App\Http\Controllers\Admin\Compras\RecepcionController::class, 'store'])->name('recepcion.store');
        Route::get('/recepcion/{recepcion}', [App\Http\Controllers\Admin\Compras\RecepcionController::class, 'show'])->name('recepcion.show');
        Route::get('/recepcion/{recepcion}/edit', [App\Http\Controllers\Admin\Compras\RecepcionController::class, 'edit'])->name('recepcion.edit');
        Route::put('/recepcion/{recepcion}', [App\Http\Controllers\Admin\Compras\RecepcionController::class, 'update'])->name('recepcion.update');
        Route::delete('/recepcion/{recepcion}', [App\Http\Controllers\Admin\Compras\RecepcionController::class, 'destroy'])->name('recepcion.destroy');
    });

    # Módulo Archivos de la Empresa
    Route::prefix('admin/archivos-empresa')->name('admin.archivos-empresa.')->middleware('permission:configuracion')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ArchivoEmpresaController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Admin\ArchivoEmpresaController::class, 'store'])->name('store');
        Route::delete('/{nombre}', [App\Http\Controllers\Admin\ArchivoEmpresaController::class, 'destroy'])->name('destroy');
    });
});


#POS

Route::group(['middleware' => ['auth', 'admin.access', 'permission:ventas']], function () {
    Route::prefix('admin/ventas/pos')->name('admin.ventas.pos.')->group(function () {
        
        // Ruta principal del POS
        Route::get('/', [\App\Http\Controllers\Admin\Ventas\POSController::class, 'index'])->name('index');
        
        // ========== NUEVAS RUTAS PARA GESTIÓN DE VENTAS ==========
        Route::get('/ventas', [\App\Http\Controllers\Admin\Ventas\POSController::class, 'ventas'])->name('ventas');
        Route::get('/ventas/list', [\App\Http\Controllers\Admin\Ventas\POSController::class, 'listarVentas'])->name('ventas.list');
        Route::get('/ventas/{id}', [\App\Http\Controllers\Admin\Ventas\POSController::class, 'mostrarVenta'])->name('ventas.show');
        Route::post('/ventas/registrar-pago', [\App\Http\Controllers\Admin\Ventas\POSController::class, 'registrarPago'])->name('ventas.registrar-pago');
        Route::post('/ventas/{id}/marcar-lista-entrega', [\App\Http\Controllers\Admin\Ventas\POSController::class, 'marcarListaEntrega'])->name('ventas.marcar-lista-entrega');
        Route::post('/ventas/{id}/marcar-despachada', [\App\Http\Controllers\Admin\Ventas\POSController::class, 'marcarDespachada'])->name('ventas.marcar-despachada');
        Route::get('/ventas/{id}/imprimir', [\App\Http\Controllers\Admin\Ventas\POSController::class, 'imprimirVenta'])->name('ventas.imprimir');
        Route::get('/ventas/{id}/ticket', [\App\Http\Controllers\Admin\Ventas\POSController::class, 'imprimirTicket'])->name('ventas.ticket');
        Route::get('/ventas/exportar/excel', [\App\Http\Controllers\Admin\Ventas\POSController::class, 'exportarVentas'])->name('ventas.exportar');
        // ========================================================
        
        // Rutas AJAX existentes del POS
        Route::get('/obtener-todas-partes', [\App\Http\Controllers\Admin\Ventas\POSController::class, 'obtenerTodasPartes'])->name('obtener-todas-partes');
        Route::get('/buscar-partes', [\App\Http\Controllers\Admin\Ventas\POSController::class, 'buscarPartes'])->name('buscar-partes');
        Route::get('/buscar-servicios', [\App\Http\Controllers\Admin\Ventas\POSController::class, 'buscarServicios'])->name('buscar-servicios');
        Route::get('/buscar-clientes', [\App\Http\Controllers\Admin\Ventas\POSController::class, 'buscarClientes'])->name('buscar-clientes');
        Route::get('/items-populares', [\App\Http\Controllers\Admin\Ventas\POSController::class, 'itemsPopulares'])->name('items-populares');
        Route::get('/get-stock-parte', [\App\Http\Controllers\Admin\Ventas\POSController::class, 'getStockParte'])->name('get-stock-parte');
       
        // Procesamiento de ventas
        Route::post('/procesar-venta', [\App\Http\Controllers\Admin\Ventas\POSController::class, 'procesarVenta'])->name('procesar-venta');
        Route::post('/crear-cliente', [\App\Http\Controllers\Admin\Ventas\POSController::class, 'crearCliente'])->name('crear-cliente');
        
        // Rutas de debug (opcional, puedes removerlas en producción)
        Route::get('/debug-busqueda', [\App\Http\Controllers\Admin\Ventas\POSController::class, 'debugBusqueda'])->name('debug-busqueda');
        Route::get('/debug-clientes-estructura', [\App\Http\Controllers\Admin\Ventas\POSController::class, 'debugClientesEstructura'])->name('debug-clientes-estructura');
        Route::get('/debug-estructura-completa', [\App\Http\Controllers\Admin\Ventas\POSController::class, 'debugEstructuraCompleta'])->name('debug-estructura-completa');
    });
});
Route::get('/admin/productos-servicios/vehiculos/import', [App\Http\Controllers\Admin\VehiculoImportController::class, 'showImportForm'])->name('admin.productos-servicios.vehiculos.import.form')->middleware(['auth', 'admin.access', 'permission:almacenes']);
Route::post('/admin/productos-servicios/vehiculos/import', [App\Http\Controllers\Admin\VehiculoImportController::class, 'import'])->name('admin.productos-servicios.vehiculos.import')->middleware(['auth', 'admin.access', 'permission:almacenes']);


#RECEPCIÓN PRODUCTOS
// Rutas para recepción de órdenes de compra
Route::prefix('admin/compras/recepcion')->name('admin.recepcion.')->middleware(['auth', 'admin.access', 'permission:compras'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\Compras\RecepcionController::class, 'index'])->name('index');
    Route::get('/{ordenCompra}/show', [\App\Http\Controllers\Admin\Compras\RecepcionController::class, 'show'])->name('show');
    Route::post('/{ordenCompra}', [\App\Http\Controllers\Admin\Compras\RecepcionController::class, 'store'])->name('store');
    Route::get('/{ordenCompra}/detalle', [\App\Http\Controllers\Admin\Compras\RecepcionController::class, 'detalle'])->name('detalle');
    Route::post('/{ordenCompra}/devolver', [\App\Http\Controllers\Admin\Compras\RecepcionController::class, 'devolver'])->name('devolver');
    Route::post('/{ordenCompra}/completar-faltantes', [\App\Http\Controllers\Admin\Compras\RecepcionController::class, 'completarConFaltantes'])->name('completar.faltantes');
    Route::get('/historial/todas', [\App\Http\Controllers\Admin\Compras\RecepcionController::class, 'historial'])->name('historial');
});

// Rutas para guías de entrega
Route::prefix('admin/compras/guias')->name('admin.guias.')->middleware(['auth', 'admin.access', 'permission:compras'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\Compras\GuiaEntregaController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\Compras\GuiaEntregaController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\Compras\GuiaEntregaController::class, 'store'])->name('store');
    Route::get('/{guia}', [\App\Http\Controllers\Admin\Compras\GuiaEntregaController::class, 'show'])->name('show');
    Route::get('/{guia}/edit', [\App\Http\Controllers\Admin\Compras\GuiaEntregaController::class, 'edit'])->name('edit');
    Route::put('/{guia}', [\App\Http\Controllers\Admin\Compras\GuiaEntregaController::class, 'update'])->name('update');
    Route::delete('/{guia}', [\App\Http\Controllers\Admin\Compras\GuiaEntregaController::class, 'destroy'])->name('destroy');
});

// Rutas para vales de devolución
Route::prefix('admin/compras/devoluciones')->name('admin.devoluciones.')->middleware(['auth', 'admin.access', 'permission:compras'])->group(function () {
    Route::get('/buscar-productos', [\App\Http\Controllers\Admin\Compras\DevolucionController::class, 'buscarProductos'])->name('buscar-productos');
    Route::get('/', [\App\Http\Controllers\Admin\Compras\DevolucionController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\Compras\DevolucionController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\Compras\DevolucionController::class, 'store'])->name('store');
    Route::get('/{devolucion}', [\App\Http\Controllers\Admin\Compras\DevolucionController::class, 'show'])->name('show');
    Route::get('/{devolucion}/edit', [\App\Http\Controllers\Admin\Compras\DevolucionController::class, 'edit'])->name('edit');
    Route::put('/{devolucion}', [\App\Http\Controllers\Admin\Compras\DevolucionController::class, 'update'])->name('update');
    Route::delete('/{devolucion}', [\App\Http\Controllers\Admin\Compras\DevolucionController::class, 'destroy'])->name('destroy');
});