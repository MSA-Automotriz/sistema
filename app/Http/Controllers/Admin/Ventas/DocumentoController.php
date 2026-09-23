<?php

namespace App\Http\Controllers\Admin\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Cotizacion;
use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // Importar el facade Log

class DocumentoController extends Controller
{
    /**
     * Almacena un nuevo documento en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cotizacion  $cotizacion
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Cotizacion $cotizacion)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria' => 'required|string|max:50',
            'fecha' => 'required|date',
            'archivo' => 'nullable|file|mimes:jpeg,png,jpg,pdf,doc,docx,xls,xlsx|max:10240',
            'descripcion' => 'nullable|string',
        ]);

        $documento = new Documento();
        $documento->cotizacion_id = $cotizacion->id;
        $documento->nombre = $request->nombre;
        $documento->categoria = $request->categoria;
        $documento->fecha = $request->fecha;
        $documento->descripcion = $request->descripcion;
        $documento->user_id = auth()->id();

        // Almacenar el archivo
        if ($request->hasFile('archivo')) {
            $path = $request->file('archivo')->store('public/documentos');
            $documento->archivo = str_replace('public/', '', $path);
            Log::info('Archivo guardado en: ' . storage_path('app/public/' . $documento->archivo));
        }

        $documento->save();

        return redirect()->back()->with('success', 'Documento registrado correctamente.');
    }

    /**
     * Actualiza el documento especificado en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cotizacion  $cotizacion
     * @param  \App\Models\Documento  $documento
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cotizacion $cotizacion, Documento $documento)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria' => 'required|string|max:50',
            'fecha' => 'required|date',
            'archivo' => 'nullable|file|mimes:jpeg,png,jpg,pdf,doc,docx,xls,xlsx|max:10240',
            'descripcion' => 'nullable|string',
        ]);

        $documento->nombre = $request->nombre;
        $documento->categoria = $request->categoria;
        $documento->fecha = $request->fecha;
        $documento->descripcion = $request->descripcion;

        // Procesar el archivo
        if ($request->hasFile('archivo')) {
            // Eliminar el archivo anterior si existe
            if ($documento->archivo && !$request->has('mantener_archivo')) {
                Storage::delete('public/' . $documento->archivo);
            }
            
            $path = $request->file('archivo')->store('public/documentos');
            $documento->archivo = str_replace('public/', '', $path);
            Log::info('Archivo actualizado en: ' . storage_path('app/public/' . $documento->archivo));
        } elseif (!$request->has('mantener_archivo') && $documento->archivo) {
            // Eliminar el archivo si no se marca para mantener
            Storage::delete('public/' . $documento->archivo);
            $documento->archivo = null;
        }

        $documento->save();

        return redirect()->back()->with('success', 'Documento actualizado correctamente.');
    }

    /**
     * Elimina el documento especificado de la base de datos.
     *
     * @param  \App\Models\Cotizacion  $cotizacion
     * @param  \App\Models\Documento  $documento
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cotizacion $cotizacion, Documento $documento)
    {
        if ($documento->archivo) {
            Storage::delete('public/' . $documento->archivo);
        }
        
        $documento->delete();
        
        return redirect()->back()->with('success', 'Documento eliminado correctamente.');
    }
}