<?php

namespace App\Http\Controllers\Admin\Reportes;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\OrdenCompra;
use App\Models\OrdenTrabajoMantenimiento;
use App\Models\Parte;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.reportes.index');
    }

    public function ventas(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));
        $estado = $request->input('estado', '');

        // Datos generales
        $query = Venta::whereBetween('fecha', [$fechaInicio, $fechaFin]);

        if ($estado) {
            $query->where('estado', $estado);
        }

        $totalVentas = (clone $query)->count();
        $montoTotal = (clone $query)->sum('total');
        $ventasPendientes = (clone $query)->where('estado', 'pendiente')->count();
        $ventasCompletadas = (clone $query)->where('estado', 'completada')->count();

        // Ventas por día (últimos 30 días)
        $ventasPorDia = Venta::select(
            DB::raw('DATE(fecha) as fecha'),
            DB::raw('COUNT(*) as cantidad'),
            DB::raw('SUM(total) as monto')
        )
        ->whereBetween('fecha', [now()->subDays(30), now()])
        ->groupBy('fecha')
        ->orderBy('fecha')
        ->get();

        // Ventas por estado
        $ventasPorEstado = Venta::select('estado', DB::raw('COUNT(*) as cantidad'))
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->groupBy('estado')
            ->get();

        // Top productos más vendidos
        $topProductos = DB::table('detalles_venta')
            ->join('ventas', 'detalles_venta.venta_id', '=', 'ventas.id')
            ->join('partes', 'detalles_venta.parte_id', '=', 'partes.id')
            ->whereBetween('ventas.fecha', [$fechaInicio, $fechaFin])
            ->where('detalles_venta.tipo_item', 'parte')
            ->select(
                'partes.nombre',
                'partes.codigo',
                DB::raw('SUM(detalles_venta.cantidad) as total_vendido'),
                DB::raw('SUM(detalles_venta.cantidad * detalles_venta.precio_unitario) as total_monto')
            )
            ->groupBy('partes.id', 'partes.nombre', 'partes.codigo')
            ->orderBy('total_vendido', 'desc')
            ->limit(10)
            ->get();

        return view('admin.reportes.ventas', compact(
            'totalVentas',
            'montoTotal',
            'ventasPendientes',
            'ventasCompletadas',
            'ventasPorDia',
            'ventasPorEstado',
            'topProductos',
            'fechaInicio',
            'fechaFin',
            'estado'
        ));
    }

    public function compras(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));
        $estado = $request->input('estado', '');

        // Datos generales - usando created_at ya que no existe fecha_orden
        $query = OrdenCompra::whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59']);

        if ($estado) {
            $query->where('estado', $estado);
        }

        $totalOrdenes = (clone $query)->count();
        $montoTotal = (clone $query)->sum('total');
        $ordenesPendientes = (clone $query)->where('estado', 'pendiente')->count();
        $ordenesRecibidas = (clone $query)->where('estado', 'recibida')->count();

        // Compras por día
        $comprasPorDia = OrdenCompra::select(
            DB::raw('DATE(created_at) as fecha'),
            DB::raw('COUNT(*) as cantidad'),
            DB::raw('SUM(total) as monto')
        )
        ->whereBetween('created_at', [now()->subDays(30), now()])
        ->groupBy('fecha')
        ->orderBy('fecha')
        ->get();

        // Compras por estado
        $comprasPorEstado = OrdenCompra::select('estado', DB::raw('COUNT(*) as cantidad'))
            ->whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->groupBy('estado')
            ->get();

        // Top proveedores
        $topProveedores = OrdenCompra::join('proveedores', 'orden_compras.proveedor_id', '=', 'proveedores.id')
            ->whereBetween('orden_compras.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->select(
                DB::raw('COALESCE(proveedores.razon_social, CONCAT(proveedores.nombres, " ", COALESCE(proveedores.apellido_paterno, ""), " ", COALESCE(proveedores.apellido_materno, ""))) as nombre'),
                DB::raw('COUNT(orden_compras.id) as total_ordenes'),
                DB::raw('SUM(orden_compras.total) as total_monto')
            )
            ->groupBy('proveedores.id', 'proveedores.razon_social', 'proveedores.nombres', 'proveedores.apellido_paterno', 'proveedores.apellido_materno')
            ->orderBy('total_monto', 'desc')
            ->limit(10)
            ->get();

        return view('admin.reportes.compras', compact(
            'totalOrdenes',
            'montoTotal',
            'ordenesPendientes',
            'ordenesRecibidas',
            'comprasPorDia',
            'comprasPorEstado',
            'topProveedores',
            'fechaInicio',
            'fechaFin',
            'estado'
        ));
    }

    public function inventario(Request $request)
    {
        $almacenId = $request->input('almacen_id', '');

        // Stock total
        $query = Inventario::query();

        if ($almacenId) {
            $query->where('almacen_id', $almacenId);
        }

        $stockTotal = (clone $query)->sum('stock_disponible');
        $valorInventario = (clone $query)
            ->join('partes', 'inventarios.parte_id', '=', 'partes.id')
            ->sum(DB::raw('inventarios.stock_disponible * partes.precio_compra'));

        $productosStockBajo = (clone $query)
            ->whereRaw('stock_disponible <= stock_minimo')
            ->count();

        $productosSinStock = (clone $query)
            ->where('stock_disponible', 0)
            ->count();

        // Productos con stock bajo
        $productosConStockBajo = Inventario::join('partes', 'inventarios.parte_id', '=', 'partes.id')
            ->join('almacenes', 'inventarios.almacen_id', '=', 'almacenes.id')
            ->whereRaw('inventarios.stock_disponible <= inventarios.stock_minimo')
            ->select(
                'partes.codigo',
                'partes.nombre',
                'almacenes.nombre as almacen',
                'inventarios.stock_disponible',
                'inventarios.stock_minimo'
            )
            ->orderBy('inventarios.stock_disponible')
            ->limit(20)
            ->get();

        // Movimientos recientes
        $movimientosRecientes = DB::table('kardex')
            ->join('partes', 'kardex.parte_id', '=', 'partes.id')
            ->join('almacenes', 'kardex.almacen_id', '=', 'almacenes.id')
            ->select(
                'kardex.created_at as fecha',
                'kardex.tipo_movimiento',
                'partes.codigo',
                'partes.nombre',
                'almacenes.nombre as almacen',
                DB::raw('COALESCE(kardex.cantidad_entrada, 0) + COALESCE(kardex.cantidad_salida, 0) as cantidad'),
                'kardex.stock_actual as stock_final'
            )
            ->orderBy('kardex.created_at', 'desc')
            ->limit(15)
            ->get();

        return view('admin.reportes.inventario', compact(
            'stockTotal',
            'valorInventario',
            'productosStockBajo',
            'productosSinStock',
            'productosConStockBajo',
            'movimientosRecientes',
            'almacenId'
        ));
    }

    public function mantenimiento(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));
        $estado = $request->input('estado', '');

        // Datos generales
        $query = OrdenTrabajoMantenimiento::whereBetween('fecha_ingreso', [$fechaInicio, $fechaFin]);

        if ($estado) {
            $query->where('estado', $estado);
        }

        $totalOrdenes = (clone $query)->count();
        $ordenesPendientes = (clone $query)->where('estado', 'pendiente')->count();
        $ordenesEnProceso = (clone $query)->where('estado', 'en_progreso')->count();
        $ordenesCompletadas = (clone $query)->where('estado', 'completado')->count();

        // Órdenes por día
        $ordenesPorDia = OrdenTrabajoMantenimiento::select(
            DB::raw('DATE(fecha_ingreso) as fecha'),
            DB::raw('COUNT(*) as cantidad')
        )
        ->whereBetween('fecha_ingreso', [now()->subDays(30), now()])
        ->groupBy('fecha')
        ->orderBy('fecha')
        ->get();

        // Órdenes por estado
        $ordenesPorEstado = OrdenTrabajoMantenimiento::select('estado', DB::raw('COUNT(*) as cantidad'))
            ->whereBetween('fecha_ingreso', [$fechaInicio, $fechaFin])
            ->groupBy('estado')
            ->get();

        // Técnicos con más órdenes
        $topTecnicos = OrdenTrabajoMantenimiento::join('users', 'ordenes_trabajo_mantenimiento.tecnico_asignado_id', '=', 'users.id')
            ->whereBetween('ordenes_trabajo_mantenimiento.fecha_ingreso', [$fechaInicio, $fechaFin])
            ->select(
                'users.name',
                DB::raw('COUNT(ordenes_trabajo_mantenimiento.id) as total_ordenes'),
                DB::raw('SUM(CASE WHEN ordenes_trabajo_mantenimiento.estado = "completado" THEN 1 ELSE 0 END) as completadas')
            )
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_ordenes', 'desc')
            ->limit(10)
            ->get();

        // Tiempo promedio de servicio
        $tiempoPromedio = OrdenTrabajoMantenimiento::whereBetween('fecha_ingreso', [$fechaInicio, $fechaFin])
            ->whereNotNull('fecha_fin_trabajo')
            ->whereNotNull('fecha_inicio_trabajo')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, fecha_inicio_trabajo, fecha_fin_trabajo)) as promedio_horas'))
            ->first();

        return view('admin.reportes.mantenimiento', compact(
            'totalOrdenes',
            'ordenesPendientes',
            'ordenesEnProceso',
            'ordenesCompletadas',
            'ordenesPorDia',
            'ordenesPorEstado',
            'topTecnicos',
            'tiempoPromedio',
            'fechaInicio',
            'fechaFin',
            'estado'
        ));
    }
}
