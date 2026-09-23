<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArchivoEmpresaController extends Controller
{
    public function index()
    {
        $archivos = collect(Storage::disk('public')->files('archivos-empresa'))->map(function ($path) {
            return [
                'nombre' => basename($path),
                'path'   => $path,
                'url'    => Storage::disk('public')->url($path),
                'size'   => Storage::disk('public')->size($path),
                'fecha'  => Storage::disk('public')->lastModified($path),
            ];
        });

        return view('admin.archivos-empresa.index', compact('archivos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|max:10240',
        ]);

        $path = $request->file('archivo')->store('archivos-empresa', 'public');

        return redirect()->route('admin.archivos-empresa.index')
            ->with('success', 'Archivo subido correctamente.');
    }

    public function destroy(string $nombre)
    {
        $path = 'archivos-empresa/' . $nombre;

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        return redirect()->route('admin.archivos-empresa.index')
            ->with('success', 'Archivo eliminado correctamente.');
    }
}
