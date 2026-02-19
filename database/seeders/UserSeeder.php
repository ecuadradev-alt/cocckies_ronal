<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('slug', 'demo-company')->first();

        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'company_id' => $company->id,
            ]
        );
        $admin->assignRole('admin');

        // Editor
        $editor = User::firstOrCreate(
            ['email' => 'editor@demo.com'],
            [
                'name' => 'Editor',
                'password' => Hash::make('password'),
                'company_id' => $company->id,
            ]
        );
        $editor->assignRole('editor');

        // Usuario
        $usuario = User::firstOrCreate(
            ['email' => 'usuario@demo.com'],
            [
                'name' => 'Usuario',
                'password' => Hash::make('password'),
                'company_id' => $company->id,
            ]
        );
        $usuario->assignRole('usuario');
    }
}
