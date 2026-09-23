<?php

namespace App\Http\Controllers\Admin\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Cotizacion;
use App\Models\DocumentoSunarp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentoSunarpController extends Controller
{
    /**
     * Almacena un nuevo documento SUNARP en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cotizacion  $cotizacion
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Cotizacion $cotizacion)
{
    $request->validate([
        'nombre' => 'required|string|max:255',
        'tipo' => 'required|string|max:50',
        'fecha' => 'required|date',
        'archivo' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120', // Cambiado a nullable
    ]);

    $documento = new DocumentoSunarp();
    $documento->cotizacion_id = $cotizacion->id;
    $documento->nombre = $request->nombre;
    $documento->tipo = $request->tipo;
    $documento->fecha = $request->fecha;
    $documento->observaciones = $request->observaciones;
    $documento->user_id = auth()->id();

    if ($request->hasFile('archivo')) {
        $path = $request->file('archivo')->store('public/documentos_sunarp');
        $documento->archivo = str_replace('public/', '', $path);
        \Log::info('Archivo guardado en: ' . storage_path('app/public/' . $documento->archivo));
    }

    $documento->save();

    return redirect()->back()->with('success', 'Documento SUNARP registrado correctamente.');
}

    /**
     * Actualiza el documento SUNARP especificado en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cotizacion  $cotizacion
     * @param  \App\Models\DocumentoSunarp  $documento
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cotizacion $cotizacion, DocumentoSunarp $documento)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string|max:50',
            'fecha' => 'required|date',
            'archivo' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $documento->nombre = $request->nombre;
        $documento->tipo = $request->tipo;
        $documento->fecha = $request->fecha;
        $documento->observaciones = $request->observaciones;

        // Procesar el archivo
        if ($request->hasFile('archivo')) {
            // Eliminar el archivo anterior si existe
            if ($documento->archivo && !$request->has('mantener_archivo')) {
                Storage::delete('public/' . $documento->archivo);
            }
            
            $path = $request->file('archivo')->store('public/documentos_sunarp');
            $documento->archivo = str_replace('public/', '', $path);
        } elseif (!$request->has('mantener_archivo') && $documento->archivo) {
            // Eliminar el archivo si no se marca para mantener
            Storage::delete('public/' . $documento->archivo);
            $documento->archivo = null;
        }

        $documento->save();

        return redirect()->back()->with('success', 'Documento SUNARP actualizado correctamente.');
    }

    /**
     * Elimina el documento SUNARP especificado de la base de datos.
     *
     * @param  \App\Models\Cotizacion  $cotizacion
     * @param  \App\Models\DocumentoSunarp  $documento
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cotizacion $cotizacion, DocumentoSunarp $documento)
    {
        // Eliminar el archivo si existe
        if ($documento->archivo) {
            Storage::delete('public/' . $documento->archivo);
        }
        
        $documento->delete();
        
        return redirect()->back()->with('success', 'Documento SUNARP eliminado correctamente.');
    }
}