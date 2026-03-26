<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * 📄 Listar usuarios con roles y empresa
     */
    public function index()
    {
        $usuarios = User::with(['roles', 'company'])->get();

        return response()->json([
            'success' => true,
            'data'    => $usuarios
        ], 200);
    }

    /**
     * ➕ Crear usuario con roles + empresa
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|string|min:6',
            'company_id'   => 'required|exists:companies,id', // 🔥 NUEVO
            'roles'        => 'array',
            'roles.*'      => 'string|exists:roles,name',
            'permissions'  => 'array',
            'permissions.*'=> 'string|exists:permissions,name',
        ]);

        // 🔐 Lógica multi-tenant (opcional pero PRO)
        $companyId = auth()->user()?->company_id;

        if ($companyId && !auth()->user()->hasRole('admin')) {
            // Usuario normal → fuerza su empresa
            $validated['company_id'] = $companyId;
        }

        $user = User::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'company_id' => $validated['company_id'],
        ]);

        // Roles
        if (!empty($validated['roles'])) {
            $user->syncRoles($validated['roles']);        }

        // Permisos
        if (!empty($validated['permissions'])) {
            $user->syncPermissions($validated['permissions']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado correctamente',
            'data'    => $user->load(['roles', 'permissions', 'company'])
        ], 201);
    }

    /**
     * 🔍 Mostrar usuario
     */
    public function show(int $id)
    {
        $user = User::with(['roles', 'permissions', 'company'])->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $user
        ], 200);
    }

    /**
     * ✏️ Actualizar usuario
     */
    public function update(Request $request, int $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $validated = $request->validate([
            'name'         => 'sometimes|required|string|max:255',
            'email'        => ['sometimes','required','email', Rule::unique('users')->ignore($user->id)],
            'password'     => 'nullable|string|min:6',
            'company_id'   => 'sometimes|exists:companies,id', // 🔥 NUEVO
            'roles'        => 'array',
            'roles.*'      => 'string|exists:roles,name',
            'permissions'  => 'array',
            'permissions.*'=> 'string|exists:permissions,name',
        ]);

        // Actualizar campos básicos
        if (isset($validated['name'])) {
            $user->name = $validated['name'];
        }

        if (isset($validated['email'])) {
            $user->email = $validated['email'];
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        // 🔥 Empresa
        if (isset($validated['company_id'])) {
            $user->company_id = $validated['company_id'];
        }

        $user->save();

        // Roles
        if (array_key_exists('roles', $validated)) {
            $user->syncRoles($validated['roles'] ?? []);
        }

        // Permisos
        if (array_key_exists('permissions', $validated)) {
            $user->syncPermissions($validated['permissions'] ?? []);
        }

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado correctamente',
            'data'    => $user->load(['roles','permissions','company'])
        ], 200);
    }

    /**
     * 🗑 Eliminar usuario
     */
    public function destroy(int $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado correctamente',
            'id'      => $id
        ], 200);
    }
}