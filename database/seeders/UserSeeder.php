<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('admin');

        // Usuario editor
        $editor = User::firstOrCreate(
            ['email' => 'editor@demo.com'],
            [
                'name' => 'Editor',
                'password' => Hash::make('password'),
            ]
        );
        $editor->assignRole('editor');

        // Usuario regular
        $usuario = User::firstOrCreate(
            ['email' => 'usuario@demo.com'],
            [
                'name' => 'Usuario',
                'password' => Hash::make('password'),
            ]
        );
        $usuario->assignRole('usuario');
    }
}
