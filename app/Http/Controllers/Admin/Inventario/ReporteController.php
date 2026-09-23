<?php

namespace App\Http\Controllers\Admin\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function index()
    {
        return view('admin..inventario.index');
    }

    public function kardex()
    {
        // Lógica existente para reporte kardex
        return view('admin.inventario.kardex');
    }

    public function inventario()
    {
        // Lógica existente para reporte de inventario
        return view('admin.inventario.inventario');
    }
}