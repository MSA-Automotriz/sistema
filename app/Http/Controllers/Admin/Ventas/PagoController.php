<?php

namespace App\Http\Controllers\Admin\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Cotizacion;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PagoController extends Controller
{
    /**
     * Almacena un nuevo pago en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cotizacion  $cotizacion
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Cotizacion $cotizacion)
    {
        $request->validate([
            'concepto' => 'required|string|max:255',
            'fecha_pago' => 'required|date',
            'tipo' => 'required|string|in:Inicial,Parcial,Final',
            'monto' => 'required|numeric|min:0',
            'medio_pago' => 'required|string|max:50',
            'comprobante' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $pago = new Pago();
        $pago->cotizacion_id = $cotizacion->id;
        $pago->concepto = $request->concepto;
        $pago->fecha_pago = $request->fecha_pago;
        $pago->tipo = $request->tipo;
        $pago->monto = $request->monto;
        $pago->moneda = $cotizacion->moneda;
        $pago->medio_pago = $request->medio_pago;
        $pago->observaciones = $request->observaciones;
        $pago->user_id = auth()->id();

        // Almacenar el comprobante si se proporciona
        if ($request->hasFile('comprobante')) {
            $path = $request->file('comprobante')->store('public/pagos');
            $pago->comprobante = str_replace('public/', '', $path);
        }

        $pago->save();

        return redirect()->back()->with('success', 'Pago registrado correctamente.');
    }

    /**
     * Actualiza el pago especificado en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cotizacion  $cotizacion
     * @param  \App\Models\Pago  $pago
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cotizacion $cotizacion, Pago $pago)
    {
        $request->validate([
            'concepto' => 'required|string|max:255',
            'fecha_pago' => 'required|date',
            'tipo' => 'required|string|in:Inicial,Parcial,Final',
            'monto' => 'required|numeric|min:0',
            'medio_pago' => 'required|string|max:50',
            'comprobante' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $pago->concepto = $request->concepto;
        $pago->fecha_pago = $request->fecha_pago;
        $pago->tipo = $request->tipo;
        $pago->monto = $request->monto;
        $pago->medio_pago = $request->medio_pago;
        $pago->observaciones = $request->observaciones;

        // Procesar el comprobante
        if ($request->hasFile('comprobante')) {
            // Eliminar el archivo anterior si existe
            if ($pago->comprobante && !$request->has('mantener_comprobante')) {
                Storage::delete('public/' . $pago->comprobante);
            }
            
            $path = $request->file('comprobante')->store('public/pagos');
            $pago->comprobante = str_replace('public/', '', $path);
        } elseif (!$request->has('mantener_comprobante') && $pago->comprobante) {
            // Eliminar el archivo si no se marca para mantener
            Storage::delete('public/' . $pago->comprobante);
            $pago->comprobante = null;
        }

        $pago->save();

        return redirect()->back()->with('success', 'Pago actualizado correctamente.');
    }

    /**
     * Elimina el pago especificado de la base de datos.
     *
     * @param  \App\Models\Cotizacion  $cotizacion
     * @param  \App\Models\Pago  $pago
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cotizacion $cotizacion, Pago $pago)
    {
        // Eliminar el archivo de comprobante si existe
        if ($pago->comprobante) {
            Storage::delete('public/' . $pago->comprobante);
        }
        
        $pago->delete();
        
        return redirect()->back()->with('success', 'Pago eliminado correctamente.');
    }
}