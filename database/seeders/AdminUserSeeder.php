<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear el rol de administrador si no existe
        $adminRole = Role::firstOrCreate([
            'name' => 'admin'
        ], [
            'description' => 'Administrador del sistema'
        ]);

        // Crear usuario administrador
        $admin = User::firstOrCreate([
            'email' => 'admin@sistema.com'
        ], [
            'name' => 'Administrador',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);

        // Asignar rol de administrador
        if (!$admin->hasRole('admin')) {
            $admin->assignRole($adminRole);
        }

        $this->command->info('Usuario administrador creado exitosamente:');
        $this->command->info('Email: admin@sistema.com');
        $this->command->info('Password: admin123');
    }
}