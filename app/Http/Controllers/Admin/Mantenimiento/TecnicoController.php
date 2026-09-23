<?php

namespace App\Http\Controllers\Admin\Mantenimiento;

use App\Http\Controllers\Controller;
use App\Models\Tecnico;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TecnicoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tecnicos = Tecnico::with('user')->latest()->paginate(10);
        return view('admin.mantenimiento.tecnicos.index', compact('tecnicos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.mantenimiento.tecnicos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'codigo' => 'required|string|max:50|unique:tecnicos',
            'especialidad' => 'nullable|string|max:255',
            'cedula_profesional' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'telefono_emergencia' => 'nullable|string|max:20',
            'certificaciones' => 'nullable|string',
            'habilidades' => 'nullable|string',
            'fecha_ingreso' => 'nullable|date',
            'estado' => 'required|in:activo,inactivo,vacaciones,licencia',
            'notas' => 'nullable|string',
        ]);

        // Crear el usuario
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Crear el técnico
        $tecnico = Tecnico::create([
            'user_id' => $user->id,
            'codigo' => $validated['codigo'],
            'especialidad' => $validated['especialidad'] ?? null,
            'cedula_profesional' => $validated['cedula_profesional'] ?? null,
            'telefono' => $validated['telefono'] ?? null,
            'telefono_emergencia' => $validated['telefono_emergencia'] ?? null,
            'certificaciones' => $validated['certificaciones'] ?? null,
            'habilidades' => $validated['habilidades'] ?? null,
            'fecha_ingreso' => $validated['fecha_ingreso'] ?? now(),
            'estado' => $validated['estado'],
            'notas' => $validated['notas'] ?? null,
        ]);

        return redirect()->route('admin.mantenimiento.tecnicos.index')
            ->with('success', 'Técnico creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tecnico $tecnico)
    {
        $tecnico->load(['user', 'ordenesTrabajoMantenimiento' => function($query) {
            $query->latest()->limit(10);
        }]);

        return view('admin.mantenimiento.tecnicos.show', compact('tecnico'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tecnico $tecnico)
    {
        $tecnico->load('user');
        return view('admin.mantenimiento.tecnicos.edit', compact('tecnico'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tecnico $tecnico)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($tecnico->user_id)],
            'password' => 'nullable|string|min:8|confirmed',
            'codigo' => ['required', 'string', 'max:50', Rule::unique('tecnicos')->ignore($tecnico->id)],
            'especialidad' => 'nullable|string|max:255',
            'cedula_profesional' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'telefono_emergencia' => 'nullable|string|max:20',
            'certificaciones' => 'nullable|string',
            'habilidades' => 'nullable|string',
            'fecha_ingreso' => 'nullable|date',
            'estado' => 'required|in:activo,inactivo,vacaciones,licencia',
            'notas' => 'nullable|string',
        ]);

        // Actualizar el usuario
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $tecnico->user->update($userData);

        // Actualizar el técnico
        $tecnico->update([
            'codigo' => $validated['codigo'],
            'especialidad' => $validated['especialidad'] ?? null,
            'cedula_profesional' => $validated['cedula_profesional'] ?? null,
            'telefono' => $validated['telefono'] ?? null,
            'telefono_emergencia' => $validated['telefono_emergencia'] ?? null,
            'certificaciones' => $validated['certificaciones'] ?? null,
            'habilidades' => $validated['habilidades'] ?? null,
            'fecha_ingreso' => $validated['fecha_ingreso'] ?? $tecnico->fecha_ingreso,
            'estado' => $validated['estado'],
            'notas' => $validated['notas'] ?? null,
        ]);

        return redirect()->route('admin.mantenimiento.tecnicos.index')
            ->with('success', 'Técnico actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tecnico $tecnico)
    {
        $tecnico->user->delete(); // Esto también eliminará el técnico por CASCADE

        return redirect()->route('admin.mantenimiento.tecnicos.index')
            ->with('success', 'Técnico eliminado exitosamente.');
    }
}
