<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArchivoEmpresaController extends Controller
{
    public function index()
    {
        if (!Storage::disk('public')->exists('archivos-empresa')) {
            Storage::disk('public')->makeDirectory('archivos-empresa');
        }

        $archivos = collect(Storage::disk('public')->files('archivos-empresa'))
            ->filter(function ($path) {
                return !str_starts_with(basename($path), '.');
            })
            ->map(function ($path) {
                $nombre = basename($path);
                $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

                return [
                    'nombre'    => $nombre,
                    'path'      => $path,
                    'size'      => Storage::disk('public')->size($path),
                    'fecha'     => Storage::disk('public')->lastModified($path),
                    'extension' => $extension,
                ];
            })
            ->sortByDesc('fecha')
            ->values();

        return view('admin.archivos-empresa.index', compact('archivos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'archivo'              => 'required|file|max:20480',
            'nombre_personalizado' => 'nullable|string|max:150',
        ], [
            'archivo.required' => 'Debe seleccionar un archivo para subir.',
            'archivo.max'      => 'El archivo no debe pesar más de 20 MB.',
        ]);

        $targetDir = storage_path('app/public/archivos-empresa');
        if (!file_exists($targetDir)) {
            @mkdir($targetDir, 0777, true);
            @chmod($targetDir, 0777);
        }

        $file = $request->file('archivo');
        $originalExt = $file->getClientOriginalExtension();

        // Determinar nombre base deseado
        if ($request->filled('nombre_personalizado')) {
            $baseName = pathinfo($request->input('nombre_personalizado'), PATHINFO_FILENAME);
        } else {
            $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        }

        // Sanitizar caracteres inválidos para el sistema de archivos
        $cleanName = preg_replace('/[\\\\\/:\*\?"<>\|\x00-\x1F]/', '_', $baseName);
        $cleanName = trim($cleanName);
        if (empty($cleanName)) {
            $cleanName = 'archivo_' . date('Ymd_His');
        }

        $extension = $originalExt ?: pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        $filename = $cleanName . ($extension ? ('.' . $extension) : '');

        // Evitar sobreescritura agregando numeración si ya existe
        $counter = 1;
        while (Storage::disk('public')->exists('archivos-empresa/' . $filename) || file_exists($targetDir . '/' . $filename)) {
            $filename = $cleanName . " ({$counter})" . ($extension ? ('.' . $extension) : '');
            $counter++;
        }

        $saved = false;
        try {
            $saved = $file->storeAs('archivos-empresa', $filename, 'public');
        } catch (\Throwable $e) {
            $saved = false;
        }

        // Fallback usando move directo si storeAs fue bloqueado por permisos de disco
        if (!$saved || !Storage::disk('public')->exists('archivos-empresa/' . $filename)) {
            try {
                $file->move($targetDir, $filename);
                $saved = file_exists($targetDir . '/' . $filename);
            } catch (\Throwable $e) {
                $saved = false;
            }
        }

        if (!$saved) {
            return redirect()->route('admin.archivos-empresa.index')
                ->with('error', 'No se pudo guardar el archivo. Ejecute en su servidor VPS: sudo chmod -R 777 storage bootstrap/cache');
        }

        return redirect()->route('admin.archivos-empresa.index')
            ->with('success', "Archivo '{$filename}' subido correctamente.");
    }

    public function show(string $nombre)
    {
        $nombre = basename($nombre);
        $path = 'archivos-empresa/' . $nombre;

        if (!Storage::disk('public')->exists($path)) {
            return redirect()->route('admin.archivos-empresa.index')
                ->with('error', 'El archivo no existe o fue eliminado.');
        }

        $mimeType = Storage::disk('public')->mimeType($path) ?: 'application/octet-stream';

        return Storage::disk('public')->response($path, $nombre, [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $nombre . '"',
        ]);
    }

    public function download(string $nombre)
    {
        $nombre = basename($nombre);
        $path = 'archivos-empresa/' . $nombre;

        if (!Storage::disk('public')->exists($path)) {
            return redirect()->route('admin.archivos-empresa.index')
                ->with('error', 'El archivo no existe o fue eliminado.');
        }

        return Storage::disk('public')->download($path, $nombre);
    }

    public function destroy(string $nombre)
    {
        $nombre = basename($nombre);
        $path = 'archivos-empresa/' . $nombre;

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
            return redirect()->route('admin.archivos-empresa.index')
                ->with('success', "Archivo '{$nombre}' eliminado correctamente.");
        }

        return redirect()->route('admin.archivos-empresa.index')
            ->with('error', 'El archivo no se pudo encontrar.');
    }
}
