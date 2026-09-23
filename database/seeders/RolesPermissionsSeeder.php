<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definir permisos por módulo
        $permissions = [
            // Sistema
            ['name' => 'dashboard', 'display_name' => 'Dashboard', 'module' => 'sistema'],
            ['name' => 'reportes', 'display_name' => 'Reportes', 'module' => 'sistema'],
            ['name' => 'usuarios', 'display_name' => 'Usuarios y Roles', 'module' => 'sistema'],
            ['name' => 'configuracion', 'display_name' => 'Configuración', 'module' => 'sistema'],

            // Ventas
            ['name' => 'ventas', 'display_name' => 'Ventas y Cotizaciones', 'module' => 'ventas'],
            ['name' => 'clientes', 'display_name' => 'Clientes', 'module' => 'ventas'],

            // Compras
            ['name' => 'compras', 'display_name' => 'Compras y Proveedores', 'module' => 'compras'],

            // Almacén e Inventario
            ['name' => 'almacenes', 'display_name' => 'Almacenes y Partes', 'module' => 'almacen'],
            ['name' => 'inventario', 'display_name' => 'Inventario y Traslados', 'module' => 'almacen'],

            // Mantenimiento
            ['name' => 'mantenimiento', 'display_name' => 'Mantenimiento', 'module' => 'mantenimiento'],
        ];

        // Crear permisos
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                ['display_name' => $permission['display_name'], 'module' => $permission['module']]
            );
        }

        // Crear roles y asignar permisos
        $roles = [
            [
                'name' => 'admin',
                'description' => 'Administrador del sistema - Acceso total',
                'permissions' => ['dashboard', 'ventas', 'clientes', 'compras', 'almacenes', 'inventario', 'mantenimiento', 'reportes', 'usuarios', 'configuracion']
            ],
            [
                'name' => 'vendedor',
                'description' => 'Vendedor - Acceso a ventas y clientes',
                'permissions' => ['dashboard', 'ventas', 'clientes']
            ],
            [
                'name' => 'almacenero',
                'description' => 'Almacenero - Acceso a almacenes e inventario',
                'permissions' => ['dashboard', 'almacenes', 'inventario']
            ],
            [
                'name' => 'tecnico',
                'description' => 'Técnico - Acceso a mantenimiento',
                'permissions' => ['dashboard', 'mantenimiento']
            ],
            [
                'name' => 'compras',
                'description' => 'Compras - Acceso a compras y almacenes',
                'permissions' => ['dashboard', 'compras', 'almacenes']
            ],
            [
                'name' => 'cliente',
                'description' => 'Cliente - Sin acceso al admin',
                'permissions' => []
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleData['name']],
                ['description' => $roleData['description']]
            );

            // Sincronizar permisos
            if (!empty($roleData['permissions'])) {
                $permissionIds = Permission::whereIn('name', $roleData['permissions'])->pluck('id')->toArray();
                $role->permissions()->sync($permissionIds);
            } else {
                $role->permissions()->sync([]);
            }
        }
    }
}
