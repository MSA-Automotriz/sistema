<?php
namespace App\Http\Controllers\Admin\Usuarios;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::with('roles')->paginate(10);
        return view('admin.usuarios.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.usuarios.usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        }

        return redirect()->route('admin.usuarios.usuarios.index')
                        ->with('success', 'Usuario creado con éxito');
    }

    public function show(User $usuario)
    {
        $usuario->load('roles');
        return view('admin.usuarios.usuarios.show', compact('usuario'));
    }

    public function edit(User $usuario)
    {
        $roles = Role::all();
        $usuario->load('roles');
        return view('admin.usuarios.usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $usuario->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

        if ($request->has('roles')) {
            $usuario->roles()->sync($request->roles);
        } else {
            $usuario->roles()->detach();
        }

        return redirect()->route('admin.usuarios.usuarios.index')
                        ->with('success', 'Usuario actualizado con éxito');
    }

    public function destroy(User $usuario)
    {
        // No permitir eliminar al usuario logeado
        if ($usuario->id === auth()->id()) {
            return redirect()->route('admin.usuarios.usuarios.index')
                            ->with('error', 'No puedes eliminar tu propio usuario');
        }

        $usuario->roles()->detach();
        $usuario->delete();

        return redirect()->route('admin.usuarios.usuarios.index')
                        ->with('success', 'Usuario eliminado con éxito');
    }
}