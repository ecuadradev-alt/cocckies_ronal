<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\CashRegisterController;
use App\Http\Controllers\API\NewsController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\UserRoleController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\RolePermissionController;
use App\Http\Controllers\API\UserPermissionController;

// 🔐 Autenticación pública
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// ✅ Perfil del usuario autenticado
Route::middleware('auth:sanctum')->get('/profile', function (Request $req) {
    return response()->json([
        'user' => $req->user(),
    ]);
});

// 🔒 Rutas protegidas (requieren sesión activa con Sanctum)
Route::middleware(['auth:sanctum'])->group(function () {

    // 🧾 Caja registradora
    Route::prefix('caja')->group(function () {
        Route::post('/abrir',  [CashRegisterController::class, 'abrir']);
        Route::post('/cerrar', [CashRegisterController::class, 'cerrar']);
        Route::get('/actual',  [CashRegisterController::class, 'actual']);
    });

    // 💰 Transacciones
    Route::prefix('transactions')->group(function () {
        Route::get('/', [TransactionController::class, 'index']);
        Route::get('/day', [TransactionController::class, 'day']);
        Route::post('/', [TransactionController::class, 'store']);
    });

    // 📰 Noticias / Productos
    Route::prefix('noticias')->group(function () {
        Route::get('/', [NewsController::class, 'index']);
        Route::post('/', [NewsController::class, 'store']);
        Route::get('/{id}', [NewsController::class, 'show']);
        Route::put('/{id}', [NewsController::class, 'update']);
        Route::delete('/{id}', [NewsController::class, 'destroy']);
    });

    // 👤 Usuarios
    Route::prefix('usuarios')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

    // 🎭 Roles
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::post('/', [RoleController::class, 'store']);

        // ✅ Permisos del rol
        Route::get('/{role}/permisos', [RolePermissionController::class, 'permissions']);
        Route::put('/{role}/permisos', [RolePermissionController::class, 'update']);

        // (Opcional) Métodos antiguos de asignar/revocar individuales
        Route::post('/{role}/permisos/asignar', [RolePermissionController::class, 'assignPermission'])
            ->name('roles.permissions.assign');
        Route::post('/{role}/permisos/revocar', [RolePermissionController::class, 'revokePermission'])
            ->name('roles.permissions.revoke');
    });

    // 🔑 Permisos
    Route::prefix('permisos')->group(function () {
        Route::get('/', [PermissionController::class, 'index']);
        Route::post('/', [PermissionController::class, 'store']);
        Route::get('/{permission}', [PermissionController::class, 'show']);
        Route::put('/{permission}', [PermissionController::class, 'update']);
        Route::delete('/{permission}', [PermissionController::class, 'destroy']);
    });

    // ⚙️ Asignaciones de roles y permisos a usuarios
    Route::prefix('usuarios')->group(function () {
        // Roles del usuario
        Route::post('/{user}/roles/asignar', [UserRoleController::class, 'assignRole']);
        Route::post('/{user}/roles/revocar', [UserRoleController::class, 'revokeRole']);
        Route::get('/{user}/roles', [UserRoleController::class, 'roles']);

        // Permisos directos del usuario
        Route::post('/{user}/permisos/asignar', [UserPermissionController::class, 'givePermission']);
        Route::post('/{user}/permisos/revocar', [UserPermissionController::class, 'revokePermission']);
        Route::get('/{user}/permisos', [UserPermissionController::class, 'permissions']);
    });

    // 👑 Admin Dashboard (solo rol admin)
    Route::middleware('role:admin')->get('/admin/dashboard', function () {
        return response()->json(['message' => 'Bienvenido Admin']);
    });

    // 📊 Acceso a reportes (solo con permiso)
    Route::middleware('permission:ver reportes')->get('/reportes', function () {
        return response()->json(['message' => 'Vista de reportes']);
    });
});
