@extends('admin.layouts.app')

@section('title', 'Detalles del Cliente')

@section('header', 'Detalles del Cliente')

@section('content')
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <p><strong>Documento:</strong> {{ $cliente->documento_identidad }}</p>
        <p><strong>Tipo:</strong> {{ $cliente->tipo_cliente }}</p>
        <p><strong>Departamento:</strong> {{ $cliente->departamento }}</p>
        <p><strong>Provincia:</strong> {{ $cliente->provincia }}</p>
        <p><strong>Distrito:</strong> {{ $cliente->distrito }}</p>
        <p><strong>Correo:</strong> {{ $cliente->correo ?? 'N/A' }}</p>
        <p><strong>Categoría:</strong> {{ $cliente->categoria->nombre }}</p>
        <p><strong>Canal:</strong> {{ $cliente->canalCaptacion->nombre }}</p>
        <div>
            <strong>Teléfonos:</strong>
            <ul>
                @foreach ($cliente->telefonos->where('tipo', 'telefono') as $telefono)
                    <li>{{ $telefono->numero }}</li>
                @endforeach
            </ul>
        </div>
        <div>
            <strong>Celulares:</strong>
            <ul>
                @foreach ($cliente->telefonos->where('tipo', 'celular') as $celular)
                    <li>{{ $celular->numero }}</li>
                @endforeach
            </ul>
        </div>
        <a href="{{ route('admin.clientes.index') }}" class="mt-4 bg-blue-500 text-white p-2 rounded-md">Volver</a>
    </div>
@endsection