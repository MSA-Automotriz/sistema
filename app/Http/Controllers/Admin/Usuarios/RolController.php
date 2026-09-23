<?php
namespace App\Http\Controllers\Admin\Usuarios;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RolController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->paginate(10);
        return view('admin.usuarios.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::groupedByModule();
        return view('admin.usuarios.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create($request->only(['name', 'description']));

        // Sincronizar permisos
        if ($request->has('permissions') && !empty($request->permissions)) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('admin.usuarios.roles.index')
                        ->with('success', 'Rol creado con éxito');
    }

    public function show(Role $rol)
    {
        $rol->load('users', 'permissions');
        return view('admin.usuarios.roles.show', compact('rol'));
    }

    public function edit(Role $rol)
    {
        $permissions = Permission::groupedByModule();
        $rol->load('permissions');
        return view('admin.usuarios.roles.edit', compact('rol', 'permissions'));
    }

    public function update(Request $request, Role $rol)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $rol->id,
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $rol->update($request->only(['name', 'description']));

        // Sincronizar permisos
        if ($request->has('permissions') && !empty($request->permissions)) {
            $rol->permissions()->sync($request->permissions);
        } else {
            $rol->permissions()->sync([]);
        }

        return redirect()->route('admin.usuarios.roles.index')
                        ->with('success', 'Rol actualizado con éxito');
    }

    public function destroy(Role $rol)
    {
        // Verificar que no sea un rol importante
        if (in_array($rol->name, ['admin', 'cliente'])) {
            return redirect()->route('admin.usuarios.roles.index')
                            ->with('error', 'No se puede eliminar este rol del sistema');
        }

        $rol->delete();

        return redirect()->route('admin.usuarios.roles.index')
                        ->with('success', 'Rol eliminado con éxito');
    }
}