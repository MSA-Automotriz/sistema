<?php

namespace App\Http\Controllers\Admin\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Cotizacion;
use App\Models\PlacaInfo;
use App\Models\DocumentoPlaca;
use App\Models\PlacaComentario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlacaController extends Controller
{
    public function index(Cotizacion $cotizacion)
    {
        $placas = PlacaInfo::where('cotizacion_id', $cotizacion->id)
            ->with(['documentos', 'comentarios.usuario'])
            ->get();
        
        \Log::info("Placas encontradas para cotización {$cotizacion->id}: " . count($placas), [
            'placas' => $placas
        ]);
        
        return view('admin.ventas.cotizaciones.proceso.placa.index', [
            'cotizacion' => $cotizacion,
            'placas' => $placas,
            'estados' => PlacaInfo::ESTADOS
        ]);
    }

    public function storePlaca(Request $request, Cotizacion $cotizacion)
    {
        $request->validate([
            'numero_placa' => 'nullable|string|max:20',
            'tipo_placa' => 'required|in:rotativa,definitiva',
            'fecha_emision' => 'nullable|date',
            'paso_actual' => 'required|integer|between:1,5',
            'observaciones' => 'nullable|string'
        ]);

        $placa = new PlacaInfo();
        $placa->cotizacion_id = $cotizacion->id;
        $placa->numero_placa = $request->numero_placa;
        $placa->tipo_placa = $request->tipo_placa;
        $placa->fecha_emision = $request->fecha_emision;
        $placa->estado_placa = PlacaInfo::ESTADOS[$request->paso_actual];
        $placa->paso_actual = $request->paso_actual;
        $placa->observaciones = $request->observaciones;
        $placa->user_id = auth()->id();
        $placa->save();

        return redirect()->back()->with('success', 'Placa registrada correctamente.');
    }

    public function updatePlaca(Request $request, Cotizacion $cotizacion, PlacaInfo $placa)
    {
        $request->validate([
            'numero_placa' => 'nullable|string|max:20',
            'tipo_placa' => 'required|in:rotativa,definitiva',
            'fecha_emision' => 'nullable|date',
            'paso_actual' => 'required|integer|between:1,5',
            'observaciones' => 'nullable|string'
        ]);

        $placa->numero_placa = $request->numero_placa;
        $placa->tipo_placa = $request->tipo_placa;
        $placa->fecha_emision = $request->fecha_emision;
        $placa->estado_placa = PlacaInfo::ESTADOS[$request->paso_actual];
        $placa->paso_actual = $request->paso_actual;
        $placa->observaciones = $request->observaciones;
        $placa->save();

        return redirect()->back()->with('success', 'Placa actualizada correctamente.');
    }

    public function destroyPlaca(Cotizacion $cotizacion, PlacaInfo $placa)
    {
        foreach ($placa->documentos as $documento) {
            if ($documento->archivo) {
                Storage::delete('public/' . $documento->archivo);
            }
            $documento->delete();
        }
        
        $placa->delete();
        
        return redirect()->back()->with('success', 'Placa eliminada correctamente.');
    }

    public function storeDocumento(Request $request, Cotizacion $cotizacion, PlacaInfo $placa)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|in:rotativa,definitiva,guia_remision,otros',
            'fecha' => 'required|date',
            'archivo' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $documento = new DocumentoPlaca();
        $documento->placa_id = $placa->id;
        $documento->cotizacion_id = $cotizacion->id;
        $documento->nombre = $request->nombre;
        $documento->tipo = $request->tipo;
        $documento->fecha = $request->fecha;
        $documento->observaciones = $request->observaciones;
        $documento->user_id = auth()->id();

        if ($request->hasFile('archivo')) {
            $path = $request->file('archivo')->store('public/documentos_placa');
            $documento->archivo = str_replace('public/', '', $path);
        }

        $documento->save();

        return redirect()->back()->with('success', 'Documento de placa registrado correctamente.');
    }

    public function updateDocumento(Request $request, Cotizacion $cotizacion, PlacaInfo $placa, DocumentoPlaca $documento)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|in:rotativa,definitiva,guia_remision,otros',
            'fecha' => 'required|date',
            'archivo' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $documento->nombre = $request->nombre;
        $documento->tipo = $request->tipo;
        $documento->fecha = $request->fecha;
        $documento->observaciones = $request->observaciones;

        if ($request->hasFile('archivo')) {
            if ($documento->archivo && !$request->has('mantener_archivo')) {
                Storage::delete('public/' . $documento->archivo);
            }
            
            $path = $request->file('archivo')->store('public/documentos_placa');
            $documento->archivo = str_replace('public/', '', $path);
        } elseif (!$request->has('mantener_archivo') && $documento->archivo) {
            Storage::delete('public/' . $documento->archivo);
            $documento->archivo = null;
        }

        $documento->save();

        return redirect()->back()->with('success', 'Documento de placa actualizado correctamente.');
    }

    public function destroyDocumento(Cotizacion $cotizacion, PlacaInfo $placa, DocumentoPlaca $documento)
    {
        if ($documento->archivo) {
            Storage::delete('public/' . $documento->archivo);
        }
        
        $documento->delete();
        
        return redirect()->back()->with('success', 'Documento de placa eliminado correctamente.');
    }

    public function storeComentario(Request $request, Cotizacion $cotizacion, PlacaInfo $placa)
    {
        $request->validate([
            'comentario' => 'required|string|max:1000',
        ]);

        $comentario = new PlacaComentario();
        $comentario->placa_id = $placa->id;
        $comentario->cotizacion_id = $cotizacion->id;
        $comentario->comentario = $request->comentario;
        $comentario->user_id = auth()->id();
        $comentario->save();

        return redirect()->back()->with('success', 'Comentario registrado correctamente.');
    }

    public function destroyComentario(Cotizacion $cotizacion, PlacaInfo $placa, PlacaComentario $comentario)
    {
        $comentario->delete();
        return redirect()->back()->with('success', 'Comentario eliminado correctamente.');
    }
}