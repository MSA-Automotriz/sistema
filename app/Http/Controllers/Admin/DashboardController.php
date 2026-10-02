<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Venta;
use App\Models\Cotizacion;
use App\Models\CitaMantenimiento;
use App\Models\OrdenTrabajoMantenimiento;
use App\Models\Cliente;
use App\Models\Inventario;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $totalUsuarios = null;
        if ($user->hasPermission('usuarios')) {
            $totalUsuarios = User::count();
        }

        $ventasMes = null;
        if ($user->hasPermission('ventas')) {
            try {
                $ventasMes = Venta::whereMonth('fecha', now()->month)
                    ->whereYear('fecha', now()->year)
                    ->sum('total');
            } catch (\Exception $e) {
                $ventasMes = 0;
            }
        }

        $ordenesPendientes = null;
        $citasPendientes = null;
        if ($user->hasPermission('mantenimiento')) {
            try {
                $ordenesPendientes = OrdenTrabajoMantenimiento::whereNotIn('estado', ['completado', 'entregado', 'cancelado'])->count();
                $citasPendientes = CitaMantenimiento::whereDate('fecha_hora_cita', today())->count();
            } catch (\Exception $e) {
                $ordenesPendientes = 0;
                $citasPendientes = 0;
            }
        }

        $stockCritico = null;
        if ($user->hasPermission('inventario') || $user->hasPermission('almacenes') || $user->hasPermission('compras')) {
            try {
                $stockCritico = Inventario::whereColumn('stock_disponible', '<=', 'stock_minimo')->count();
            } catch (\Exception $e) {
                $stockCritico = 0;
            }
        }

        $actividadReciente = [];
        if ($user->hasPermission('ventas')) {
            $actividadReciente['cotizaciones'] = Cotizacion::where('created_at', '>=', now()->subDays(30))->count();
            $actividadReciente['ventas'] = Venta::where('created_at', '>=', now()->subDays(30))->count();
        }
        if ($user->hasPermission('mantenimiento')) {
            $actividadReciente['ordenes'] = OrdenTrabajoMantenimiento::where('created_at', '>=', now()->subDays(30))->count();
        }
        if ($user->hasPermission('clientes') || $user->hasPermission('ventas')) {
            $actividadReciente['clientes'] = Cliente::where('created_at', '>=', now()->subDays(30))->count();
        }

        return view('admin.dashboard.index', compact(
            'totalUsuarios',
            'ventasMes',
            'ordenesPendientes',
            'citasPendientes',
            'stockCritico',
            'actividadReciente'
        ));
    }
}