<?php
namespace App\Http\Controllers\Admin\Establecimientos;

use App\Http\Controllers\Controller;
use App\Models\Establecimiento;
use Illuminate\Http\Request;

class EstablecimientoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $establecimientos = Establecimiento::paginate(10);
        return view('admin.establecimientos.index', compact('establecimientos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.establecimientos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'direccion' => 'required|string',
            'telefono' => 'required|string|max:20',
        ]);

        Establecimiento::create($request->all());

        return redirect()->route('admin.establecimientos.index')
            ->with('success', 'Establecimiento creado exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Establecimiento  $establecimiento
     * @return \Illuminate\Http\Response
     */
    public function edit(Establecimiento $establecimiento)
    {
        return view('admin.establecimientos.edit', compact('establecimiento'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Establecimiento  $establecimiento
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Establecimiento $establecimiento)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'direccion' => 'required|string',
            'telefono' => 'required|string|max:20',
        ]);

        $establecimiento->update($request->all());

        return redirect()->route('admin.establecimientos.index')
            ->with('success', 'Establecimiento actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Establecimiento  $establecimiento
     * @return \Illuminate\Http\Response
     */
    public function destroy(Establecimiento $establecimiento)
    {
        try {
            $establecimiento->delete();
            return redirect()->route('admin.establecimientos.index')
                ->with('success', 'Establecimiento eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('admin.establecimientos.index')
                ->with('error', 'No se pudo eliminar el establecimiento. Error: ' . $e->getMessage());
        }
    }
}