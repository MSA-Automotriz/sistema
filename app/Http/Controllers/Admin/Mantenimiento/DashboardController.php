<?php

namespace App\Http\Controllers\Admin\Mantenimiento;

use App\Http\Controllers\Controller;
use App\Models\CitaMantenimiento;
use App\Models\DetalleOrdenTrabajoRepuesto;
use App\Models\DetalleOrdenTrabajoServicio;
use App\Models\FacturaOrdenTrabajo;
use App\Models\OrdenTrabajoMantenimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Mostrar el dashboard de mantenimiento
     */
    public function index()
    {
        // Obtener estadísticas
        $estadisticas = $this->obtenerEstadisticas('week'); // Por defecto, esta semana
        
        // Obtener próximas citas
        $proximasCitas = CitaMantenimiento::where('estado', 'pendiente')
            ->where('fecha_hora_cita', '>=', Carbon::now())
            ->with(['cliente', 'vehiculo'])
            ->orderBy('fecha_hora_cita')
            ->limit(5)
            ->get();
        
        // Obtener órdenes en progreso
        $ordenesEnProgreso = OrdenTrabajoMantenimiento::whereIn('estado', ['diagnostico', 'espera_aprobacion', 'en_progreso'])
            ->with(['cliente', 'vehiculo', 'tecnico'])
            ->orderBy('fecha_ingreso', 'desc')
            ->limit(5)
            ->get();
        
        // Obtener servicios más solicitados
        $topServicios = DetalleOrdenTrabajoServicio::select(
                'descripcion',
                DB::raw('SUM(cantidad) as cantidad'),
                DB::raw('SUM(cantidad * precio_unitario) as total')
            )
            ->whereHas('ordenTrabajo', function($query) {
                $query->where('created_at', '>=', Carbon::now()->startOfMonth());
            })
            ->groupBy('descripcion')
            ->orderBy('cantidad', 'desc')
            ->limit(5)
            ->get();
        
        // Obtener repuestos más utilizados
        $topRepuestos = DetalleOrdenTrabajoRepuesto::select(
                'descripcion',
                DB::raw('SUM(cantidad) as cantidad'),
                DB::raw('SUM(cantidad * precio_unitario) as total')
            )
            ->whereHas('ordenTrabajo', function($query) {
                $query->where('created_at', '>=', Carbon::now()->startOfMonth());
            })
            ->groupBy('descripcion')
            ->orderBy('cantidad', 'desc')
            ->limit(5)
            ->get();
        
        return view('admin.mantenimiento.dashboard', compact(
            'estadisticas',
            'proximasCitas',
            'ordenesEnProgreso',
            'topServicios',
            'topRepuestos'
        ));
    }
    
    /**
     * Obtener datos por período para actualizar el dashboard vía AJAX
     */
    public function obtenerDatosPorPeriodo(Request $request)
    {
        $period = $request->get('period', 'week');
        $estadisticas = $this->obtenerEstadisticas($period);
        
        return response()->json($estadisticas);
    }
    
    /**
     * Calcular estadísticas según el período
     */
    private function obtenerEstadisticas($period)
    {
        // Definir fecha de inicio según el período
        $fechaInicio = $this->obtenerFechaInicio($period);
        
        // Estadísticas de citas
        $citasPendientes = CitaMantenimiento::where('estado', 'pendiente')
            ->where('fecha_hora_cita', '>=', Carbon::now())
            ->count();
        
        // Estadísticas de órdenes por estado
        $ordenesDiagnostico = OrdenTrabajoMantenimiento::where('estado', 'diagnostico')
            ->where('created_at', '>=', $fechaInicio)
            ->count();
            
        $ordenesEsperaAprobacion = OrdenTrabajoMantenimiento::where('estado', 'espera_aprobacion')
            ->where('created_at', '>=', $fechaInicio)
            ->count();
            
        $ordenesEnProgreso = OrdenTrabajoMantenimiento::where('estado', 'en_progreso')
            ->where('created_at', '>=', $fechaInicio)
            ->count();
            
        $ordenesFinalizadas = OrdenTrabajoMantenimiento::where('estado', 'finalizado')
            ->where('created_at', '>=', $fechaInicio)
            ->count();
            
        $ordenesFacturadas = OrdenTrabajoMantenimiento::where('estado', 'facturado')
            ->where('created_at', '>=', $fechaInicio)
            ->count();
            
        $ordenesEntregadas = OrdenTrabajoMantenimiento::where('estado', 'entregado')
            ->where('created_at', '>=', $fechaInicio)
            ->count();
        
        // Sumar órdenes completadas (finalizadas, facturadas y entregadas)
        $ordenesCompletadas = $ordenesFinalizadas + $ordenesFacturadas + $ordenesEntregadas;
        
        // Estadísticas de facturación
        $facturacionTotal = FacturaOrdenTrabajo::where('fecha_emision', '>=', $fechaInicio)
            ->sum('total');
        
        // Datos para el gráfico de facturación mensual
        $facturacionMensualLabels = [];
        $facturacionMensualValores = [];
        
        // Si el período es 'year', mostrar facturación por mes
        if ($period === 'year') {
            $mesesAtras = 11; // Últimos 12 meses
            
            for ($i = $mesesAtras; $i >= 0; $i--) {
                $mes = Carbon::now()->subMonths($i);
                $facturacionMensualLabels[] = $mes->translatedFormat('M Y');
                
                $totalMes = FacturaOrdenTrabajo::whereBetween('fecha_emision', [
                        $mes->copy()->startOfMonth(),
                        $mes->copy()->endOfMonth()
                    ])
                    ->sum('total');
                
                $facturacionMensualValores[] = $totalMes;
            }
        } 
        // Si el período es 'month', mostrar facturación por semana
        elseif ($period === 'month') {
            $fechaInicio = Carbon::now()->startOfMonth();
            $fechaFin = Carbon::now()->endOfMonth();
            $diasPorSemana = 7;
            
            for ($fecha = $fechaInicio->copy(); $fecha->lte($fechaFin); $fecha->addDays($diasPorSemana)) {
                $finSemana = $fecha->copy()->addDays($diasPorSemana - 1)->lte($fechaFin) 
                    ? $fecha->copy()->addDays($diasPorSemana - 1) 
                    : $fechaFin->copy();
                
                $facturacionMensualLabels[] = $fecha->format('d') . ' - ' . $finSemana->format('d') . ' ' . $fecha->translatedFormat('M');
                
                $totalSemana = FacturaOrdenTrabajo::whereBetween('fecha_emision', [
                        $fecha->copy()->startOfDay(),
                        $finSemana->copy()->endOfDay()
                    ])
                    ->sum('total');
                
                $facturacionMensualValores[] = $totalSemana;
            }
        } 
        // Si el período es 'week', mostrar facturación por día
        elseif ($period === 'week') {
            $fechaInicio = Carbon::now()->startOfWeek();
            $fechaFin = Carbon::now()->endOfWeek();
            
            for ($fecha = $fechaInicio->copy(); $fecha->lte($fechaFin); $fecha->addDay()) {
                $facturacionMensualLabels[] = $fecha->translatedFormat('D d');
                
                $totalDia = FacturaOrdenTrabajo::whereDate('fecha_emision', $fecha->toDateString())
                    ->sum('total');
                
                $facturacionMensualValores[] = $totalDia;
            }
        } 
        // Si el período es 'day', mostrar facturación por hora
        else {
            $fechaInicio = Carbon::now()->startOfDay();
            $fechaFin = Carbon::now()->endOfDay();
            $horasPorBloque = 3;
            
            for ($hora = 0; $hora < 24; $hora += $horasPorBloque) {
                $inicioBloque = $fechaInicio->copy()->addHours($hora);
                $finBloque = $fechaInicio->copy()->addHours($hora + $horasPorBloque);
                
                $facturacionMensualLabels[] = $inicioBloque->format('H:i') . ' - ' . $finBloque->format('H:i');
                
                $totalBloque = FacturaOrdenTrabajo::whereBetween('fecha_emision', [
                        $inicioBloque,
                        $finBloque
                    ])
                    ->sum('total');
                
                $facturacionMensualValores[] = $totalBloque;
            }
        }
        
        return [
            'citas_pendientes' => $citasPendientes,
            'ordenes_diagnostico' => $ordenesDiagnostico,
            'ordenes_espera_aprobacion' => $ordenesEsperaAprobacion,
            'ordenes_en_progreso' => $ordenesEnProgreso,
            'ordenes_en_proceso' => $ordenesDiagnostico + $ordenesEsperaAprobacion + $ordenesEnProgreso,
            'ordenes_finalizadas' => $ordenesFinalizadas,
            'ordenes_facturadas' => $ordenesFacturadas,
            'ordenes_entregadas' => $ordenesEntregadas,
            'ordenes_completadas' => $ordenesCompletadas,
            'facturacion_total' => $facturacionTotal,
            'facturacion_mensual_labels' => $facturacionMensualLabels,
            'facturacion_mensual_valores' => $facturacionMensualValores,
        ];
    }
    
    /**
     * Obtener fecha de inicio según el período
     */
    private function obtenerFechaInicio($period)
    {
        switch ($period) {
            case 'day':
                return Carbon::now()->startOfDay();
            case 'week':
                return Carbon::now()->startOfWeek();
            case 'month':
                return Carbon::now()->startOfMonth();
            case 'year':
                return Carbon::now()->startOfYear();
            default:
                return Carbon::now()->startOfWeek();
        }
    }
}