<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Permisos
        $permisos = [
            'ver reportes',
            'editar usuarios',
            'crear posts',
            'borrar posts',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // Roles
        $admin   = Role::firstOrCreate(['name' => 'admin']);
        $editor  = Role::firstOrCreate(['name' => 'editor']);
        $usuario = Role::firstOrCreate(['name' => 'usuario']);

        // Asignar permisos a roles
        $admin->syncPermissions(Permission::all());
        $editor->syncPermissions(['crear posts', 'borrar posts']);
        $usuario->syncPermissions([]); // sin permisos directos
    }
}
