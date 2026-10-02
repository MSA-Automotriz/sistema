<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Ventas\CotizacionController;
use App\Http\Controllers\Admin\Ventas\POSController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['web', 'auth', \App\Http\Middleware\CheckAdminAccess::class, 'permission:ventas'])->group(function () {
    Route::get('/search/clientes', [CotizacionController::class, 'buscarClientesAjax']);
    Route::get('/repuestos/search', [CotizacionController::class, 'buscarRepuestos']);

    // Rutas para vehículos
    Route::get('/cotizaciones/marcas/search', [CotizacionController::class, 'searchMarcas']);
    Route::get('/cotizaciones/marcas/{marcaId}/modelos', [CotizacionController::class, 'searchModelos']);
    Route::get('/cotizaciones/modelos/{modeloId}/versiones', [CotizacionController::class, 'searchVersiones']);
    Route::get('/cotizaciones/versiones/{versionId}/anios', [CotizacionController::class, 'searchAnios']);
    Route::get('/cotizaciones/colores/search', [CotizacionController::class, 'searchColores']);

    Route::prefix('ventas/pos')->group(function() {
        Route::post('/procesar', [POSController::class, 'procesarVenta']);
        Route::get('/items-populares', [POSController::class, 'itemsPopulares']);
        Route::get('/buscar-partes', [POSController::class, 'buscarPartes']);
        Route::get('/buscar-servicios', [POSController::class, 'buscarServicios']);
        Route::get('/buscar-clientes', [POSController::class, 'buscarClientes']);
        Route::post('/crear-cliente', [POSController::class, 'crearCliente']);
        Route::get('/get-stock-parte', [POSController::class, 'getStockParte']);
    });
});