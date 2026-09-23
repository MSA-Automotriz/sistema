<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    public function clientes(Request $request)
    {
        $term = $request->input('term', '');
        
        Log::info('Búsqueda de clientes con término: ' . $term);
        
        if (empty($term)) {
            return response()->json([]);
        }
        
        $clientes = Cliente::where('documento_identidad', 'LIKE', "%{$term}%")
            ->orWhere(function($query) use ($term) {
                $query->where('tipo_cliente', 'natural')
                    ->where(function($q) use ($term) {
                        $q->where('nombres', 'LIKE', "%{$term}%")
                          ->orWhere('apellido_paterno', 'LIKE', "%{$term}%")
                          ->orWhere('apellido_materno', 'LIKE', "%{$term}%");
                    });
            })
            ->orWhere(function($query) use ($term) {
                $query->where('tipo_cliente', 'juridica')
                    ->where('razon_social', 'LIKE', "%{$term}%");
            })
            ->limit(10)
            ->get();
        
        Log::info('Resultados encontrados: ' . $clientes->count());
        
        $results = $clientes->map(function ($cliente) {
            return [
                'id' => $cliente->id,
                'label' => $cliente->tipo_cliente === 'natural'
                    ? "{$cliente->documento_identidad} - {$cliente->nombres} {$cliente->apellido_paterno} {$cliente->apellido_materno}"
                    : "{$cliente->documento_identidad} - {$cliente->razon_social}",
                'tipo_cliente' => $cliente->tipo_cliente,
                'nombres' => $cliente->nombres,
                'apellido_paterno' => $cliente->apellido_paterno,
                'apellido_materno' => $cliente->apellido_materno,
                'razon_social' => $cliente->razon_social,
                'documento_identidad' => $cliente->documento_identidad,
            ];
        })->toArray();
        
        return response()->json($results);
    }
}