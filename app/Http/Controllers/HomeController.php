<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehiculo;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Obtener vehículos destacados (por ejemplo, los últimos 3)
        $vehiculosDestacados = Vehiculo::orderBy('created_at', 'desc')->take(3)->get();

        // Pasar los datos a la vista
        return view('home', [
            'vehiculosDestacados' => $vehiculosDestacados,
        ]);
    }
}