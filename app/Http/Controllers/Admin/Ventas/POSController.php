<?php

namespace App\Http\Controllers\Admin\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Parte;
use App\Models\Cliente;
use App\Models\Cotizacion;
use App\Models\DetalleCotizacion;
use App\Models\CategoriasPartes;
use App\Models\EstadoCotizacion;
use App\Models\Almacen;
use App\Models\Inventario;
use App\Models\Movimiento;
use App\Models\TipoMovimiento;
use App\Models\HistorialCotizacion;
use App\Models\Telefono;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\RequerimientoCompra;
use App\Models\DetalleRequerimiento;
use App\Models\EstadoRequerimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class POSController extends Controller
{
    /**
     * Muestra la vista principal del punto de venta
     */
public function index()
    {
        try {
            // Lógica específica para el terminal POS
            $almacenes = Almacen::where('activo', true)->get();
            $clientes = Cliente::limit(10)->get(); // Clientes frecuentes
            
            return view('admin.ventas.pos.index', compact('almacenes', 'clientes'));
            
        } catch (\Exception $e) {
            Log::error('Error al cargar POS: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar el punto de venta');
        }
    }

    /**
     * Vista de gestión/listado de ventas con estadísticas y filtros avanzados
     * Ruta: admin.ventas.index 
     * Vista: resources/views/admin/ventas/index.blade.php
     */
    public function ventas(Request $request)
    {
        try {
            Log::info('🔍 VENTAS: Accediendo al dashboard de ventas', [
                'url' => $request->url(),
                'method' => $request->method(),
                'is_ajax' => $request->ajax(),
                'wants_json' => $request->wantsJson(),
                'headers' => $request->headers->all(),
                'params' => $request->all()
            ]);

            // Si es petición AJAX, devolver JSON
            if ($request->ajax() || $request->wantsJson()) {
                Log::info('🔍 VENTAS: Es petición AJAX, llamando obtenerVentasAjax');
                return $this->obtenerVentasAjax($request);
            }

            // Obtener clientes para el filtro
            $clientes = Cliente::select('id', 'nombres', 'apellido_paterno', 'documento_identidad')
                              ->orderBy('nombres')
                              ->limit(100)
                              ->get();

            // Obtener almacenes
            $almacenes = Almacen::where('activo', true)->get();
            
            Log::info('Dashboard de ventas cargado exitosamente', [
                'clientes_count' => $clientes->count(),
                'almacenes_count' => $almacenes->count()
            ]);

            return view('admin.ventas.pos.ventas', compact('clientes', 'almacenes'));

        } catch (\Exception $e) {
            Log::error('Error al cargar vista de ventas: ' . $e->getMessage(), [
                'exception' => $e,
                'url' => $request->url(),
                'method' => $request->method()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al cargar las ventas'
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error al cargar las ventas');
        }
    }

    /**
     * Método auxiliar para obtener ventas vía AJAX
     */
    private function obtenerVentasAjax(Request $request)
    {
        Log::info('🔍 AJAX: Iniciando obtenerVentasAjax', [
            'params' => $request->all()
        ]);

        $query = Venta::with(['cliente', 'usuario', 'almacen', 'pagos']);

        Log::info('🔍 AJAX: Consulta inicial creada');

        // Aplicar filtros
        $this->aplicarFiltrosVentas($query, $request);

        Log::info('🔍 AJAX: Filtros aplicados');
        
        // Si se solicitan estadísticas
        if ($request->filled('estadisticas')) {
            return response()->json([
                'success' => true,
                'estadisticas' => $this->calcularEstadisticas($request)
            ]);
        }
        
        // Obtener ventas paginadas
        $ventas = $query->orderBy('fecha', 'desc')
                       ->orderBy('id', 'desc')
                       ->paginate(20);

        Log::info('🔍 AJAX: Ventas obtenidas', [
            'total' => $ventas->total(),
            'count' => $ventas->count(),
            'current_page' => $ventas->currentPage()
        ]);

        // Agregar información calculada
        $ventas->getCollection()->transform(function ($venta) {
            $venta->esta_vencida = $venta->estaVencida();
            $venta->dias_vencimiento = $venta->diasVencimiento();
            return $venta;
        });

        // Formatear las ventas para el frontend
        $ventasFormateadas = $ventas->getCollection()->map(function($venta) {
            $clienteNombre = '';
            $clienteDocumento = '';

            if ($venta->cliente) {
                if ($venta->cliente->tipo_cliente == 'natural') {
                    $clienteNombre = trim(
                        ($venta->cliente->nombres ?? '') . ' ' .
                        ($venta->cliente->apellido_paterno ?? '') . ' ' .
                        ($venta->cliente->apellido_materno ?? '')
                    );
                } else {
                    $clienteNombre = $venta->cliente->razon_social ?? 'Cliente corporativo';
                }
                $clienteDocumento = $venta->cliente->documento_identidad ?? 'Sin documento';
            } else {
                $clienteNombre = 'Cliente no especificado';
                $clienteDocumento = 'N/A';
            }

            // Mapear estados de la base de datos a estados del frontend
            $estadoMapeado = $this->mapearEstadoVenta($venta->estado);

            return [
                'id' => $venta->id,
                'codigo' => $venta->codigo,
                'fecha' => $venta->fecha,
                'cliente_nombre' => $clienteNombre,
                'cliente_documento' => $clienteDocumento,
                'usuario_nombre' => $venta->usuario->name ?? 'N/A',
                'almacen_nombre' => $venta->almacen->nombre ?? 'N/A',
                'total' => $venta->total,
                'monto_abonado' => $venta->monto_abonado ?? 0,
                'saldo_pendiente' => $venta->saldo_pendiente ?? 0,
                'porcentaje_abonado' => $venta->porcentaje_abonado ?? 0,
                'estado' => $estadoMapeado,
                'moneda' => $venta->moneda,
                'cotizacion_codigo' => $venta->cotizacion_codigo ?? null
            ];
        });

        // Información de paginación para el frontend
        $pagination = [
            'current_page' => $ventas->currentPage(),
            'last_page' => $ventas->lastPage(),
            'per_page' => $ventas->perPage(),
            'total' => $ventas->total(),
            'from' => $ventas->firstItem(),
            'to' => $ventas->lastItem()
        ];

        // Calcular resumen
        $resumen = $this->calcularResumenVentas($request);

        Log::info('🔍 AJAX: Respuesta final preparada', [
            'ventas_count' => count($ventasFormateadas),
            'pagination' => $pagination,
            'resumen' => $resumen
        ]);

        return response()->json([
            'success' => true,
            'ventas' => $ventasFormateadas,
            'pagination' => $pagination,
            'resumen' => $resumen
        ]);
    }

    /**
     * Mapear estados de la base de datos a estados del frontend
     */
    private function mapearEstadoVenta($estadoBD)
    {
        switch (strtolower($estadoBD)) {
            case 'despachado':
                return 'Completada';

            case 'pagado':
                return 'Pagado';

            case 'pendiente':
                return 'Pendiente';

            case 'pendiente_stock':
                return 'Pendiente Stock';

            case 'en_compra':
                return 'En Compra';

            case 'listo_entrega':
                return 'Listo Entrega';

            case 'no_pagado':
                return 'No Pagado';

            case 'en_cotizacion':
                return 'En Cotización';

            case 'para_importacion':
                return 'Para Importación';

            case 'pedido_especial':
                return 'Pedido Especial';

            case 'parcial':
            case 'abonado':
                return 'Parcial';

            case 'anulada':
            case 'anulado':
            case 'cancelada':
            case 'cancelado':
                return 'Cancelada';

            default:
                // Para estados no reconocidos, mostrar el estado original capitalizado
                return ucfirst(str_replace('_', ' ', $estadoBD));
        }
    }

    /**
     * Calcular resumen de ventas
     */
    private function calcularResumenVentas(Request $request)
    {
        $query = Venta::query();

        // Aplicar los mismos filtros que en la consulta principal
        $this->aplicarFiltrosVentas($query, $request);

        $totalVentas = $query->count();
        $montoTotal = $query->sum('total');
        $ventasParciales = $query->where('estado', 'Parcial')->count();
        $saldoPendiente = $query->sum('saldo_pendiente');

        return [
            'total_ventas' => $totalVentas,
            'monto_total' => $montoTotal,
            'ventas_parciales' => $ventasParciales,
            'saldo_pendiente' => $saldoPendiente
        ];
    }

    /**
     * Aplicar filtros a la consulta de ventas
     */
    private function aplicarFiltrosVentas($query, Request $request)
    {
        // Filtros de búsqueda
        if ($request->filled('buscar') || $request->filled('search')) {
            $search = $request->buscar ?: $request->search;
            $query->where(function($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhere('numero_factura', 'like', "%{$search}%")
                  ->orWhereHas('cliente', function($clienteQuery) use ($search) {
                      $clienteQuery->where('nombres', 'like', "%{$search}%")
                                  ->orWhere('apellido_paterno', 'like', "%{$search}%")
                                  ->orWhere('apellido_materno', 'like', "%{$search}%")
                                  ->orWhere('razon_social', 'like', "%{$search}%")
                                  ->orWhere('documento_identidad', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filtro por fecha
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }
        
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }
        
        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        
        // Filtro por cliente
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }
        
        // Filtro por almacén
        if ($request->filled('almacen_id')) {
            $query->where('almacen_id', $request->almacen_id);
        }
        
        // Filtro por moneda
        if ($request->filled('moneda')) {
            $query->where('moneda', $request->moneda);
        }
        
        // Filtro por tipo de pago
        if ($request->filled('tipo_pago')) {
            $query->where('tipo_pago', $request->tipo_pago);
        }
        
        // Filtros especiales
        if ($request->filled('vencidas')) {
            if ($request->vencidas === '1') {
                $query->vencidas();
            } elseif ($request->vencidas === '0') {
                $query->proximasVencer();
            }
        }
        
        // Filtro por prioridad
        if ($request->filled('prioridad')) {
            $query->porPrioridad($request->prioridad);
        }
    }
    
    /**
     * Calcular estadísticas para el dashboard
     */
    private function calcularEstadisticas(Request $request)
    {
        $baseQuery = Venta::query();
        
        // Aplicar los mismos filtros que la consulta principal (excepto paginación)
        $this->aplicarFiltrosVentas($baseQuery, $request);
        
        $stats = [
            'total' => $baseQuery->count(),
            'pagadas' => $baseQuery->clone()->where('estado', 'pagado')->count(),
            'pendientes' => $baseQuery->clone()->where('estado', 'no_pagado')->count(),
            'vencidas' => $baseQuery->clone()->vencidas()->count(),
            'monto_pagadas' => 'S/ ' . number_format($baseQuery->clone()->where('estado', 'pagado')->sum('total'), 2),
            'monto_pendientes' => 'S/ ' . number_format($baseQuery->clone()->where('estado', 'no_pagado')->sum('saldo_pendiente'), 2),
            'monto_vencidas' => 'S/ ' . number_format($baseQuery->clone()->vencidas()->sum('saldo_pendiente'), 2)
        ];
        
        return $stats;
    }

    public function show($id)
    {
        try {
            $venta = Venta::with([
                'cliente.telefonos',
                'usuario', 
                'almacen', 
                'cotizacion',
                'detallesPOS.parte',
                'detallesPOS.parte' // Para ventas del POS
            ])->findOrFail($id);
            
            // Determinar qué detallesPOS usar (normal o POS)
            $detallesPOS = $venta->detallesPOS->isNotEmpty() 
                ? $venta->detallesPOS 
                : $venta->detallesPOS;
            
            // Formatear datos del cliente
            $clienteData = $this->formatearCliente($venta->cliente);
            
            // Calcular totales y estadísticas
            $estadisticasVenta = $this->calcularEstadisticasVenta($venta, $detallesPOS);
            
            // Historial de pagos si existe la tabla
            $historialPagos = $this->obtenerHistorialPagos($venta->id);
            
            return view('admin.ventas.show', compact(
                'venta', 
                'detallesPOS', 
                'clienteData', 
                'estadisticasVenta',
                'historialPagos'
            ));
            
        } catch (\Exception $e) {
            Log::error('Error al mostrar venta: ' . $e->getMessage());
            return redirect()->route('admin.ventas.index')
                ->with('error', 'Venta no encontrada');
        }
    }
    /**
     * Buscar partes por nombre, código o categoría (versión ultra-robusta)
     * Modificado para soportar búsqueda en todos los almacenes
     */
    public function buscarPartes(Request $request)
    {
        try {
            $query = trim($request->input('query', ''));
            $categoriaId = $request->input('categoria_id');
            $almacenId = $request->input('almacen_id');
            $incluirSinStock = $request->boolean('incluir_sin_stock', true);
            
            // Si no hay query, usar obtenerTodasPartes
            if (empty($query)) {
                return $this->obtenerTodasPartes($request);
            }
            
            if (strlen($query) < 2) {
                return response()->json([
                    'items' => [],
                    'message' => 'Ingrese al menos 2 caracteres para buscar'
                ]);
            }
            
            // Verificar qué columnas existen
            $columnasPartes = Schema::getColumnListing('partes');
            $tieneInventarios = Schema::hasTable('inventarios');
            $columnasInventarios = $tieneInventarios ? Schema::getColumnListing('inventarios') : [];
            $tieneUnidades = Schema::hasTable('unidades');
            $tieneCategorias = Schema::hasTable('categorias_partes');
            $tieneAlmacenes = Schema::hasTable('almacenes');
            
            // Construir SELECT dinámico
            $selectFields = ['partes.id'];
            
            foreach (['codigo', 'nombre', 'precio_venta', 'moneda_venta', 'marca', 'codigo_oem', 'imagen', 'categoria_parte_id'] as $campo) {
                if (in_array($campo, $columnasPartes)) {
                    $selectFields[] = "partes.$campo";
                }
            }
            
            // Construir consulta
            $partesQuery = DB::table('partes')->select($selectFields);
            
            // Joins opcionales
            if ($tieneUnidades && in_array('unidad_id', $columnasPartes)) {
                $partesQuery->leftJoin('unidades', 'partes.unidad_id', '=', 'unidades.id')
                            ->addSelect('unidades.nombre as unidad_nombre');
            }
            
            if ($tieneCategorias && in_array('categoria_parte_id', $columnasPartes)) {
                $partesQuery->leftJoin('categorias_partes', 'partes.categoria_parte_id', '=', 'categorias_partes.id')
                            ->addSelect('categorias_partes.nombre as categoria_nombre');
            }
            
            // Join con inventarios (modificado para todos los almacenes)
            if ($tieneInventarios) {
                if ($almacenId) {
                    // Almacén específico
                    $partesQuery->leftJoin('inventarios', function($join) use ($almacenId) {
                        $join->on('partes.id', '=', 'inventarios.parte_id')
                             ->where('inventarios.almacen_id', '=', $almacenId);
                    });
                    
                    if (in_array('stock_disponible', $columnasInventarios)) {
                        $partesQuery->addSelect('inventarios.stock_disponible');
                    }
                    if (in_array('stock_real', $columnasInventarios)) {
                        $partesQuery->addSelect('inventarios.stock_real');
                    }
                } else {
                    // Todos los almacenes
                    $partesQuery->leftJoin('inventarios', 'partes.id', '=', 'inventarios.parte_id');
                    
                    if (in_array('stock_disponible', $columnasInventarios)) {
                        $partesQuery->addSelect(DB::raw('COALESCE(SUM(inventarios.stock_disponible), 0) as stock_disponible'));
                    }
                    if (in_array('stock_real', $columnasInventarios)) {
                        $partesQuery->addSelect(DB::raw('COALESCE(SUM(inventarios.stock_real), 0) as stock_real'));
                    }
                    
                    // Información de almacenes
                    if ($tieneAlmacenes) {
                        $partesQuery->leftJoin('almacenes', 'inventarios.almacen_id', '=', 'almacenes.id')
                                   ->addSelect(DB::raw('GROUP_CONCAT(DISTINCT almacenes.nombre SEPARATOR ", ") as almacenes_nombres'));
                    }
                    
                    // Agrupar
                    $partesQuery->groupBy(array_merge($selectFields, $tieneUnidades ? ['unidades.nombre'] : [], $tieneCategorias ? ['categorias_partes.nombre'] : []));
                }
            }
            
            // Construir WHERE de búsqueda dinámicamente
            $partesQuery->where(function($q) use ($query, $columnasPartes) {
                $searchTerm = '%' . $query . '%';
                
                // Buscar en nombre (siempre debería existir)
                if (in_array('nombre', $columnasPartes)) {
                    $q->where('partes.nombre', 'like', $searchTerm);
                }
                
                // Buscar en código
                if (in_array('codigo', $columnasPartes)) {
                    $q->orWhere('partes.codigo', 'like', $searchTerm);
                }
                
                // Buscar en marca
                if (in_array('marca', $columnasPartes)) {
                    $q->orWhere('partes.marca', 'like', $searchTerm);
                }
                
                // Buscar en código OEM
                if (in_array('codigo_oem', $columnasPartes)) {
                    $q->orWhere('partes.codigo_oem', 'like', $searchTerm);
                }
            });
            
            // Filtros adicionales
            if ($categoriaId && in_array('categoria_parte_id', $columnasPartes)) {
                $partesQuery->where('partes.categoria_parte_id', $categoriaId);
            }
            
            // Filtrar por stock
            if (!$incluirSinStock && $tieneInventarios && in_array('stock_disponible', $columnasInventarios)) {
                if ($almacenId) {
                    $partesQuery->where('inventarios.stock_disponible', '>', 0);
                } else {
                    $partesQuery->havingRaw('COALESCE(SUM(inventarios.stock_disponible), 0) > 0');
                }
            }
            
            // Ordenar por relevancia (solo si las columnas existen)
            $orderCases = [];
            if (in_array('codigo', $columnasPartes)) {
                $orderCases[] = "WHEN partes.codigo = ? THEN 1";
                $orderCases[] = "WHEN partes.codigo LIKE ? THEN 2";
            }
            if (in_array('nombre', $columnasPartes)) {
                $orderCases[] = "WHEN partes.nombre LIKE ? THEN 3";
            }
            $orderCases[] = "ELSE 4";
            
            if (!empty($orderCases)) {
                $orderSql = 'CASE ' . implode(' ', $orderCases) . ' END';
                $orderParams = [];
                
                if (in_array('codigo', $columnasPartes)) {
                    $orderParams[] = $query;
                    $orderParams[] = $query . '%';
                }
                if (in_array('nombre', $columnasPartes)) {
                    $orderParams[] = $query . '%';
                }
                
                $partesQuery->orderByRaw($orderSql, $orderParams);
            }
            
            $partesQuery->orderBy('partes.nombre');
            $partes = $partesQuery->take(50)->get();
            
            // Formatear resultados
            $resultado = $partes->map(function($parte) use ($almacenId) {
                $stockDisponible = isset($parte->stock_disponible) ? $parte->stock_disponible : 0;
                
                return [
                    'id' => $parte->id,
                    'codigo' => $parte->codigo ?? 'SIN-CODIGO',
                    'nombre' => $parte->nombre ?? 'Sin nombre',
                    'descripcion' => '', // No disponible en tu estructura
                    'precio' => $parte->precio_venta ?? 0,
                    'moneda' => $parte->moneda_venta ?? 'SOL',
                    'unidad' => $parte->unidad_nombre ?? 'Unidad',
                    'categoria' => $parte->categoria_nombre ?? 'Sin categoría',
                    'categoria_id' => $parte->categoria_parte_id ?? null,
                    'stock_disponible' => $stockDisponible,
                    'tipo' => 'parte',
                    'tiene_stock' => $stockDisponible > 0,
                    'marca' => $parte->marca ?? '',
                    'codigo_oem' => $parte->codigo_oem ?? '',
                    'imagen' => $parte->imagen ?? null,
                    'almacen_nombre' => $almacenId ? null : ($parte->almacenes_nombres ?? 'Multiple')
                ];
            });
            
            return response()->json([
                'items' => $resultado,
                'query' => $query,
                'total_found' => $partes->count(),
                'almacen_info' => $almacenId ? 'Almacén específico' : 'Todos los almacenes'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en buscarPartes: ' . $e->getMessage(), [
                'query' => $request->input('query'),
                'almacen_id' => $request->input('almacen_id'),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => true,
                'message' => 'Error interno en la búsqueda de partes'
            ], 500);
        }
    }

    /**
     * Buscar clientes por nombre o documento (versión robusta)
     */
    public function buscarClientes(Request $request)
    {
        try {
            $query = trim($request->input('query', ''));
            
            $clientesQuery = Cliente::with('telefonos');
            
            if (!empty($query)) {
                if (strlen($query) < 2) {
                    return response()->json([
                        'items' => [],
                        'message' => 'Ingrese al menos 2 caracteres para buscar'
                    ]);
                }
                
                $clientesQuery->where(function($q) use ($query) {
                    $searchTerm = '%' . $query . '%';
                    $q->where('documento_identidad', 'like', $searchTerm)
                      ->orWhere('nombres', 'like', $searchTerm)
                      ->orWhere('apellido_paterno', 'like', $searchTerm)
                      ->orWhere('apellido_materno', 'like', $searchTerm)
                      ->orWhere('razon_social', 'like', $searchTerm);
                      
                    // Solo buscar por correo si la columna existe
                    if (Schema::hasColumn('clientes', 'correo')) {
                        $q->orWhere('correo', 'like', $searchTerm);
                    }
                });
                
                // Ordenar por relevancia cuando hay búsqueda
                $clientesQuery->orderByRaw('
                    CASE 
                        WHEN documento_identidad = ? THEN 1
                        WHEN documento_identidad LIKE ? THEN 2
                        WHEN nombres LIKE ? OR razon_social LIKE ? THEN 3
                        WHEN CONCAT(COALESCE(nombres, ""), " ", COALESCE(apellido_paterno, ""), " ", COALESCE(apellido_materno, "")) LIKE ? THEN 4
                        ELSE 5
                    END
                ', [$query, $query . '%', $query . '%', $query . '%', '%' . $query . '%']);
            } else {
                // Sin query, devolver clientes recientes
                $clientesQuery->orderBy('updated_at', 'desc');
            }
            
            // Limitar resultados
            $limite = $request->input('limite', 20);
            $clientes = $clientesQuery->take($limite)->get();
            
            $resultado = $clientes->map(function($cliente) {
                $nombre = $cliente->tipo_cliente == 'natural' 
                    ? trim(($cliente->nombres ?? '') . ' ' . ($cliente->apellido_paterno ?? '') . ' ' . ($cliente->apellido_materno ?? ''))
                    : ($cliente->razon_social ?? 'Sin nombre');
                
                // Limpiar nombre vacío
                if (trim($nombre) === '') {
                    $nombre = 'Cliente sin nombre';
                }
                
                return [
                    'id' => $cliente->id,
                    'documento' => $cliente->documento_identidad ?? 'Sin documento',
                    'nombre' => $nombre,
                    'tipo' => $cliente->tipo_cliente ?? 'natural',
                    'tipo_documento' => $cliente->tipo_documento ?? 'DNI',
                    'telefono' => $cliente->telefonos->first() ? $cliente->telefonos->first()->numero : null,
                    'correo' => Schema::hasColumn('clientes', 'correo') ? $cliente->correo : null,
                    'direccion' => Schema::hasColumn('clientes', 'direccion') ? $cliente->direccion : null,
                    'activo' => Schema::hasColumn('clientes', 'activo') ? ($cliente->activo ?? true) : true
                ];
            });
            
            return response()->json([
                'items' => $resultado,
                'query' => $query,
                'total_found' => $resultado->count(),
                'is_search' => !empty($query)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en buscarClientes: ' . $e->getMessage(), [
                'query' => $request->input('query'),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => true,
                'message' => 'Error en la búsqueda de clientes'
            ], 500);
        }
    }

    /**
     * Obtener información detallada de stock de una parte
     */
    public function getStockParte(Request $request)
    {
        try {
            $parteId = $request->input('parte_id');
            $almacenId = $request->input('almacen_id');
            
            if (!$parteId || !$almacenId) {
                return response()->json([
                    'error' => true,
                    'message' => 'Parámetros requeridos faltantes'
                ], 400);
            }
            
            $inventario = Inventario::where('parte_id', $parteId)
                ->where('almacen_id', $almacenId)
                ->first();
            
            $parte = Parte::with(['unidad', 'categoriaParte'])->find($parteId);
            
            if (!$parte) {
                return response()->json([
                    'error' => true,
                    'message' => 'Parte no encontrada'
                ], 404);
            }
            
            $stockDisponible = $inventario ? $inventario->stock_disponible : 0;
            $stockReal = $inventario ? $inventario->stock_real : 0;
            $stockReservado = $inventario ? $inventario->stock_reservado : 0;
            
            return response()->json([
                'stock' => $stockDisponible,
                'stock_real' => $stockReal,
                'stock_reservado' => $stockReservado,
                'stock_disponible' => $stockDisponible,
                'tiene_stock' => $stockDisponible > 0,
                'parte' => [
                    'id' => $parte->id,
                    'nombre' => $parte->nombre,
                    'codigo' => $parte->codigo,
                    'unidad' => $parte->unidad ? $parte->unidad->nombre : 'N/A',
                    'categoria' => $parte->categoriaParte ? $parte->categoriaParte->nombre : 'Sin categoría'
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en getStockParte: ' . $e->getMessage());
            
            return response()->json([
                'error' => true,
                'message' => 'Error al obtener información de stock'
            ], 500);
        }
    }

    /**
     * Obtener partes populares (sin servicios)
     */
    public function itemsPopulares(Request $request)
    {
        try {
            $almacenId = $request->input('almacen_id');
            $limite = $request->input('limite', 8);
            
            // Obtener partes más vendidas con stock
            $partesPopulares = collect();
            
            if (Schema::hasTable('detallesPOS_venta')) {
                $partesPopulares = DB::table('partes')
                    ->select([
                        'partes.id',
                        'partes.nombre',
                        'partes.codigo',
                        'partes.precio_venta',
                        'partes.moneda_venta',
                        'inventarios.stock_disponible',
                        DB::raw('COALESCE(SUM(detallesPOS_venta.cantidad), 0) as total_vendido'),
                        DB::raw('"parte" as tipo')
                    ])
                    ->leftJoin('detallesPOS_venta', 'partes.id', '=', 'detallesPOS_venta.parte_id')
                    ->leftJoin('inventarios', function($join) use ($almacenId) {
                        $join->on('partes.id', '=', 'inventarios.parte_id');
                        if ($almacenId) {
                            $join->where('inventarios.almacen_id', '=', $almacenId);
                        }
                    })
                    ->whereNotNull('partes.precio_venta')
                    ->where('partes.precio_venta', '>', 0)
                    ->groupBy([
                        'partes.id', 'partes.nombre', 'partes.codigo', 
                        'partes.precio_venta', 'partes.moneda_venta', 'inventarios.stock_disponible'
                    ])
                    ->orderByDesc('total_vendido')
                    ->orderByDesc('inventarios.stock_disponible')
                    ->take($limite)
                    ->get();
            }
            
            // Si no hay datos de ventas, obtener partes aleatorias
            if ($partesPopulares->isEmpty()) {
                $partesPopulares = DB::table('partes')
                    ->select([
                        'partes.id',
                        'partes.nombre',
                        'partes.codigo',
                        'partes.precio_venta',
                        'partes.moneda_venta',
                        'inventarios.stock_disponible',
                        DB::raw('0 as total_vendido'),
                        DB::raw('"parte" as tipo')
                    ])
                    ->leftJoin('inventarios', function($join) use ($almacenId) {
                        $join->on('partes.id', '=', 'inventarios.parte_id');
                        if ($almacenId) {
                            $join->where('inventarios.almacen_id', '=', $almacenId);
                        }
                    })
                    ->whereNotNull('partes.precio_venta')
                    ->where('partes.precio_venta', '>', 0)
                    ->orderByDesc('inventarios.stock_disponible')
                    ->orderBy('partes.nombre')
                    ->take($limite)
                    ->get();
            }
            
            // Formatear resultados
            $itemsPopulares = $partesPopulares->map(function($parte) {
                return [
                    'id' => $parte->id,
                    'tipo' => 'parte',
                    'nombre' => $parte->nombre,
                    'codigo' => $parte->codigo,
                    'precio' => $parte->precio_venta,
                    'moneda' => $parte->moneda_venta ?? 'SOL',
                    'stock_disponible' => $parte->stock_disponible ?? 0,
                    'tiene_stock' => ($parte->stock_disponible ?? 0) > 0,
                    'total_vendido' => $parte->total_vendido,
                    'unidad' => 'Unidad'
                ];
            });
            
            return response()->json($itemsPopulares);
            
        } catch (\Exception $e) {
            Log::error('Error en itemsPopulares: ' . $e->getMessage());
            
            return response()->json([
                'error' => true,
                'message' => 'Error al obtener items populares'
            ], 500);
        }
    }

/**
 * Procesar la venta/cotización (VERSIÓN COMPLETAMENTE SIN VEHÍCULOS)
 */
public function procesarVenta(Request $request)
{
    // Validación de datos de entrada (simplificada sin referencias a vehículos)
    $validator = Validator::make($request->all(), [
        'items' => 'required|array|min:1',
        'items.*.id' => 'required',
        'items.*.tipo' => 'required|in:parte',
        'items.*.cantidad' => 'required|numeric|min:1',
        'items.*.precio' => 'required|numeric|min:0',
        'items.*.almacen_id' => 'nullable|exists:almacenes,id',
        'items.*.descuento' => 'nullable|numeric|min:0|max:100',
        'moneda' => 'required|in:Soles,Dólares',
        'condicion' => 'required|in:Nuevo,Usado',
        'forma_pago' => 'required|in:Contado,Crédito',
        'porcentaje_abono' => 'required_if:forma_pago,Crédito|nullable|numeric|min:0|max:100',
        'generar_requerimiento' => 'nullable|boolean',
        'datos_adicionales' => 'nullable|string|max:500',
        'tipo_documento' => 'required|in:Boleta,Factura,Ticket',
        'cliente_id' => 'nullable|exists:clientes,id'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Error de validación',
            'errors' => $validator->errors()
        ], 422);
    }

    DB::beginTransaction();
    try {
        // Obtener o crear cliente por defecto si no se especifica
        $clienteId = $request->cliente_id;
        if (!$clienteId) {
            $clienteGeneral = Cliente::firstOrCreate(
                ['documento_identidad' => '00000000'],
                [
                    'tipo_cliente' => 'natural',
                    'tipo_documento' => 'DNI',
                    'nombres' => 'Cliente',
                    'apellido_paterno' => 'General',
                    'activo' => true
                ]
            );
            $clienteId = $clienteGeneral->id;
        }

        // Obtener almacén principal (sin filtro de vehículos)
        $almacenPrincipal = $request->almacen_id;
        if (!$almacenPrincipal) {
            $almacen = Almacen::orderBy('id')->first();
            
            if (!$almacen) {
                throw new \Exception('No hay almacenes disponibles');
            }
            
            $almacenPrincipal = $almacen->id;
        }

        // Estado inicial configurado directamente en el modelo Venta

        // Crear venta directamente (sin cotización intermedia)
        $venta = new Venta();
        $venta->codigo = $this->generarCodigoVenta();
        $venta->fecha = now();
        $venta->cliente_id = $clienteId;
        $venta->usuario_id = Auth::id();
        $venta->almacen_id = $almacenPrincipal;
        $venta->moneda = $request->moneda;
        $venta->tipo_pago = $request->forma_pago;

        // Obtener tipo de cambio si existe
        $tipoCambio = null;
        if (class_exists('App\Models\TipoCambio')) {
            $tipoCambio = \App\Models\TipoCambio::obtenerActual();
            $venta->tipo_cambio_usado = $tipoCambio ? $tipoCambio->venta : null;
        }

        // Configurar estado inicial (será ajustado después según stock)
        if ($request->forma_pago === 'Contado') {
            $venta->estado = 'pendiente'; // Temporal, se ajustará según stock
            $venta->monto_abonado = 0; // Se calculará después
            $venta->saldo_pendiente = 0;
        } else {
            $venta->estado = 'no_pagado';
            $porcentajeAbono = $request->porcentaje_abono ?? 30;
            $venta->monto_abonado = 0; // Se calculará después
            $venta->saldo_pendiente = 0; // Se calculará después
            $venta->fecha_vencimiento = now()->addDays(30);
        }

        // Configurar observaciones
        $venta->observaciones = ($request->datos_adicionales ?? 'Venta creada desde POS') .
                               "\nTipo de documento: " . $request->tipo_documento;

        // Guardar venta inicial (para tener ID disponible)
        $venta->subtotal = 0; // Se calculará después
        $venta->igv = 0;     // Se calculará después
        $venta->total = 0;   // Se calculará después
        $venta->save();

        Log::info("Venta creada: {$venta->codigo}");

        // Variables para el procesamiento
        $subtotal = 0;
        $requerimientosGenerados = [];
        $itemsSinStock = [];
        $itemsProcesados = [];

        foreach ($request->items as $index => $item) {
            try {
                $parte = Parte::findOrFail($item['id']);
                
                // Determinar almacén para este ítem
                $almacenItem = $item['almacen_id'] ?? $almacenPrincipal;
                $inventario = Inventario::where('parte_id', $item['id'])
                    ->where('almacen_id', $almacenItem)
                    ->first();

                $stockDisponible = $inventario ? $inventario->stock_disponible : 0;
                
                // VERIFICAR STOCK - VERSIÓN PERMISIVA
                if ($stockDisponible < $item['cantidad']) {
                    Log::warning("Stock insuficiente para {$parte->nombre}: solicitados {$item['cantidad']}, disponibles {$stockDisponible}");
                    
                    if ($request->generar_requerimiento) {
                        $requerimiento = $this->generarRequerimientoCompra($parte, $item, $almacenItem, $venta);
                        if ($requerimiento) {
                            $requerimientosGenerados[] = $requerimiento;
                        }
                        $itemsSinStock[] = "{$parte->nombre} (Solicitados: {$item['cantidad']}, Disponibles: {$stockDisponible})";
                    } else {
                        // NO LANZAR EXCEPCIÓN - PERMITIR VENTA SIN STOCK
                        $itemsSinStock[] = "{$parte->nombre} (Stock insuficiente: solicitados {$item['cantidad']}, disponibles {$stockDisponible})";
                        Log::info("Permitiendo venta sin stock para: {$parte->nombre}");
                    }
                }

                // Procesar el ítem - CORREGIDO: Solo descontar si hay stock suficiente
                if ($stockDisponible >= $item['cantidad'] && $inventario) {
                    $stockAnterior = $inventario->stock_disponible;
                    $inventario->decrement('stock_disponible', $item['cantidad']);
                    
                    $this->registrarMovimientoInventario(
                        $parte,
                        $almacenItem,
                        $item['cantidad'],
                        $stockAnterior,
                        $inventario->stock_disponible,
                        $venta
                    );
                } else {
                    Log::info("No se descuenta stock para {$parte->nombre} - stock insuficiente o sin inventario");
                }

                // Calcular totales del ítem - SIEMPRE procesar totales
                $descuento = $item['descuento'] ?? 0;
                $subtotalItem = $item['cantidad'] * $item['precio'];
                $totalItem = $subtotalItem * (1 - $descuento / 100);

                // Crear detalle de venta POS
                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'parte_id' => $item['id'],
                    'tipo_item' => 'parte',
                    'descripcion' => $parte->nombre,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'descuento' => $descuento,
                    'subtotal' => $subtotalItem,
                    'total' => $totalItem,
                ]);

                $subtotal += $totalItem;
                $itemsProcesados[] = $parte->nombre;

            } catch (\Exception $e) {
                Log::error("Error procesando item {$item['id']}: " . $e->getMessage());
                // Solo lanzar excepción si NO se permite generar requerimientos Y el error no es de stock
                if (!$request->generar_requerimiento && !str_contains($e->getMessage(), 'Stock insuficiente')) {
                    throw $e;
                }
                continue;
            }
        }

        // Validaciones finales - MODIFICADAS para ser más permisivas
        if (empty($itemsProcesados) && empty($itemsSinStock)) {
            throw new \Exception('No se pudo procesar ningún ítem');
        }

        // Permitir ventas con subtotal 0 si hay items sin stock
        if ($subtotal == 0 && empty($itemsSinStock)) {
            throw new \Exception('No se calculó ningún monto para la venta');
        }

        // Calcular totales finales
        $impuestos = $subtotal * 0.18;
        $total = $subtotal + $impuestos;

        // Calcular abono según forma de pago
        if ($request->forma_pago === 'Contado') {
            $abono = $total;
            $saldoPendiente = 0;
        } else {
            $porcentajeAbono = $request->porcentaje_abono ?? 30;
            $abono = $total * ($porcentajeAbono / 100);
            $saldoPendiente = $total - $abono;
        }

        // Actualizar venta con totales finales
        $venta->subtotal = $subtotal;
        $venta->igv = $impuestos;
        $venta->total = $total;
        $venta->monto_abonado = $abono;
        $venta->saldo_pendiente = $saldoPendiente;

        // Actualizar estado final según pago (se ajustará después según stock)
        if ($saldoPendiente <= 0) {
            $venta->estado = 'pagado'; // Temporal, se ajustará según stock
        }

        // Agregar información de items sin stock a observaciones
        if (!empty($itemsSinStock)) {
            $venta->observaciones .= "\n\nITEMS SIN STOCK:\n" . implode("\n", $itemsSinStock);
        }

        $venta->save();

        // Verificar stock y gestionar flujo de compra automático
        $resultadoStock = $venta->verificarYGestionarStock();

        DB::commit();

        // Preparar respuesta con información de redirección
        $response = [
            'success' => true,
            'venta_id' => $venta->id,
            'venta_codigo' => $venta->codigo,
            'stock_info' => $resultadoStock
        ];

        // Definir mensaje y redirección según el resultado
        if (empty($itemsSinStock)) {
            // Venta completa procesada
            $response['message'] = 'Venta procesada correctamente';
            $response['redirect'] = route('admin.ventas.pos.ventas');
        } else {
            // Venta con algunos items sin stock
            $response['message'] = 'Venta procesada. Algunos items requieren stock adicional';
            $response['items_sin_stock'] = $itemsSinStock;
            $response['redirect'] = route('admin.ventas.pos.ventas');
            if (!empty($requerimientosGenerados)) {
                $response['requerimientos_generados'] = $requerimientosGenerados;
            }
        }

        return response()->json($response);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error completo en procesarVenta: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'request_data' => $request->all()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error al procesar la venta: ' . $e->getMessage()
        ], 500);
    }
}

 /**
     * Generar código único para cotización - VERSIÓN TEMPORAL DIRECTA
     * REEMPLAZA COMPLETAMENTE EL MÉTODO ACTUAL generarCodigoCotizacion()
     */
    private function generarCodigoCotizacion()
    {
        return DB::transaction(function () {
            $año = date('Y');

            // Obtener el último número usando FOR UPDATE para evitar condiciones de carrera
            $ultimaCotizacion = DB::table('cotizaciones')
                ->where('codigo', 'like', "COT-{$año}%")
                ->orderByRaw('CAST(SUBSTRING(codigo, 9) AS UNSIGNED) DESC')
                ->lockForUpdate()
                ->first();

            if ($ultimaCotizacion) {
                // Extraer número: COT-20250005 -> 0005 -> 5
                $numeroActual = intval(substr($ultimaCotizacion->codigo, -4));
                $nuevoNumero = $numeroActual + 1;
            } else {
                $nuevoNumero = 1;
            }

            // Generar nuevo código
            $nuevoCodigo = 'COT-' . $año . str_pad($nuevoNumero, 4, '0', STR_PAD_LEFT);

            // Verificar una vez más que no existe (por seguridad)
            $existe = DB::table('cotizaciones')
                ->where('codigo', $nuevoCodigo)
                ->exists();

            if ($existe) {
                // Si aún existe, usar timestamp único
                $nuevoCodigo = 'COT-' . date('YmdHis') . '-' . rand(100, 999);
                Log::warning("Código duplicado detectado, usando timestamp: {$nuevoCodigo}");
            }

            Log::info("Código generado: {$nuevoCodigo}");
            return $nuevoCodigo;
        });
    }
    /**
     * Generar requerimiento de compra para items sin stock
     */
    private function generarRequerimientoCompra($parte, $item, $almacenId, $venta)
    {
        try {
            // Verificar si existe la tabla y modelo de requerimientos
            if (!Schema::hasTable('requerimientos_compra')) {
                Log::warning('Tabla requerimientos_compra no existe, saltando generación de requerimiento');
                return null;
            }
            
            // Obtener estado "Pendiente" para requerimientos
            $estadoPendiente = null;
            if (Schema::hasTable('estado_requerimientos')) {
                $estadoPendiente = DB::table('estado_requerimientos')
                    ->where('nombre', 'pendiente')
                    ->first();

                if (!$estadoPendiente) {
                    // Crear estado pendiente si no existe
                    $estadoPendienteId = DB::table('estado_requerimientos')
                        ->insertGetId([
                            'nombre' => 'pendiente',
                            'descripcion' => 'Requerimiento pendiente de atención',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    $estadoPendiente = (object)['id' => $estadoPendienteId];
                }
            } else {
                // Si no existe la tabla de estados, usar estado por defecto
                Log::warning('Tabla estado_requerimientos no existe, usando estado por defecto');
                $estadoPendiente = (object)['id' => 1]; // Estado por defecto
            }
            
            // Verificar que tenemos un estado válido
            if (!$estadoPendiente || !$estadoPendiente->id) {
                Log::error('No se pudo obtener un estado válido para el requerimiento de compra');
                return null;
            }

            // Crear requerimiento
            $requerimientoId = DB::table('requerimientos_compra')->insertGetId([
                'codigo' => 'REQ-' . date('ymdHi') . rand(100, 999),
                'fecha' => now(),
                'almacen_id' => $almacenId,
                'estado_id' => $estadoPendiente->id,
                'prioridad' => 'Alta',
                'comentario' => "Generado automáticamente por venta POS (Venta: {$venta->codigo})",
                'user_id' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Agregar detalle del requerimiento si existe la tabla
            if (Schema::hasTable('detallesPOS_requerimiento')) {
                DB::table('detallesPOS_requerimiento')->insert([
                    'requerimiento_id' => $requerimientoId,
                    'parte_id' => $parte->id,
                    'cantidad_solicitada' => $item['cantidad'],
                    'cantidad_aprobada' => $item['cantidad'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            return [
                'id' => $requerimientoId,
                'codigo' => 'REQ-' . date('ymdHi') . rand(100, 999),
                'parte_id' => $parte->id,
                'parte_nombre' => $parte->nombre,
                'cantidad' => $item['cantidad']
            ];
            
        } catch (\Exception $e) {
            Log::error('Error al generar requerimiento de compra: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Registrar movimiento de inventario
     */
    private function registrarMovimientoInventario($parte, $almacenId, $cantidad, $stockAnterior, $stockResultante, $venta)
    {
        try {
            // Obtener o crear tipo de movimiento
            $tipoMovimiento = TipoMovimiento::where('nombre', 'Venta POS')->first();
            if (!$tipoMovimiento) {
                $tipoMovimiento = TipoMovimiento::create([
                    'nombre' => 'Venta POS',
                    'operacion' => 'salida',
                    'afecta_stock' => 1,
                    'descripcion' => 'Venta generada desde el Punto de Venta',
                ]);
            }
            
            // Datos para movimiento según tu estructura real
            $datosMovimiento = [
                'tipo_movimiento_id' => $tipoMovimiento->id,
                'parte_id' => $parte->id,
                'almacen_id' => $almacenId,
                'cantidad' => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_resultante' => $stockResultante,
                'documento_referencia' => $venta->codigo,
                'fecha_movimiento' => now(),
                'usuario_id' => Auth::id(),
                'observaciones' => "Venta generada desde POS. Venta: {$venta->codigo}",
            ];
            
            Movimiento::create($datosMovimiento);
            
            Log::info("Movimiento registrado correctamente", [
                'parte' => $parte->nombre,
                'stock_anterior' => $stockAnterior,
                'stock_resultante' => $stockResultante
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al registrar movimiento: ' . $e->getMessage(), [
                'parte_id' => $parte->id,
                'trace' => $e->getTraceAsString()
            ]);
            // No lanzar excepción para no interrumpir la venta
        }
    }

    /**
     * Crear venta desde cotización (solo partes) - CORREGIDO
     */
    private function crearVentaDesdeCotizacion($cotizacion, $items)
    {
        try {
            // Obtener tipo de cambio actual si se está usando
            $tipoCambio = null;
            if (class_exists('App\Models\TipoCambio')) {
                $tipoCambio = \App\Models\TipoCambio::obtenerActual();
            }
            
            $venta = new Venta();
            $venta->codigo = Venta::generarCodigo(); // Usar el método del modelo
            $venta->fecha = now();
            $venta->cliente_id = $cotizacion->cliente_id;
            $venta->usuario_id = $cotizacion->user_id;
            $venta->almacen_id = $cotizacion->almacen_id;
            $venta->subtotal = $cotizacion->subtotal;
            $venta->igv = $cotizacion->impuestos;
            $venta->total = $cotizacion->total;
            $venta->moneda = $cotizacion->moneda;
            $venta->tipo_pago = $cotizacion->forma_pago;
            $venta->tipo_cambio_usado = $tipoCambio ? $tipoCambio->venta : null;
            
            // Usar los nuevos estados
            if ($cotizacion->forma_pago === 'Contado') {
                $venta->estado = 'pagado';
                $venta->monto_abonado = $cotizacion->total;
                $venta->saldo_pendiente = 0;
            } else {
                // Crédito
                $venta->estado = $cotizacion->abono >= $cotizacion->total ? 'pagado' : 'no_pagado';
                $venta->monto_abonado = $cotizacion->abono;
                $venta->saldo_pendiente = $cotizacion->total - $cotizacion->abono;
                
                // Establecer fecha de vencimiento (30 días por defecto)
                $venta->fecha_vencimiento = now()->addDays(30);
            }
            
            $venta->prioridad = 'media';
            $venta->cotizacion_id = $cotizacion->id;
            
            // Inicializar tracking de estados
            $venta->detalle_estados = [[
                'fecha' => now()->toISOString(),
                'estado_anterior' => null,
                'estado_nuevo' => $venta->estado,
                'usuario_id' => $cotizacion->user_id,
                'comentario' => 'Venta creada desde POS'
            ]];
            
            $venta->save();
            
            foreach ($items as $item) {
                // Solo procesar partes
                if ($item['tipo'] == 'parte') {
                    $detalleVenta = new DetalleVenta();
                    $detalleVenta->venta_id = $venta->id;
                    $detalleVenta->cantidad = $item['cantidad'];
                    $detalleVenta->precio_unitario = $item['precio'];
                    $detalleVenta->descuento = $item['descuento'] ?? 0;
                    $detalleVenta->subtotal = $item['cantidad'] * $item['precio'];
                    $detalleVenta->total = $detalleVenta->subtotal * (1 - ($item['descuento'] ?? 0) / 100);
                    $detalleVenta->tipo_item = 'parte';
                    $detalleVenta->parte_id = $item['id'];
                    $detalleVenta->descripcion = $item['nombre'] ?? 'Producto ID: ' . $item['id'];
                    $detalleVenta->save();
                }
            }
            
            return $venta;
        } catch (\Exception $e) {
            Log::error('Error al crear venta desde cotización: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generar código único para venta
     */
    private function generarCodigoVenta()
    {
        return DB::transaction(function () {
            $año = date('Y');

            // Usar lockForUpdate para evitar concurrencia
            $ultimaVenta = Venta::where('codigo', 'like', "VTA-{$año}%")
                ->orderBy('codigo', 'desc')
                ->lockForUpdate()
                ->first();

            if ($ultimaVenta) {
                $numero = intval(substr($ultimaVenta->codigo, -4)) + 1;
            } else {
                $numero = 1;
            }

            $codigo = 'VTA-' . $año . str_pad($numero, 4, '0', STR_PAD_LEFT);

            // Verificar que no exista (double-check)
            $existe = Venta::where('codigo', $codigo)->exists();
            if ($existe) {
                // Si existe, buscar el siguiente disponible
                do {
                    $numero++;
                    $codigo = 'VTA-' . $año . str_pad($numero, 4, '0', STR_PAD_LEFT);
                } while (Venta::where('codigo', $codigo)->exists());
            }

            return $codigo;
        });
    }

    /**
     * Registrar historial de cotización
     */
    private function registrarHistorialCotizacion($cotizacion, $estado, $request, $itemsSinStock)
    {
        try {
            HistorialCotizacion::create([
                'cotizacion_id' => $cotizacion->id,
                'estado_anterior_id' => null,
                'estado_nuevo_id' => $estado->id,
                'user_id' => Auth::id(),
                'comentario' => "Cotización creada desde punto de venta (POS)"
                                . (!empty($itemsSinStock) ? " con items pendientes por stock" : "")
                                . ($request->porcentaje_abono < 100 ? " con abono del " . $request->porcentaje_abono . "%" : ""),
            ]);
        } catch (\Exception $e) {
            Log::error('Error al registrar historial de cotización: ' . $e->getMessage());
        }
    }
    
    /**
     * Crear cliente rápido desde el POS
     */
    public function crearCliente(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipo_cliente' => 'required|in:natural,juridico',
            'documento_identidad' => 'required|string|max:20|unique:clientes,documento_identidad',
            'nombres' => 'required_if:tipo_cliente,natural|string|max:255|nullable',
            'apellido_paterno' => 'required_if:tipo_cliente,natural|string|max:255|nullable',
            'apellido_materno' => 'nullable|string|max:255',
            'razon_social' => 'required_if:tipo_cliente,juridico|string|max:255|nullable',
            'correo' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            DB::beginTransaction();
            
            $cliente = new Cliente();
            $cliente->tipo_cliente = $request->tipo_cliente;
            $cliente->documento_identidad = $request->documento_identidad;
            $cliente->nombres = $request->nombres;
            $cliente->apellido_paterno = $request->apellido_paterno;
            $cliente->apellido_materno = $request->apellido_materno;
            $cliente->razon_social = $request->razon_social;
            $cliente->correo = $request->correo;
            $cliente->activo = true;
            $cliente->save();
            
            // Crear teléfono si se proporcionó
            if ($request->telefono) {
                $telefono = new Telefono();
                $telefono->cliente_id = $cliente->id;
                $telefono->numero = $request->telefono;
                $telefono->principal = true;
                $telefono->save();
            }
            
            DB::commit();
            
            if ($cliente->tipo_cliente == 'natural') {
                $nombreCompleto = trim($cliente->nombres . ' ' . $cliente->apellido_paterno . ' ' . $cliente->apellido_materno);
            } else {
                $nombreCompleto = $cliente->razon_social;
            }
            
            return response()->json([
                'success' => true,
                'cliente' => [
                    'id' => $cliente->id,
                    'documento' => $cliente->documento_identidad,
                    'nombre' => $nombreCompleto,
                    'tipo' => $cliente->tipo_cliente,
                    'telefono' => $request->telefono ?? 'N/A',
                    'correo' => $cliente->correo ?: 'N/A'
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear cliente desde POS: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener todas las partes disponibles (versión simplificada usando modelos)
     */
    public function obtenerTodasPartes(Request $request)
    {
        try {
            $almacenId = $request->input('almacen_id');
            $incluirSinStock = $request->boolean('incluir_sin_stock', true);
            $categoriaId = $request->input('categoria_id');
            $page = max(1, $request->input('page', 1));
            $perPage = 50;
            
            // MODIFICACIÓN: Permitir obtener todas las partes sin almacén específico
            // Verificar qué columnas existen en cada tabla
            $columnasPartes = Schema::getColumnListing('partes');
            $tieneInventarios = Schema::hasTable('inventarios');
            $columnasInventarios = $tieneInventarios ? Schema::getColumnListing('inventarios') : [];
            $tieneUnidades = Schema::hasTable('unidades');
            $tieneCategorias = Schema::hasTable('categorias_partes');
            $tieneAlmacenes = Schema::hasTable('almacenes');
            
            // Construir SELECT dinámico
            $selectFields = ['partes.id'];
            
            // Campos básicos
            foreach (['codigo', 'nombre', 'precio_venta', 'moneda_venta', 'marca', 'codigo_oem', 'imagen', 'categoria_parte_id'] as $campo) {
                if (in_array($campo, $columnasPartes)) {
                    $selectFields[] = "partes.$campo";
                }
            }
            
            // Construir la consulta
            $partesQuery = DB::table('partes')->select($selectFields);
            
            // Join con unidades
            if ($tieneUnidades && in_array('unidad_id', $columnasPartes)) {
                $partesQuery->leftJoin('unidades', 'partes.unidad_id', '=', 'unidades.id')
                            ->addSelect('unidades.nombre as unidad_nombre');
            }
            
            // Join con categorías
            if ($tieneCategorias && in_array('categoria_parte_id', $columnasPartes)) {
                $partesQuery->leftJoin('categorias_partes', 'partes.categoria_parte_id', '=', 'categorias_partes.id')
                            ->addSelect('categorias_partes.nombre as categoria_nombre');
            }
            
            // Join con inventarios (modificado para soportar todos los almacenes)
            if ($tieneInventarios) {
                if ($almacenId) {
                    // Almacén específico
                    $partesQuery->leftJoin('inventarios', function($join) use ($almacenId) {
                        $join->on('partes.id', '=', 'inventarios.parte_id')
                             ->where('inventarios.almacen_id', '=', $almacenId);
                    });
                    
                    if (in_array('stock_disponible', $columnasInventarios)) {
                        $partesQuery->addSelect('inventarios.stock_disponible');
                    }
                    if (in_array('stock_real', $columnasInventarios)) {
                        $partesQuery->addSelect('inventarios.stock_real');
                    }
                } else {
                    // TODOS los almacenes - sumar stock de todos
                    $partesQuery->leftJoin('inventarios', 'partes.id', '=', 'inventarios.parte_id');
                    
                    if (in_array('stock_disponible', $columnasInventarios)) {
                        $partesQuery->addSelect(DB::raw('COALESCE(SUM(inventarios.stock_disponible), 0) as stock_disponible'));
                    }
                    if (in_array('stock_real', $columnasInventarios)) {
                        $partesQuery->addSelect(DB::raw('COALESCE(SUM(inventarios.stock_real), 0) as stock_real'));
                    }
                    
                    // Agregar información de almacenes
                    if ($tieneAlmacenes) {
                        $partesQuery->leftJoin('almacenes', 'inventarios.almacen_id', '=', 'almacenes.id')
                                   ->addSelect(DB::raw('GROUP_CONCAT(DISTINCT almacenes.nombre SEPARATOR ", ") as almacenes_nombres'));
                    }
                    
                    // Agrupar para evitar duplicados
                    $partesQuery->groupBy(array_merge($selectFields, $tieneUnidades ? ['unidades.nombre'] : [], $tieneCategorias ? ['categorias_partes.nombre'] : []));
                }
            } else {
                // Si no hay tabla inventarios, simular stock 0
                $partesQuery->addSelect(DB::raw('0 as stock_disponible'));
                $partesQuery->addSelect(DB::raw('0 as stock_real'));
            }
            
            // Filtros básicos
            if (in_array('precio_venta', $columnasPartes)) {
                $partesQuery->whereNotNull('partes.precio_venta')
                            ->where('partes.precio_venta', '>', 0);
            }
            
            // Filtrar por categoría
            if ($categoriaId && in_array('categoria_parte_id', $columnasPartes)) {
                $partesQuery->where('partes.categoria_parte_id', $categoriaId);
            }
            
            // Filtrar por stock
            if (!$incluirSinStock && $tieneInventarios && in_array('stock_disponible', $columnasInventarios)) {
                if ($almacenId) {
                    $partesQuery->where('inventarios.stock_disponible', '>', 0);
                } else {
                    $partesQuery->havingRaw('COALESCE(SUM(inventarios.stock_disponible), 0) > 0');
                }
            }
            
            // Contar total (clonar query antes de modificar)
            $countQuery = clone $partesQuery;
            if (!$almacenId && $tieneInventarios) {
                // Para conteo con GROUP BY, usar subquery
                $total = DB::table(DB::raw("({$countQuery->toSql()}) as sub"))
                           ->mergeBindings($countQuery)
                           ->count();
            } else {
                $total = $countQuery->count();
            }
            
            // Ordenar
            if ($tieneInventarios && in_array('stock_disponible', $columnasInventarios)) {
                if ($almacenId) {
                    $partesQuery->orderByRaw('CASE WHEN COALESCE(inventarios.stock_disponible, 0) > 0 THEN 0 ELSE 1 END');
                } else {
                    $partesQuery->orderByRaw('CASE WHEN COALESCE(SUM(inventarios.stock_disponible), 0) > 0 THEN 0 ELSE 1 END');
                }
            }
            $partesQuery->orderBy('partes.nombre');
            
            // Paginación
            $offset = ($page - 1) * $perPage;
            $partes = $partesQuery->skip($offset)->take($perPage)->get();
            
            // Formatear resultados
            $resultado = $partes->map(function($parte) use ($almacenId) {
                $stockDisponible = isset($parte->stock_disponible) ? $parte->stock_disponible : 0;
                
                return [
                    'id' => $parte->id,
                    'codigo' => $parte->codigo ?? 'SIN-CODIGO',
                    'nombre' => $parte->nombre ?? 'Sin nombre',
                    'descripcion' => '', // Campo no disponible en tu estructura
                    'precio' => $parte->precio_venta ?? 0,
                    'moneda' => $parte->moneda_venta ?? 'SOL',
                    'unidad' => $parte->unidad_nombre ?? 'Unidad',
                    'categoria' => $parte->categoria_nombre ?? 'Sin categoría',
                    'categoria_id' => $parte->categoria_parte_id ?? null,
                    'stock_disponible' => $stockDisponible,
                    'stock_real' => $parte->stock_real ?? 0,
                    'tipo' => 'parte',
                    'tiene_stock' => $stockDisponible > 0,
                    'marca' => $parte->marca ?? '',
                    'codigo_oem' => $parte->codigo_oem ?? '',
                    'imagen' => $parte->imagen ?? null,
                    'almacen_nombre' => $almacenId ? null : ($parte->almacenes_nombres ?? 'Disponible')
                ];
            });
            
            return response()->json([
                'items' => $resultado,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => ceil($total / $perPage),
                    'has_more' => ($page * $perPage) < $total
                ],
                'almacen_info' => $almacenId ? 'Almacén específico' : 'Todos los almacenes'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en obtenerTodasPartes: ' . $e->getMessage(), [
                'almacen_id' => $request->input('almacen_id'),
                'page' => $request->input('page'),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => true,
                'message' => 'Error interno al obtener las partes'
            ], 500);
        }
    }

    /**
     * Listar ventas con filtros y paginación
     */
    public function listarVentas(Request $request)
    {
        try {
            $query = Venta::with(['cliente', 'usuario', 'almacen', 'cotizacion']);
            
            // Filtros
            if ($request->filled('fecha_desde')) {
                $query->whereDate('fecha', '>=', $request->fecha_desde);
            }
            
            if ($request->filled('fecha_hasta')) {
                $query->whereDate('fecha', '<=', $request->fecha_hasta);
            }
            
            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }
            
            if ($request->filled('almacen_id')) {
                $query->where('almacen_id', $request->almacen_id);
            }
            
            if ($request->filled('moneda')) {
                $query->where('moneda', $request->moneda);
            }
            
            if ($request->filled('buscar')) {
                $buscar = $request->buscar;
                $query->where(function($q) use ($buscar) {
                    $q->where('codigo', 'like', "%{$buscar}%")
                      ->orWhereHas('cliente', function($clienteQuery) use ($buscar) {
                          $clienteQuery->where('nombres', 'like', "%{$buscar}%")
                                      ->orWhere('apellido_paterno', 'like', "%{$buscar}%")
                                      ->orWhere('apellido_materno', 'like', "%{$buscar}%")
                                      ->orWhere('razon_social', 'like', "%{$buscar}%")
                                      ->orWhere('documento_identidad', 'like', "%{$buscar}%");
                      })
                      ->orWhereHas('usuario', function($usuarioQuery) use ($buscar) {
                          $usuarioQuery->where('name', 'like', "%{$buscar}%");
                      });
                });
            }
            
            // Ordenar por fecha descendente
            $query->orderBy('created_at', 'desc');
            
            // Paginación
            $perPage = 15;
            $ventas = $query->paginate($perPage);
            
            // Formatear datos
            $ventasData = $ventas->getCollection()->map(function($venta) {
                $clienteNombre = '';
                if ($venta->cliente) {
                    if ($venta->cliente->tipo_cliente == 'natural') {
                        $clienteNombre = trim(
                            ($venta->cliente->nombres ?? '') . ' ' . 
                            ($venta->cliente->apellido_paterno ?? '') . ' ' . 
                            ($venta->cliente->apellido_materno ?? '')
                        );
                    } else {
                        $clienteNombre = $venta->cliente->razon_social ?? 'Cliente corporativo';
                    }
                } else {
                    $clienteNombre = 'Cliente no encontrado';
                }
                
                return [
                    'id' => $venta->id,
                    'codigo' => $venta->codigo,
                    'fecha' => $venta->fecha->format('Y-m-d H:i:s'),
                    'cliente_nombre' => $clienteNombre ?: 'Sin nombre',
                    'cliente_documento' => $venta->cliente->documento_identidad ?? 'Sin documento',
                    'usuario_nombre' => $venta->usuario->name ?? 'Usuario no encontrado',
                    'almacen_nombre' => $venta->almacen->nombre ?? 'Almacén no encontrado',
                    'subtotal' => $venta->subtotal,
                    'igv' => $venta->igv,
                    'total' => $venta->total,
                    'monto_abonado' => $venta->monto_abonado,
                    'saldo_pendiente' => $venta->saldo_pendiente,
                    'porcentaje_abonado' => $venta->getPorcentajeAbonadoAttribute(),
                    'moneda' => $venta->moneda,
                    'tipo_pago' => $venta->tipo_pago,
                    'estado' => $venta->estado,
                    'cotizacion_codigo' => $venta->cotizacion->codigo ?? null,
                    'observaciones' => $venta->observaciones
                ];
            });
            
            // Calcular resumen
            $resumen = $this->calcularResumenVentas($request);
            
            return response()->json([
                'ventas' => $ventasData,
                'pagination' => [
                    'current_page' => $ventas->currentPage(),
                    'last_page' => $ventas->lastPage(),
                    'per_page' => $ventas->perPage(),
                    'total' => $ventas->total(),
                    'from' => $ventas->firstItem(),
                    'to' => $ventas->lastItem()
                ],
                'resumen' => $resumen
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al listar ventas: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Error al cargar las ventas'
            ], 500);
        }
    }

    /**
     * Mostrar detalle de una venta específica
     */
    public function mostrarVenta($id)
    {
        try {
            $venta = Venta::with([
                'cliente', 
                'usuario', 
                'almacen', 
                'cotizacion',
                'detallesPOS.parte'  // Cambiamos de 'detalles' a 'detallesPOS'
            ])->findOrFail($id);
            
            // Formatear nombre del cliente
            $clienteNombre = '';
            if ($venta->cliente) {
                if ($venta->cliente->tipo_cliente == 'natural') {
                    $clienteNombre = trim(
                        ($venta->cliente->nombres ?? '') . ' ' . 
                        ($venta->cliente->apellido_paterno ?? '') . ' ' . 
                        ($venta->cliente->apellido_materno ?? '')
                    );
                } else {
                    $clienteNombre = $venta->cliente->razon_social ?? 'Cliente corporativo';
                }
            }
            
            // Formatear detallesPOS
            $detallesPOS = $venta->detallesPOS->map(function($detalle) {
                return [
                    'id' => $detalle->id,
                    'tipo_item' => $detalle->tipo_item,
                    'item_nombre' => $detalle->parte ? $detalle->parte->nombre : $detalle->descripcion,
                    'item_codigo' => $detalle->parte ? $detalle->parte->codigo : 'N/A',
                    'cantidad' => $detalle->cantidad,
                    'precio_unitario' => $detalle->precio_unitario,
                    'descuento' => $detalle->descuento ?? 0,
                    'subtotal' => $detalle->subtotal,
                    'total' => $detalle->total,
                    'descripcion' => $detalle->descripcion
                ];
            });
            
            $ventaData = [
                'id' => $venta->id,
                'codigo' => $venta->codigo,
                'fecha' => $venta->fecha,
                'cliente_nombre' => $clienteNombre ?: 'Sin nombre',
                'cliente_documento' => $venta->cliente->documento_identidad ?? 'Sin documento',
                'usuario_nombre' => $venta->usuario->name ?? 'Usuario no encontrado',
                'almacen_nombre' => $venta->almacen->nombre ?? 'Almacén no encontrado',
                'subtotal' => $venta->subtotal,
                'igv' => $venta->igv,
                'total' => $venta->total,
                'monto_abonado' => $venta->monto_abonado,
                'saldo_pendiente' => $venta->saldo_pendiente,
                'porcentaje_abonado' => $venta->getPorcentajeAbonadoAttribute(),
                'moneda' => $venta->moneda,
                'tipo_pago' => $venta->tipo_pago,
                'estado' => $venta->estado,
                'observaciones' => $venta->observaciones,
                'cotizacion_codigo' => $venta->cotizacion->codigo ?? null,
                'detalles' => $detallesPOS, // Cambiar key para compatibilidad con la vista
                'detallesPOS' => $detallesPOS // Mantener para retrocompatibilidad
            ];
            
            // Si es petición AJAX, devolver JSON
            if (request()->ajax() || request()->wantsJson()) {
                // Generar HTML del detalle
                $html = view('admin.ventas.pos.partials.detalle-venta', compact('venta', 'ventaData'))->render();

                return response()->json([
                    'success' => true,
                    'venta' => $ventaData,
                    'html' => $html
                ]);
            }

            // Si es petición directa, devolver vista completa con layout
            return view('admin.ventas.pos.show', compact('venta', 'ventaData'));
            
        } catch (\Exception $e) {
            Log::error('Error al mostrar venta: ' . $e->getMessage());

            // Si es petición AJAX, devolver JSON
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Error al cargar el detalle de la venta'
                ], 404);
            }

            // Si es petición directa, redirigir con error
            return redirect()->route('admin.ventas.pos.ventas')
                           ->with('error', 'No se pudo cargar el detalle de la venta');
        }
    }

    /**
     * Registrar pago adicional a una venta
     */
 public function registrarPago(Request $request)
    {
        $request->validate([
            'venta_id' => 'required|exists:ventas,id',
            'monto' => 'required|numeric|min:0.01',
            'referencia' => 'nullable|string|max:255',
            'comentario' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $venta = Venta::findOrFail($request->venta_id);
            
            // Verificar que el monto no exceda el saldo pendiente
            if ($request->monto > $venta->saldo_pendiente) {
                return response()->json([
                    'success' => false,
                    'message' => 'El monto ingresado excede el saldo pendiente'
                ], 400);
            }
            
            // Registrar el pago (sin pasar moneda para que use el mapeo automático)
            $resultado = $venta->registrarPago(
                $request->monto,
                'efectivo', // método de pago por defecto
                $request->referencia,
                $request->comentario
                // No pasamos moneda para que use el mapeo automático basado en $this->moneda
            );
            
            if (!$resultado) {
                throw new \Exception('No se pudo registrar el pago');
            }
            
            // No necesitamos insertar manualmente porque el modelo PagoVenta ya lo hace
            // El método registrarPago del modelo Venta ya crea el registro en pagos_ventas
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pago registrado correctamente',
                'venta' => [
                    'id' => $venta->id,
                    'monto_abonado' => $venta->fresh()->monto_abonado,
                    'saldo_pendiente' => $venta->fresh()->saldo_pendiente,
                    'estado' => $venta->fresh()->estado
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar pago: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar venta como lista para entrega (cuando llega el stock)
     */
    public function marcarListaEntrega(Request $request, $id)
    {
        try {
            $venta = Venta::findOrFail($id);

            if (!$venta->puedeMarcarseListaEntrega()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta venta no puede marcarse como lista para entrega en su estado actual'
                ], 400);
            }

            $resultado = $venta->marcarListaParaEntrega(
                $request->comentario,
                auth()->id()
            );

            if ($resultado) {
                return response()->json([
                    'success' => true,
                    'message' => 'Venta marcada como lista para entrega',
                    'nuevo_estado' => $venta->fresh()->estado
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al marcar la venta como lista para entrega'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error al marcar venta como lista para entrega: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno al procesar la solicitud'
            ], 500);
        }
    }

    /**
     * Marcar venta como despachada (entregada al cliente)
     */
    public function marcarDespachada(Request $request, $id)
    {
        try {
            $venta = Venta::findOrFail($id);

            if (!$venta->puedeDespacharse()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta venta no puede despacharse. Verifique que esté lista para entrega y completamente pagada.'
                ], 400);
            }

            $resultado = $venta->marcarComoDespachada(
                $request->comentario,
                auth()->id()
            );

            return response()->json($resultado);

        } catch (\Exception $e) {
            Log::error('Error al marcar venta como despachada: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno al procesar la solicitud'
            ], 500);
        }
    }

    /**
     * Imprimir venta
     */
    public function imprimirVenta($id)
    {
        try {
            $venta = Venta::with([
                'cliente', 
                'usuario', 
                'almacen', 
                'detallesPOS.parte'
            ])->findOrFail($id);
            
            return view('admin.ventas.pos.imprimir', compact('venta'));
            
        } catch (\Exception $e) {
            Log::error('Error al generar impresión: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al generar la impresión');
        }
    }
public function anular(Request $request, $id)
    {
        $request->validate([
            'comentario' => 'required|string|max:500'
        ]);

        try {
            DB::beginTransaction();
            
            $venta = Venta::findOrFail($id);
            
            // Verificar que se pueda anular
            if ($venta->estado === 'Cancelada') {
                return redirect()->back()
                    ->with('error', 'La venta ya está cancelada');
            }
            
            // Actualizar estado
            $venta->estado = 'Cancelada';
            $venta->observaciones = ($venta->observaciones ?? '') . 
                "\n\n[" . now()->format('d/m/Y H:i') . "] ANULADA por " . 
                Auth::user()->name . ": " . $request->motivo;
            $venta->save();
            
            // Aquí podrías revertir el stock si es necesario
            // $this->revertirStock($venta);
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Venta anulada correctamente');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al anular venta: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al anular la venta');
        }
    }

    public function exportar(Request $request)
    {
        try {
            $query = Venta::with(['cliente', 'usuario', 'almacen']);
            
            // Aplicar los mismos filtros que en index
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('codigo', 'like', "%{$search}%")
                      ->orWhereHas('cliente', function($clienteQuery) use ($search) {
                          $clienteQuery->where('nombres', 'like', "%{$search}%")
                                      ->orWhere('apellido_paterno', 'like', "%{$search}%")
                                      ->orWhere('apellido_materno', 'like', "%{$search}%")
                                      ->orWhere('razon_social', 'like', "%{$search}%")
                                      ->orWhere('documento_identidad', 'like', "%{$search}%");
                      });
                });
            }
            
            if ($request->filled('fecha_desde')) {
                $query->whereDate('fecha', '>=', $request->fecha_desde);
            }
            
            if ($request->filled('fecha_hasta')) {
                $query->whereDate('fecha', '<=', $request->fecha_hasta);
            }
            
            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }
            
            if ($request->filled('almacen_id')) {
                $query->where('almacen_id', $request->almacen_id);
            }
            
            $ventas = $query->orderBy('fecha', 'desc')->get();
            
            // Preparar datos para CSV
            $data = [];
            $data[] = [
                'Código', 'Fecha', 'Cliente', 'Documento', 'Usuario', 'Almacén',
                'Subtotal', 'IGV', 'Total', 'Abonado', 'Saldo', 'Moneda', 
                'Tipo Pago', 'Estado', 'Observaciones'
            ];
            
            foreach ($ventas as $venta) {
                $clienteNombre = $this->formatearNombreCliente($venta->cliente);
                
                $data[] = [
                    $venta->codigo,
                    $venta->fecha->format('d/m/Y H:i'),
                    $clienteNombre,
                    $venta->cliente->documento_identidad ?? 'Sin documento',
                    $venta->usuario->name ?? 'Usuario no encontrado',
                    $venta->almacen->nombre ?? 'Almacén no encontrado',
                    $venta->subtotal,
                    $venta->igv,
                    $venta->total,
                    $venta->monto_abonado,
                    $venta->saldo_pendiente,
                    $venta->moneda,
                    $venta->tipo_pago,
                    $venta->estado,
                    $venta->observaciones
                ];
            }
            
            // Crear archivo CSV
            $filename = 'ventas_' . date('Y-m-d_H-i-s') . '.csv';
            $path = storage_path('app/temp/' . $filename);
            
            // Crear directorio si no existe
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }
            
            // Escribir CSV
            $file = fopen($path, 'w');
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
            
            return response()->download($path, $filename)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            Log::error('Error al exportar ventas: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al exportar las ventas');
        }
    }
     private function obtenerEstadisticas($request)
    {
        try {
            $query = Venta::query();
            
            // Aplicar filtros de fecha si existen
            if ($request->filled('fecha_desde')) {
                $query->whereDate('fecha', '>=', $request->fecha_desde);
            } else {
                // Por defecto, último mes
                $query->whereDate('fecha', '>=', now()->subMonth());
            }
            
            if ($request->filled('fecha_hasta')) {
                $query->whereDate('fecha', '<=', $request->fecha_hasta);
            }
            
            $estadisticas = $query->selectRaw('
                COUNT(*) as total_ventas,
                SUM(total) as monto_total,
                SUM(CASE WHEN estado = "Completada" THEN 1 ELSE 0 END) as ventas_completadas,
                SUM(CASE WHEN estado = "Parcial" THEN 1 ELSE 0 END) as ventas_parciales,
                SUM(CASE WHEN estado = "Cancelada" THEN 1 ELSE 0 END) as ventas_canceladas,
                SUM(monto_abonado) as total_abonado,
                SUM(saldo_pendiente) as total_pendiente,
                AVG(total) as promedio_venta
            ')->first();
            
            return $estadisticas;
            
        } catch (\Exception $e) {
            Log::error('Error al obtener estadísticas: ' . $e->getMessage());
            return (object)[
                'total_ventas' => 0,
                'monto_total' => 0,
                'ventas_completadas' => 0,
                'ventas_parciales' => 0,
                'ventas_canceladas' => 0,
                'total_abonado' => 0,
                'total_pendiente' => 0,
                'promedio_venta' => 0
            ];
        }
    }
    private function formatearCliente($cliente)
    {
        if (!$cliente) {
            return [
                'nombre' => 'Cliente no encontrado',
                'documento' => 'Sin documento',
                'telefono' => 'Sin teléfono',
                'email' => 'Sin email',
                'direccion' => 'Sin dirección'
            ];
        }
        
        $nombre = '';
        if ($cliente->tipo_cliente == 'natural') {
            $nombre = trim(
                ($cliente->nombres ?? '') . ' ' . 
                ($cliente->apellido_paterno ?? '') . ' ' . 
                ($cliente->apellido_materno ?? '')
            );
        } else {
            $nombre = $cliente->razon_social ?? 'Cliente corporativo';
        }
        
        return [
            'nombre' => $nombre ?: 'Sin nombre',
            'documento' => $cliente->documento_identidad ?? 'Sin documento',
            'telefono' => $cliente->telefonos->first() ? $cliente->telefonos->first()->numero : 'Sin teléfono',
            'email' => $cliente->correo ?? 'Sin email',
            'direccion' => $cliente->direccion ?? 'Sin dirección'
        ];
    }
    private function formatearNombreCliente($cliente)
    {
        if (!$cliente) {
            return 'Cliente no encontrado';
        }
        
        if ($cliente->tipo_cliente == 'natural') {
            return trim(
                ($cliente->nombres ?? '') . ' ' . 
                ($cliente->apellido_paterno ?? '') . ' ' . 
                ($cliente->apellido_materno ?? '')
            ) ?: 'Sin nombre';
        } else {
            return $cliente->razon_social ?? 'Cliente corporativo';
        }
    }
    private function calcularEstadisticasVenta($venta, $detallesPOS)
    {
        $totalItems = $detallesPOS->sum('cantidad');
        $itemsUnicos = $detallesPOS->count();
        $descuentoTotal = $detallesPOS->sum(function($detalle) {
            return ($detalle->precio_unitario * $detalle->cantidad) - $detalle->subtotal;
        });
        
        return [
            'total_items' => $totalItems,
            'items_unicos' => $itemsUnicos,
            'descuento_total' => $descuentoTotal,
            'porcentaje_abonado' => $venta->getPorcentajeAbonadoAttribute(),
            'esta_pagada' => $venta->estaPagada()
        ];
    }
private function obtenerHistorialPagos($ventaId)
    {
        try {
            if (!\Schema::hasTable('pagos_venta')) {
                return collect();
            }
            
            return DB::table('pagos_venta')
                ->leftJoin('users', 'pagos_venta.usuario_id', '=', 'users.id')
                ->where('venta_id', $ventaId)
                ->select([
                    'pagos_venta.*',
                    'users.name as usuario_nombre'
                ])
                ->orderBy('fecha_pago', 'desc')
                ->get();
                
        } catch (\Exception $e) {
            Log::error('Error al obtener historial de pagos: ' . $e->getMessage());
            return collect();
        }
    }
    /**
     * Exportar ventas a Excel
     */
    public function exportarVentas(Request $request)
    {
        try {
            $query = Venta::with(['cliente', 'usuario', 'almacen']);
            
            // Aplicar filtros
            if ($request->filled('fecha_desde')) {
                $query->whereDate('fecha', '>=', $request->fecha_desde);
            }
            
            if ($request->filled('fecha_hasta')) {
                $query->whereDate('fecha', '<=', $request->fecha_hasta);
            }
            
            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }
            
            if ($request->filled('almacen_id')) {
                $query->where('almacen_id', $request->almacen_id);
            }
            
            if ($request->filled('moneda')) {
                $query->where('moneda', $request->moneda);
            }
            
            $ventas = $query->orderBy('created_at', 'desc')->get();
            
            // Preparar datos para Excel
            $data = [];
            $data[] = [
                'Código', 'Fecha', 'Cliente', 'Documento', 'Usuario', 'Almacén',
                'Subtotal', 'IGV', 'Total', 'Abonado', 'Saldo', 'Moneda', 
                'Tipo Pago', 'Estado', 'Observaciones'
            ];
            
            foreach ($ventas as $venta) {
                $clienteNombre = '';
                if ($venta->cliente) {
                    if ($venta->cliente->tipo_cliente == 'natural') {
                        $clienteNombre = trim(
                            ($venta->cliente->nombres ?? '') . ' ' . 
                            ($venta->cliente->apellido_paterno ?? '') . ' ' . 
                            ($venta->cliente->apellido_materno ?? '')
                        );
                    } else {
                        $clienteNombre = $venta->cliente->razon_social ?? 'Cliente corporativo';
                    }
                }
                
                $data[] = [
                    $venta->codigo,
                    $venta->fecha->format('d/m/Y H:i'),
                    $clienteNombre ?: 'Sin nombre',
                    $venta->cliente->documento_identidad ?? 'Sin documento',
                    $venta->usuario->name ?? 'Usuario no encontrado',
                    $venta->almacen->nombre ?? 'Almacén no encontrado',
                    $venta->subtotal,
                    $venta->igv,
                    $venta->total,
                    $venta->monto_abonado,
                    $venta->saldo_pendiente,
                    $venta->moneda,
                    $venta->tipo_pago,
                    $venta->estado,
                    $venta->observaciones
                ];
            }
            
            // Crear archivo temporal
            $filename = 'ventas_pos_' . date('Y-m-d_H-i-s') . '.csv';
            $path = storage_path('app/temp/' . $filename);
            
            // Crear directorio si no existe
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }
            
            // Escribir CSV
            $file = fopen($path, 'w');
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
            
            return response()->download($path, $filename)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            Log::error('Error al exportar ventas: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al exportar las ventas');
        }
    }

    /**
     * Generar código único genérico - MÉTODO AUXILIAR MEJORADO
     */
    private function generarCodigoUnico($prefijo, $modelo)
    {
        $año = date('Y');
        $maxIntentos = 20;
        
        // Intentar generar código único con transacción
        return DB::transaction(function() use ($prefijo, $modelo, $año, $maxIntentos) {
            
            for ($intento = 1; $intento <= $maxIntentos; $intento++) {
                
                // Obtener el último número de forma más segura
                $ultimoNumero = DB::table((new $modelo)->getTable())
                    ->where('codigo', 'like', "{$prefijo}-{$año}%")
                    ->lockForUpdate()
                    ->orderByRaw('CAST(SUBSTRING(codigo, LENGTH(?) + 2) AS UNSIGNED) DESC', [$prefijo . '-' . $año])
                    ->value('codigo');
                
                if ($ultimoNumero) {
                    // Extraer número del código: COT-20250005 -> 0005 -> 5
                    $parteNumero = str_replace($prefijo . '-' . $año, '', $ultimoNumero);
                    $numero = intval($parteNumero) + 1;
                } else {
                    $numero = 1;
                }
                
                // Generar código con formato fijo de 4 dígitos
                $nuevoCodigo = $prefijo . '-' . $año . str_pad($numero, 4, '0', STR_PAD_LEFT);
                
                // Verificar disponibilidad con bloqueo
                $existe = DB::table((new $modelo)->getTable())
                    ->where('codigo', $nuevoCodigo)
                    ->lockForUpdate()
                    ->exists();
                    
                if (!$existe) {
                    Log::info("Código único generado: {$nuevoCodigo} en intento {$intento}");
                    return $nuevoCodigo;
                }
                
                Log::warning("Código {$nuevoCodigo} ya existe, intento {$intento} de {$maxIntentos}");
                
                // Esperar entre intentos para evitar condiciones de carrera
                usleep(rand(1000, 5000)); // 1-5ms aleatorio
            }
            
            // Si todos los intentos fallan, usar timestamp único
            $codigoFallback = $prefijo . '-' . $año . '-' . date('mdHis') . rand(100, 999);
            Log::error("Usando código de emergencia: {$codigoFallback}");
            
            return $codigoFallback;
        });
    }
}