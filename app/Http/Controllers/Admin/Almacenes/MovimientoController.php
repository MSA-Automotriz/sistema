<?php

namespace App\Http\Controllers\Admin\Almacenes;

use App\Http\Controllers\Controller;
use App\Models\Movimiento;
use App\Models\TipoMovimiento;
use App\Models\Parte;
use App\Models\Almacen;
use App\Models\CentroCosto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MovimientoController extends Controller
{
    public function index()
    {
        $movimientos = Movimiento::with(['tipoMovimiento', 'parte', 'almacen', 'usuario'])
            ->orderBy('fecha_movimiento', 'desc')
            ->paginate(25);

        return view('admin.inventario.movimientos.index', compact('movimientos'));
    }

    public function create()
    {
        $tiposMovimiento = TipoMovimiento::all();
        $partes = Parte::all();
        $almacenes = Almacen::all();
        $centrosCostos = CentroCosto::all();

        return view('admin.inventario.movimientos.create', compact(
            'tiposMovimiento',
            'partes',
            'almacenes',
            'centrosCostos'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo_movimiento_id' => 'required|exists:tipos_movimiento,id',
            'parte_id' => 'required|exists:partes,id',
            'almacen_id' => 'required|exists:almacenes,id',
            'centro_costo_id' => 'required|exists:centros_costos,id',
            'cantidad' => 'required|integer|min:1',
            'documento_referencia' => 'nullable|string|max:50',
            'fecha_movimiento' => 'required|date',
            'observaciones' => 'nullable|string'
        ]);

        Movimiento::create([
            'tipo_movimiento_id' => $request->tipo_movimiento_id,
            'parte_id' => $request->parte_id,
            'almacen_id' => $request->almacen_id,
            'centro_costo_id' => $request->centro_costo_id,
            'cantidad' => $request->cantidad,
            'documento_referencia' => $request->documento_referencia,
            'fecha_movimiento' => $request->fecha_movimiento,
            'usuario_id' => Auth::id(),
            'observaciones' => $request->observaciones
        ]);

        return redirect()->route('admin.inventario.movimientos.index')
            ->with('success', 'Movimiento registrado correctamente');
    }

    public function edit(Movimiento $movimiento)
    {
        $tiposMovimiento = TipoMovimiento::all();
        $partes = Parte::all();
        $almacenes = Almacen::all();
        $centrosCostos = CentroCosto::all();

        return view('admin.inventario.movimientos.edit', compact(
            'movimiento',
            'tiposMovimiento',
            'partes',
            'almacenes',
            'centrosCostos'
        ));
    }

    public function update(Request $request, Movimiento $movimiento)
    {
        $request->validate([
            'tipo_movimiento_id' => 'required|exists:tipos_movimiento,id',
            'parte_id' => 'required|exists:partes,id',
            'almacen_id' => 'required|exists:almacenes,id',
            'centro_costo_id' => 'required|exists:centros_costos,id',
            'cantidad' => 'required|integer|min:1',
            'documento_referencia' => 'nullable|string|max:50',
            'fecha_movimiento' => 'required|date',
            'observaciones' => 'nullable|string'
        ]);

        $movimiento->update($request->all());

        return redirect()->route('admin.inventario.movimientos.index')
            ->with('success', 'Movimiento actualizado correctamente');
    }

    public function destroy(Movimiento $movimiento)
    {
        $movimiento->delete();

        return redirect()->route('admin.inventario.movimientos.index')
            ->with('success', 'Movimiento eliminado correctamente');
    }
}