<?php

namespace App\Http\Controllers\Admin\Almacenes;

use App\Http\Controllers\Controller;
use App\Models\Inventario;
use App\Models\Almacen;
use App\Models\CentroCosto;
use App\Models\Movimiento;
use App\Models\Parte;
use App\Models\Vehiculo;
use App\Models\Marca;
use App\Models\Modelo;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Inventario::query();
        
        // Eager loading según el tipo de filtro
        if ($request->tipo == 'vehiculos') {
            $query->whereNotNull('vehiculo_id')
                  ->with(['vehiculo.marca', 'vehiculo.modelo', 'vehiculo.version', 'almacen.centroCosto']);
        } elseif ($request->tipo == 'partes') {
            $query->whereNotNull('parte_id')
                  ->with(['parte' => fn($q) => $q->select('id', 'nombre', 'codigo'), 'almacen.centroCosto']);
        } else {
            // Ambos tipos
            $query->with([
                'parte' => fn($q) => $q->select('id', 'nombre', 'codigo'),
                'vehiculo.marca', 'vehiculo.modelo', 'vehiculo.version',
                'almacen.centroCosto' => fn($q) => $q->select('id', 'nombre')
            ]);
        }
    
        // Filtros adicionales
        if ($request->filled('almacen_id')) {
            $query->where('almacen_id', $request->almacen_id);
        }
    
        if ($request->filled('centro_costo_id')) {
            $query->whereHas('almacen', fn($q) => 
                $q->where('centro_costo_id', $request->centro_costo_id)
            );
        }
    
        if ($request->filled('search')) {
            $search = "%{$request->search}%";
            $query->where(function($q) use ($search) {
                $q->whereHas('parte', fn($sq) => 
                    $sq->where('nombre', 'like', $search)
                       ->orWhere('codigo', 'like', $search)
                )
                ->orWhereHas('vehiculo.marca', fn($sq) => 
                    $sq->where('nombre', 'like', $search)
                )
                ->orWhereHas('vehiculo.modelo', fn($sq) => 
                    $sq->where('nombre', 'like', $search)
                );
            });
        }
    
        $inventarios = $query->paginate(25)->appends($request->query());
    
        $almacenes = Almacen::select('id', 'nombre', 'centro_costo_id')
            ->with(['centroCosto' => fn($q) => $q->select('id', 'nombre')])
            ->get();
        $centrosCostos = CentroCosto::select('id', 'nombre')->get();
    
        return view('admin.inventario.index', [
            'inventarios' => $inventarios,
            'almacenes' => $almacenes,
            'centrosCostos' => $centrosCostos,
            'filters' => $request->all()
        ]);
    }

    public function kardex(Inventario $inventario)
    {
        $movimientos = Movimiento::with([
            'tipoMovimiento' => fn($q) => $q->select('id', 'nombre', 'afecta_stock'),
            'usuario' => fn($q) => $q->select('id', 'name'),
            'documento', // Sin especificar campos para relaciones polimórficas
        ])
            ->where('parte_id', $inventario->parte_id)
            ->where('almacen_id', $inventario->almacen_id)
            ->when($inventario->centro_costo_id, fn($q) => 
                $q->where('centro_costo_id', $inventario->centro_costo_id)
            )
            ->orderBy('fecha_movimiento', 'desc')
            ->paginate(50);

        $saldo = $this->calcularSaldo($inventario->parte_id, $inventario->almacen_id, $inventario->centro_costo_id);

        // Agregar estas líneas para definir todas las variables que la vista necesita
        $partes = Parte::select('id', 'nombre', 'codigo')->orderBy('nombre')->get();
        $almacenes = Almacen::select('id', 'nombre')->orderBy('nombre')->get();
        $vehiculos = Vehiculo::with(['marca', 'modelo', 'version', 'anioModelo'])->get(); 

        return view('admin.inventario.kardex.index', [
            'inventario' => $inventario->load([
                'parte' => fn($q) => $q->select('id', 'nombre', 'codigo'),
                'almacen' => fn($q) => $q->select('id', 'nombre')
            ]),
            'movimientos' => $movimientos,
            'saldo' => $saldo,
            'partes' => $partes,
            'almacenes' => $almacenes,
            'vehiculos' => $vehiculos 
        ]);
    }

    /**
     * Muestra el formulario para generar un reporte de kardex.
     *
     * @return \Illuminate\View\View
     */
    public function kardexForm(Request $request)
    {
        // Obtener parámetros de filtro
        $inventarioId = $request->query('inventario_id');
        $almacenId = $request->query('almacen_id');
        $tipoItem = $request->query('tipo_item');
        $fechaDesde = $request->query('fecha_desde');
        $fechaHasta = $request->query('fecha_hasta');

        // Consultar movimientos
        $query = Movimiento::with([
            'tipoMovimiento' => fn($q) => $q->select('id', 'nombre', 'afecta_stock'),
            'usuario' => fn($q) => $q->select('id', 'name'),
            'documento', // Sin especificar campos para relaciones polimórficas
            'almacen' => fn($q) => $q->select('id', 'nombre'),
            'parte' => fn($q) => $q->select('id', 'nombre', 'codigo'),
            'vehiculo.marca', 'vehiculo.modelo', 'vehiculo.version'
        ]);

        if ($inventarioId) {
            $inventario = Inventario::findOrFail($inventarioId);
            $query->where('parte_id', $inventario->parte_id)
                ->where('almacen_id', $inventario->almacen_id)
                ->when($inventario->centro_costo_id, fn($q) => 
                    $q->where('centro_costo_id', $inventario->centro_costo_id)
                );
        }

        if ($almacenId) {
            $query->where('almacen_id', $almacenId);
        }

        if ($tipoItem == 'parte') {
            $query->whereNotNull('parte_id');
        }

        if ($fechaDesde) {
            $query->whereDate('fecha_movimiento', '>=', $fechaDesde);
        }

        if ($fechaHasta) {
            $query->whereDate('fecha_movimiento', '<=', $fechaHasta);
        }

        $movimientos = $query->orderBy('fecha_movimiento', 'desc')->paginate(50)->appends($request->query());

        // Asegurarse de que $inventarios siempre esté definido
        $inventarios = Inventario::with([
            'parte' => fn($q) => $q->select('id', 'nombre', 'codigo'),
            'vehiculo.marca' => fn($q) => $q->select('id', 'nombre'),
            'vehiculo.modelo' => fn($q) => $q->select('id', 'nombre')
        ])->get();

        $almacenes = Almacen::select('id', 'nombre')->orderBy('nombre')->get();

        return view('admin.inventario.kardex.consulta', [
            'movimientos' => $movimientos,
            'inventarios' => $inventarios,
            'almacenes' => $almacenes,
            'filters' => $request->all()
        ]);
    }

    /**
     * Genera el reporte de kardex basado en los filtros proporcionados.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function kardexReporte(Request $request)
    {
        // Validaciones según el tipo de reporte
        if ($request->tipo_reporte == 'parte' || !$request->filled('tipo_reporte')) {
            $request->validate([
                'parte_id' => 'required|exists:partes,id',
                'almacen_id' => 'nullable|exists:almacenes,id',
                'fecha_inicio' => 'nullable|date',
                'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio'
            ]);

            return $this->generarReportePartes($request);
        } else {
            $request->validate([
                'vehiculo_id' => 'required|exists:catalogos,id',
                'almacen_id' => 'nullable|exists:almacenes,id',
                'fecha_inicio' => 'nullable|date',
                'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio'
            ]);

            return $this->generarReporteVehiculos($request);
        }
    }

    /**
     * Genera un reporte de kardex para partes.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    protected function generarReportePartes(Request $request)
    {
        $query = Movimiento::with([
            'tipoMovimiento' => fn($q) => $q->select('id', 'nombre', 'afecta_stock'),
            'usuario' => fn($q) => $q->select('id', 'name'),
            'documento', // Sin especificar campos para relaciones polimórficas,
            'almacen' => fn($q) => $q->select('id', 'nombre')
        ])
            ->where('parte_id', $request->parte_id);
    
        if ($request->filled('almacen_id')) {
            $query->where('almacen_id', $request->almacen_id);
        }
    
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_movimiento', '>=', $request->fecha_inicio);
        }
    
        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_movimiento', '<=', $request->fecha_fin);
        }
    
        $movimientos = $query->orderBy('fecha_movimiento', 'desc')->paginate(50)->appends($request->query());
    
        $saldo = $this->calcularSaldo(
            $request->parte_id,
            $request->almacen_id ?? null,
            null // Asumimos que no se filtra por centro de costo en este caso
        );
    
        // Obtener la información de stock actual para la parte
        $stockActualQuery = Inventario::where('parte_id', $request->parte_id)
            ->with(['almacen' => fn($q) => $q->select('id', 'nombre')]);
        
        if ($request->filled('almacen_id')) {
            $stockActualQuery->where('almacen_id', $request->almacen_id);
        }
        
        $stockActual = $stockActualQuery->get();
    
        $partes = Parte::select('id', 'nombre', 'codigo')->orderBy('nombre')->get();
        $almacenes = Almacen::select('id', 'nombre')->orderBy('nombre')->get();
    
        $parte = Parte::with('unidad')->find($request->parte_id);
    
        return view('admin.inventario.kardex.reporte', [
            'movimientos' => $movimientos,
            'saldo' => $saldo,
            'parte' => $parte,
            'almacen' => $request->almacen_id ? Almacen::find($request->almacen_id) : null,
            'partes' => $partes,
            'almacenes' => $almacenes,
            'filters' => $request->all(),
            'stockActual' => $stockActual,
            'tipoReporte' => 'parte'
        ]);
    }

    /**
     * Genera un reporte de kardex para vehículos.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    protected function generarReporteVehiculos(Request $request)
    {
        // Similar al código de partes pero adaptado para vehículos
        $query = Movimiento::with([
            'tipoMovimiento' => fn($q) => $q->select('id', 'nombre', 'afecta_stock'),
            'usuario' => fn($q) => $q->select('id', 'name'),
            'documento', // Sin especificar campos para relaciones polimórficas,
            'almacen' => fn($q) => $q->select('id', 'nombre')
        ])
            ->where('vehiculo_id', $request->vehiculo_id);
    
        if ($request->filled('almacen_id')) {
            $query->where('almacen_id', $request->almacen_id);
        }
    
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_movimiento', '>=', $request->fecha_inicio);
        }
    
        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_movimiento', '<=', $request->fecha_fin);
        }
    
        $movimientos = $query->orderBy('fecha_movimiento', 'desc')->paginate(50)->appends($request->query());
    
        // Adaptamos el cálculo de saldo para vehículos
        $saldo = $this->calcularSaldoVehiculo(
            $request->vehiculo_id,
            $request->almacen_id ?? null,
            null
        );
    
        // Obtener la información de stock actual para el vehículo
        $stockActualQuery = Inventario::where('vehiculo_id', $request->vehiculo_id)
            ->with(['almacen' => fn($q) => $q->select('id', 'nombre')]);
        
        if ($request->filled('almacen_id')) {
            $stockActualQuery->where('almacen_id', $request->almacen_id);
        }
        
        $stockActual = $stockActualQuery->get();
    
        $vehiculos = Vehiculo::with(['marca', 'modelo', 'version', 'anioModelo'])->get();
        $almacenes = Almacen::select('id', 'nombre')->orderBy('nombre')->get();
    
        $vehiculo = Vehiculo::with(['marca', 'modelo', 'version', 'anioModelo'])->find($request->vehiculo_id);
    
        return view('admin.inventario.kardex.reporte_vehiculo', [
            'movimientos' => $movimientos,
            'saldo' => $saldo,
            'vehiculo' => $vehiculo,
            'almacen' => $request->almacen_id ? Almacen::find($request->almacen_id) : null,
            'vehiculos' => $vehiculos,
            'almacenes' => $almacenes,
            'filters' => $request->all(),
            'stockActual' => $stockActual,
            'tipoReporte' => 'vehiculo'
        ]);
    }

    /**
     * Calcula el saldo actual de un vehículo en el inventario.
     *
     * @param int $vehiculoId
     * @param int|null $almacenId
     * @param int|null $centroCostoId
     * @return int
     */
    protected function calcularSaldoVehiculo($vehiculoId, $almacenId = null, $centroCostoId = null)
    {
        $query = Movimiento::where('vehiculo_id', $vehiculoId)
            ->when($almacenId, fn($q) => $q->where('almacen_id', $almacenId))
            ->when($centroCostoId, fn($q) => $q->where('centro_costo_id', $centroCostoId))
            ->with('tipoMovimiento');

        $movimientos = $query->get();

        $saldo = 0;
        foreach ($movimientos as $movimiento) {
            $cantidad = $movimiento->cantidad;
            $saldo += $movimiento->tipoMovimiento->afecta_stock ? $cantidad : -$cantidad;
        }

        return $saldo;
    }

    /**
     * Calcula el saldo actual de una parte en el inventario.
     *
     * @param int $parteId
     * @param int|null $almacenId
     * @param int|null $centroCostoId
     * @return int
     */
    protected function calcularSaldo($parteId, $almacenId = null, $centroCostoId = null)
    {
        $query = Movimiento::where('parte_id', $parteId)
            ->when($almacenId, fn($q) => $q->where('almacen_id', $almacenId))
            ->when($centroCostoId, fn($q) => $q->where('centro_costo_id', $centroCostoId))
            ->with('tipoMovimiento');

        $movimientos = $query->get();

        $saldo = 0;
        foreach ($movimientos as $movimiento) {
            $cantidad = $movimiento->cantidad;
            $saldo += $movimiento->tipoMovimiento->afecta_stock ? $cantidad : -$cantidad;
        }

        return $saldo;
    }
}