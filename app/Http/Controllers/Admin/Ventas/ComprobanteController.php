<?php

namespace App\Http\Controllers\Admin\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Cotizacion;
use App\Models\Comprobante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ComprobanteController extends Controller
{
    /**
     * Almacena un nuevo comprobante en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cotizacion  $cotizacion
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Cotizacion $cotizacion)
    {
        $request->validate([
            'tipo' => 'required|string|in:Factura,Boleta',
            'serie' => 'required|string|max:20',
            'numero' => 'required|string|max:20',
            'fecha_emision' => 'required|date',
            'monto' => 'required|numeric|min:0',
            'moneda' => 'required|string|in:Soles,Dólares',
            'archivo' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $comprobante = new Comprobante();
        $comprobante->cotizacion_id = $cotizacion->id;
        $comprobante->tipo = $request->tipo;
        $comprobante->serie = $request->serie;
        $comprobante->numero = $request->numero;
        $comprobante->fecha_emision = $request->fecha_emision;
        $comprobante->monto = $request->monto;
        $comprobante->moneda = $request->moneda;
        $comprobante->detalle = $request->detalle;
        $comprobante->user_id = auth()->id();

        // Almacenar el archivo si se proporciona
        if ($request->hasFile('archivo')) {
            $path = $request->file('archivo')->store('public/comprobantes');
            $comprobante->archivo = str_replace('public/', '', $path);
        }

        $comprobante->save();

        return redirect()->back()->with('success', 'Comprobante registrado correctamente.');
    }

    /**
     * Actualiza el comprobante especificado en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cotizacion  $cotizacion
     * @param  \App\Models\Comprobante  $comprobante
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cotizacion $cotizacion, Comprobante $comprobante)
    {
        $request->validate([
            'tipo' => 'required|string|in:Factura,Boleta',
            'serie' => 'required|string|max:20',
            'numero' => 'required|string|max:20',
            'fecha_emision' => 'required|date',
            'monto' => 'required|numeric|min:0',
            'moneda' => 'required|string|in:Soles,Dólares',
            'archivo' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $comprobante->tipo = $request->tipo;
        $comprobante->serie = $request->serie;
        $comprobante->numero = $request->numero;
        $comprobante->fecha_emision = $request->fecha_emision;
        $comprobante->monto = $request->monto;
        $comprobante->moneda = $request->moneda;
        $comprobante->detalle = $request->detalle;

        // Procesar el archivo
        if ($request->hasFile('archivo')) {
            // Eliminar el archivo anterior si existe
            if ($comprobante->archivo && !$request->has('mantener_archivo')) {
                Storage::delete('public/' . $comprobante->archivo);
            }
            
            $path = $request->file('archivo')->store('public/comprobantes');
            $comprobante->archivo = str_replace('public/', '', $path);
        } elseif (!$request->has('mantener_archivo') && $comprobante->archivo) {
            // Eliminar el archivo si no se marca para mantener
            Storage::delete('public/' . $comprobante->archivo);
            $comprobante->archivo = null;
        }

        $comprobante->save();

        return redirect()->back()->with('success', 'Comprobante actualizado correctamente.');
    }

    /**
     * Elimina el comprobante especificado de la base de datos.
     *
     * @param  \App\Models\Cotizacion  $cotizacion
     * @param  \App\Models\Comprobante  $comprobante
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cotizacion $cotizacion, Comprobante $comprobante)
    {
        // Eliminar el archivo si existe
        if ($comprobante->archivo) {
            Storage::delete('public/' . $comprobante->archivo);
        }
        
        $comprobante->delete();
        
        return redirect()->back()->with('success', 'Comprobante eliminado correctamente.');
    }
}