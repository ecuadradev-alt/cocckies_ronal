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
use App\Http\Controllers\API\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes (Sanctum)
|--------------------------------------------------------------------------
| Autenticación por TOKEN
| Header: Authorization: Bearer <token>
|--------------------------------------------------------------------------
*/

// 🔓 RUTAS PÚBLICAS
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail']);
Route::post('/password/reset', [AuthController::class, 'resetPassword']);

// 🔐 RUTAS PROTEGIDAS
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/profile', [AuthController::class, 'profile']);

    /*
    |--------------------------------------------------------------------------
    | Caja Registradora
    |--------------------------------------------------------------------------
    */
/*
|--------------------------------------------------------------------------
| Caja Registradora
|--------------------------------------------------------------------------
*/
Route::prefix('caja')->group(function () {

    // Abrir caja
    Route::post('/abrir', [CashRegisterController::class, 'abrir']);

    // Cerrar caja
    Route::post('/cerrar', [CashRegisterController::class, 'cerrar']);

    // Caja actual (hoy)
    Route::get('/actual', [CashRegisterController::class, 'actual']);

    // Listar cierres
    Route::get('/cierres', [CashRegisterController::class, 'cierres']);

    // 🔥 Resumen detallado por día
    Route::get('/resumen/{date}', [CashRegisterController::class, 'resumenDia']);
});


    /*
    |--------------------------------------------------------------------------
    | Transacciones
    |--------------------------------------------------------------------------
    */
    Route::prefix('transactions')->group(function () {
        Route::get('/', [TransactionController::class, 'index']);
        Route::get('/day', [TransactionController::class, 'day']);
        Route::post('/', [TransactionController::class, 'store']);
    });

    /*
    |--------------------------------------------------------------------------
    | Noticias
    |--------------------------------------------------------------------------
    */
    Route::prefix('noticias')->group(function () {
        Route::get('/', [NewsController::class, 'index']);
        Route::post('/', [NewsController::class, 'store']);
        Route::get('/{id}', [NewsController::class, 'show']);
        Route::put('/{id}', [NewsController::class, 'update']);
        Route::delete('/{id}', [NewsController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Usuarios
    |--------------------------------------------------------------------------
    */
    Route::prefix('usuarios')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);

        // Roles
        Route::get('/{user}/roles', [UserRoleController::class, 'roles']);
        Route::post('/{user}/roles/asignar', [UserRoleController::class, 'assignRole']);
        Route::post('/{user}/roles/revocar', [UserRoleController::class, 'revokeRole']);

        // Permisos
        Route::get('/{user}/permisos', [UserPermissionController::class, 'permissions']);
        Route::post('/{user}/permisos/asignar', [UserPermissionController::class, 'givePermission']);
        Route::post('/{user}/permisos/revocar', [UserPermissionController::class, 'revokePermission']);
    });

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::post('/', [RoleController::class, 'store']);

        Route::get('/{role}/permissions', [RolePermissionController::class, 'permissions']);
        Route::put('/{role}/permissions', [RolePermissionController::class, 'update']);

        Route::post('/{role}/permissions/assign', [RolePermissionController::class, 'assignPermission']);
        Route::post('/{role}/permissions/revoke', [RolePermissionController::class, 'revokePermission']);
    });

    /*
    |--------------------------------------------------------------------------
    | Permisos
    |--------------------------------------------------------------------------
    */
    Route::prefix('permisos')->group(function () {
        Route::get('/', [PermissionController::class, 'index']);
        Route::post('/', [PermissionController::class, 'store']);
        Route::get('/{id}', [PermissionController::class, 'show']);
        Route::put('/{id}', [PermissionController::class, 'update']);
        Route::delete('/{id}', [PermissionController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:admin');

    Route::get('/reportes', [DashboardController::class, 'reportes'])
        ->middleware('permission:ver reportes');
});

// ❌ Fallback
Route::fallback(function () {
    return response()->json(['message' => 'Ruta no encontrada'], 404);
});
