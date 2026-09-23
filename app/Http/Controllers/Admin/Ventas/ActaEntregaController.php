<?php

namespace App\Http\Controllers\Admin\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Cotizacion;
use App\Models\ActaEntrega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\PDF;

class ActaEntregaController extends Controller
{
    /**
     * Almacena una nueva acta de entrega en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cotizacion  $cotizacion
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Cotizacion $cotizacion)
    {
        $request->validate([
            'fecha_entrega' => 'required|date',
            'persona_entrega' => 'required|string|max:255',
            'vehiculo_detalle' => 'required|string|max:255',
            'kilometraje' => 'required|integer|min:0',
            'nivel_combustible' => 'required|integer|in:0,25,50,75,100',
            'estado' => 'required|string|in:En proceso,Completada',
            'documento_firmado' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $acta = new ActaEntrega();
        $acta->cotizacion_id = $cotizacion->id;
        $acta->codigo = ActaEntrega::generarCodigo();
        $acta->fecha_entrega = $request->fecha_entrega;
        $acta->persona_entrega = $request->persona_entrega;
        $acta->vehiculo_detalle = $request->vehiculo_detalle;
        $acta->placa = $request->placa;
        $acta->kilometraje = $request->kilometraje;
        $acta->nivel_combustible = $request->nivel_combustible;
        $acta->estado = $request->estado;
        $acta->observaciones = $request->observaciones;
        $acta->user_id = auth()->id();

        // Procesar los checkboxes del checklist
        $checklistItems = [
            'check_manual', 'check_garantia', 'check_tarjeta', 'check_soat',
            'check_llave', 'check_gata', 'check_rueda', 'check_herramientas',
            'check_carroceria', 'check_pintura', 'check_lunas', 'check_llantas',
            'check_asientos', 'check_tablero', 'check_radio', 'check_climatizacion',
            'check_motor', 'check_luces', 'check_frenos', 'check_direccion',
            'check_bateria', 'check_arranque'
        ];

        foreach ($checklistItems as $item) {
            $acta->$item = $request->has($item) ? 1 : 0;
        }

        // Almacenar el documento firmado si se proporciona
        if ($request->hasFile('documento_firmado')) {
            $path = $request->file('documento_firmado')->store('public/actas_entrega');
            $acta->documento_firmado = str_replace('public/', '', $path);
        }

        $acta->save();

        return redirect()->back()->with('success', 'Acta de entrega creada correctamente.');
    }

    /**
     * Actualiza el acta de entrega especificada en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cotizacion  $cotizacion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cotizacion $cotizacion)
    {
        $request->validate([
            'fecha_entrega' => 'required|date',
            'persona_entrega' => 'required|string|max:255',
            'vehiculo_detalle' => 'required|string|max:255',
            'kilometraje' => 'required|integer|min:0',
            'nivel_combustible' => 'required|integer|in:0,25,50,75,100',
            'estado' => 'required|string|in:En proceso,Completada',
            'documento_firmado' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $acta = $cotizacion->acta_entrega;
        
        if (!$acta) {
            return redirect()->back()->with('error', 'No se encontró el acta de entrega.');
        }

        $acta->fecha_entrega = $request->fecha_entrega;
        $acta->persona_entrega = $request->persona_entrega;
        $acta->vehiculo_detalle = $request->vehiculo_detalle;
        $acta->placa = $request->placa;
        $acta->kilometraje = $request->kilometraje;
        $acta->nivel_combustible = $request->nivel_combustible;
        $acta->estado = $request->estado;
        $acta->observaciones = $request->observaciones;

        // Procesar los checkboxes del checklist
        $checklistItems = [
            'check_manual', 'check_garantia', 'check_tarjeta', 'check_soat',
            'check_llave', 'check_gata', 'check_rueda', 'check_herramientas',
            'check_carroceria', 'check_pintura', 'check_lunas', 'check_llantas',
            'check_asientos', 'check_tablero', 'check_radio', 'check_climatizacion',
            'check_motor', 'check_luces', 'check_frenos', 'check_direccion',
            'check_bateria', 'check_arranque'
        ];

        foreach ($checklistItems as $item) {
            $acta->$item = $request->has($item) ? 1 : 0;
        }

        // Procesar el documento firmado
        if ($request->hasFile('documento_firmado')) {
            // Eliminar el archivo anterior si existe
            if ($acta->documento_firmado && !$request->has('mantener_documento')) {
                Storage::delete('public/' . $acta->documento_firmado);
            }
            
            $path = $request->file('documento_firmado')->store('public/actas_entrega');
            $acta->documento_firmado = str_replace('public/', '', $path);
        } elseif (!$request->has('mantener_documento') && $acta->documento_firmado) {
            // Eliminar el archivo si no se marca para mantener
            Storage::delete('public/' . $acta->documento_firmado);
            $acta->documento_firmado = null;
        }

        $acta->save();

        return redirect()->back()->with('success', 'Acta de entrega actualizada correctamente.');
    }

    public function generarPDF(Cotizacion $cotizacion)
    {
        if (!$cotizacion->acta_entrega) {
            return redirect()->back()->with('error', 'No se encontró el acta de entrega.');
        }
    
        $pdf = PDF::loadView('admin.ventas.cotizaciones.proceso.acta-entrega.pdf', [
            'cotizacion' => $cotizacion,
            'acta' => $cotizacion->acta_entrega,
            'cliente' => $cotizacion->cliente
        ]);
        
        return $pdf->download('acta_entrega_' . $cotizacion->acta_entrega->codigo . '.pdf');
    }
}