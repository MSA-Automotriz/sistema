<?php

namespace App\Http\Controllers\Admin\Compras;

use App\Http\Controllers\Controller;
use App\Models\GuiaEntrega;
use App\Models\DetalleGuiaEntrega;
use App\Models\Proveedor;
use App\Models\Parte;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GuiaEntregaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $guias = GuiaEntrega::with(['proveedor', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.compras.documentos.guias.index', compact('guias'));
    }

    public function create()
    {
        $proveedores = Proveedor::orderBy('razon_social')->get();
        return view('admin.compras.documentos.guias.create', compact('proveedores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'fecha' => 'required|date',
            'transportista' => 'nullable|string|max:255',
            'placa_vehiculo' => 'nullable|string|max:20',
            'conductor' => 'nullable|string|max:255',
            'dni_conductor' => 'nullable|string|max:20',
            'observaciones' => 'nullable|string',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|integer',
            'productos.*.tipo' => 'required|in:parte,vehiculo',
            'productos.*.cantidad' => 'required|numeric|min:0.01',
            'productos.*.precio' => 'required|numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            // Crear la guía de entrega
            $guia = GuiaEntrega::create([
                'numero' => GuiaEntrega::generarNumero(),
                'fecha' => $request->fecha,
                'proveedor_id' => $request->proveedor_id,
                'transportista' => $request->transportista,
                'placa_vehiculo' => $request->placa_vehiculo,
                'conductor' => $request->conductor,
                'dni_conductor' => $request->dni_conductor,
                'observaciones' => $request->observaciones,
                'estado' => 'pendiente',
                'usuario_id' => Auth::id()
            ]);

            // Crear los detalles
            foreach ($request->productos as $producto) {
                $item = null;
                if ($producto['tipo'] === 'parte') {
                    $item = Parte::find($producto['id']);
                } else {
                    $item = Vehiculo::find($producto['id']);
                }

                if ($item) {
                    DetalleGuiaEntrega::create([
                        'guia_entrega_id' => $guia->id,
                        'producto_id' => $producto['id'],
                        'tipo_producto' => $producto['tipo'],
                        'codigo_producto' => $item->codigo ?? 'N/A',
                        'nombre_producto' => $item->nombre ?? 'N/A',
                        'cantidad_enviada' => $producto['cantidad'],
                        'precio_unitario' => $producto['precio']
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.guias.show', $guia->id)
                ->with('success', 'Guía de entrega creada correctamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Error al crear la guía de entrega: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $guia = GuiaEntrega::with(['proveedor', 'usuario', 'detalles', 'recibidoPor'])
            ->findOrFail($id);
        
        return view('admin.compras.documentos.guias.show', compact('guia'));
    }

    public function edit($id)
    {
        $guia = GuiaEntrega::with(['proveedor', 'detalles'])
            ->findOrFail($id);
        
        // Solo permitir editar si está en estado pendiente
        if ($guia->estado !== 'pendiente') {
            return redirect()->route('admin.guias.show', $id)
                ->with('error', 'Solo se pueden editar guías de entrega en estado pendiente.');
        }
        
        $proveedores = Proveedor::orderBy('razon_social')->get();
        
        return view('admin.compras.documentos.guias.edit', compact('guia', 'proveedores'));
    }

    public function update(Request $request, $id)
    {
        $guia = GuiaEntrega::findOrFail($id);
        
        // Solo permitir actualizar si está en estado pendiente
        if ($guia->estado !== 'pendiente') {
            return redirect()->route('admin.guias.show', $id)
                ->with('error', 'Solo se pueden editar guías de entrega en estado pendiente.');
        }
        
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'fecha' => 'required|date',
            'transportista' => 'nullable|string|max:255',
            'placa_vehiculo' => 'nullable|string|max:20',
            'conductor' => 'nullable|string|max:255',
            'dni_conductor' => 'nullable|string|max:20',
            'observaciones' => 'nullable|string',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|integer',
            'productos.*.tipo' => 'required|in:parte,vehiculo',
            'productos.*.cantidad' => 'required|numeric|min:0.01',
            'productos.*.precio' => 'required|numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            // Actualizar la guía de entrega
            $guia->update([
                'fecha' => $request->fecha,
                'proveedor_id' => $request->proveedor_id,
                'transportista' => $request->transportista,
                'placa_vehiculo' => $request->placa_vehiculo,
                'conductor' => $request->conductor,
                'dni_conductor' => $request->dni_conductor,
                'observaciones' => $request->observaciones
            ]);

            // Eliminar detalles existentes
            $guia->detalles()->delete();

            // Crear los nuevos detalles
            foreach ($request->productos as $producto) {
                $item = null;
                if ($producto['tipo'] === 'parte') {
                    $item = Parte::find($producto['id']);
                } else {
                    $item = Vehiculo::find($producto['id']);
                }

                if ($item) {
                    DetalleGuiaEntrega::create([
                        'guia_entrega_id' => $guia->id,
                        'producto_id' => $producto['id'],
                        'tipo_producto' => $producto['tipo'],
                        'codigo_producto' => $item->codigo ?? 'N/A',
                        'nombre_producto' => $item->nombre ?? 'N/A',
                        'cantidad_enviada' => $producto['cantidad'],
                        'precio_unitario' => $producto['precio']
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.guias.show', $guia->id)
                ->with('success', 'Guía de entrega actualizada correctamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Error al actualizar la guía de entrega: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $guia = GuiaEntrega::findOrFail($id);
        
        // Solo permitir eliminar si está en estado pendiente
        if ($guia->estado !== 'pendiente') {
            return redirect()->route('admin.guias.index')
                ->with('error', 'Solo se pueden eliminar guías de entrega en estado pendiente.');
        }
        
        try {
            $numero = $guia->numero;
            $guia->delete();
            
            return redirect()->route('admin.guias.index')
                ->with('success', "Guía de entrega {$numero} eliminada correctamente.");
        } catch (\Exception $e) {
            return redirect()->route('admin.guias.index')
                ->with('error', 'Error al eliminar la guía de entrega: ' . $e->getMessage());
        }
    }

    public function marcarEnTransito($id)
    {
        $guia = GuiaEntrega::findOrFail($id);
        
        if ($guia->estado !== 'pendiente') {
            return redirect()->route('admin.guias.show', $id)
                ->with('error', 'Solo se pueden marcar como en tránsito las guías pendientes.');
        }
        
        $guia->update(['estado' => 'en_transito']);
        
        return redirect()->route('admin.guias.show', $id)
            ->with('success', 'Guía marcada como en tránsito.');
    }

    public function marcarRecibida($id)
    {
        $guia = GuiaEntrega::findOrFail($id);
        
        if (!in_array($guia->estado, ['pendiente', 'en_transito'])) {
            return redirect()->route('admin.guias.show', $id)
                ->with('error', 'Solo se pueden marcar como recibidas las guías pendientes o en tránsito.');
        }
        
        $guia->update([
            'estado' => 'recibida',
            'recibido_por' => Auth::id(),
            'fecha_recepcion' => now()
        ]);
        
        return redirect()->route('admin.guias.show', $id)
            ->with('success', 'Guía marcada como recibida.');
    }
}