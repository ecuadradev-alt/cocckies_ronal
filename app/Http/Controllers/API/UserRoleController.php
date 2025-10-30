<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserPermissionController extends Controller
{
    /**
     * Asignar un permiso a un usuario
     */
    public function givePermission(Request $request, User $user)
    {
        $validated = $request->validate([
            'permission' => 'required|exists:permissions,name',
        ]);

        $user->givePermissionTo($validated['permission']);

        return response()->json([
            'success'     => true,
            'message'     => 'Permiso asignado correctamente',
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ], 200);
        
    }

    /**
     * Revocar un permiso a un usuario
     */
    public function revokePermission(Request $request, User $user)
    {
        $validated = $request->validate([
            'permission' => 'required|exists:permissions,name',
        ]);

        $user->revokePermissionTo($validated['permission']);

        return response()->json([
            'success'     => true,
            'message'     => 'Permiso removido correctamente',
            'permissions' => $user->getPermissionNames(),
        ], 200);
    }

    /**
     * Listar permisos de un usuario
     */
    public function permissions(User $user)
    {
        return response()->json([
            'success'     => true,
            'permissions' => $user->getPermissionNames(),
        ], 200);
    }
}
