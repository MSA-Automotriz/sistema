<?php

namespace App\Http\Controllers\Admin\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Cotizacion;
use App\Models\NotaPedido;
use App\Models\NotaPedidoItem;
use App\Models\Vehiculo;
use App\Models\Servicio;
use App\Models\Parte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\PDF;
use Illuminate\Support\Facades\Storage;

class NotaPedidoController extends Controller
{
    /**
     * Almacena un nuevo ítem en la nota de pedido.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cotizacion  $cotizacion
     * @return \Illuminate\Http\Response
     */
    public function storeItem(Request $request, Cotizacion $cotizacion)
    {
        $request->validate([
            'tipo' => 'required|string|in:vehiculo,servicio,parte,otro',
            'cantidad' => 'required|numeric|min:0.01',
            'precio_unitario' => 'required|numeric|min:0',
        ]);

        // Verificar si existe una nota de pedido, si no, crear una
        if (!$cotizacion->nota_pedido) {
            $notaPedido = new NotaPedido();
            $notaPedido->cotizacion_id = $cotizacion->id;
            $notaPedido->codigo = NotaPedido::generarCodigo();
            $notaPedido->fecha_emision = now();
            $notaPedido->estado = 'Pendiente';
            $notaPedido->user_id = auth()->id();
            $notaPedido->save();
        } else {
            $notaPedido = $cotizacion->nota_pedido;
        }

        // Crear el ítem según el tipo
        $item = new NotaPedidoItem();
        $item->nota_pedido_id = $notaPedido->id;
        $item->tipo = $request->tipo;
        $item->cantidad = $request->cantidad;
        $item->precio_unitario = $request->precio_unitario;
        $item->detalles = $request->detalles;

        switch ($request->tipo) {
            case 'vehiculo':
                $request->validate([
                    'vehiculo_id' => 'required|exists:catalogos,id',
                ]);
                $vehiculo = Vehiculo::with(['marca', 'modelo', 'version', 'anioModelo'])->find($request->vehiculo_id);
                $item->item_id = $vehiculo->id;
                $item->item_type = Vehiculo::class;
                $item->descripcion = $vehiculo->marca->nombre . ' ' . $vehiculo->modelo->nombre . ' ' . $vehiculo->version->nombre . ' - ' . $vehiculo->anioModelo->anio;
                break;
                
            case 'servicio':
                $request->validate([
                    'servicio_id' => 'required|exists:servicios,id',
                ]);
                $servicio = Servicio::with('categoria')->find($request->servicio_id);
                $item->item_id = $servicio->id;
                $item->item_type = Servicio::class;
                $item->descripcion = $servicio->nombre . ' - ' . ($servicio->categoria ? $servicio->categoria->nombre : 'Sin categoría');
                break;
                
            case 'parte':
                $request->validate([
                    'parte_id' => 'required|exists:partes,id',
                ]);
                $parte = Parte::with(['categoriaParte', 'fabricante'])->find($request->parte_id);
                $item->item_id = $parte->id;
                $item->item_type = Parte::class;
                $item->descripcion = $parte->codigo . ' - ' . $parte->nombre . ' (' . ($parte->fabricante ? $parte->fabricante->nombre : 'Sin fabricante') . ')';
                break;
                
            case 'otro':
                $request->validate([
                    'descripcion' => 'required|string|max:255',
                    'subtipo' => 'required|string|max:50',
                ]);
                $item->descripcion = $request->descripcion;
                $item->subtipo = $request->subtipo;
                break;
        }

        $item->save();

        return redirect()->back()->with('success', 'Ítem agregado correctamente a la nota de pedido.');
    }

    /**
     * Actualiza un ítem específico de la nota de pedido.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cotizacion  $cotizacion
     * @param  \App\Models\NotaPedidoItem  $item
     * @return \Illuminate\Http\Response
     */
    public function updateItem(Request $request, Cotizacion $cotizacion, NotaPedidoItem $item)
    {
        $request->validate([
            'descripcion' => 'required|string|max:255',
            'cantidad' => 'required|numeric|min:0.01',
            'precio_unitario' => 'required|numeric|min:0',
        ]);

        $item->descripcion = $request->descripcion;
        $item->cantidad = $request->cantidad;
        $item->precio_unitario = $request->precio_unitario;
        $item->detalles = $request->detalles;
        $item->save();

        return redirect()->back()->with('success', 'Ítem actualizado correctamente.');
    }

    /**
     * Elimina un ítem específico de la nota de pedido.
     *
     * @param  \App\Models\Cotizacion  $cotizacion
     * @param  \App\Models\NotaPedidoItem  $item
     * @return \Illuminate\Http\Response
     */
    public function destroyItem(Cotizacion $cotizacion, NotaPedidoItem $item)
    {
        $item->delete();
        
        // Si ya no hay items, verificar si se quiere eliminar la nota de pedido
        if ($cotizacion->nota_pedido && $cotizacion->nota_pedido->items->count() === 0) {
            // Opcionalmente, se podría eliminar la nota de pedido
            // $cotizacion->nota_pedido->delete();
        }
        
        return redirect()->back()->with('success', 'Ítem eliminado correctamente.');
    }

    /**
     * Actualiza las observaciones y el estado de la nota de pedido.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cotizacion  $cotizacion
     * @return \Illuminate\Http\Response
     */
    public function updateObservaciones(Request $request, Cotizacion $cotizacion)
    {
        $request->validate([
            'observaciones' => 'nullable|string',
            'estado' => 'required|string|in:Pendiente,En proceso,Completada',
        ]);

        if ($cotizacion->nota_pedido) {
            $cotizacion->nota_pedido->observaciones = $request->observaciones;
            $cotizacion->nota_pedido->estado = $request->estado;
            $cotizacion->nota_pedido->save();
            
            return redirect()->back()->with('success', 'Nota de pedido actualizada correctamente.');
        }
        
        return redirect()->back()->with('error', 'No se encontró la nota de pedido.');
    }


    public function generarPDF(Cotizacion $cotizacion)
    {
        if (!$cotizacion->nota_pedido) {
            return redirect()->back()->with('error', 'No se encontró la nota de pedido.');
        }

        $pdf = PDF::loadView('admin.ventas.cotizaciones.proceso.nota-pedido.pdf', [
            'cotizacion' => $cotizacion,
            'nota_pedido' => $cotizacion->nota_pedido,
            'cliente' => $cotizacion->cliente,
            'items' => $cotizacion->nota_pedido->items
        ]);
        
        return $pdf->download('nota_pedido_' . $cotizacion->nota_pedido->codigo . '.pdf');
    }
}