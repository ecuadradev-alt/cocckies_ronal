<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Controladores principales
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
| API Routes
|--------------------------------------------------------------------------
| Este archivo contiene todas las rutas de la API del sistema.
| Se agrupan las rutas públicas, autenticadas y protegidas por roles/permisos.
|--------------------------------------------------------------------------
*/

// 🔓 RUTAS PÚBLICAS (sin autenticación)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// 👤 PERFIL del usuario autenticado
Route::middleware('auth:sanctum')->get('/profile', function (Request $req) {
    return response()->json([
        'user' => $req->user(),
    ]);
});

// 🔒 RUTAS PROTEGIDAS (autenticadas con Sanctum)
Route::middleware(['auth:sanctum'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | 🧾 Caja Registradora
    |--------------------------------------------------------------------------
    */
    Route::prefix('caja')->group(function () {
        Route::post('/abrir',  [CashRegisterController::class, 'abrir']);
        Route::post('/cerrar', [CashRegisterController::class, 'cerrar']);
        Route::get('/actual',  [CashRegisterController::class, 'actual']);
    });

    /*
    |--------------------------------------------------------------------------
    | 💰 Transacciones
    |--------------------------------------------------------------------------
    */
    Route::prefix('transactions')->group(function () {
        Route::get('/', [TransactionController::class, 'index']);
        Route::get('/day', [TransactionController::class, 'day']);
        Route::post('/', [TransactionController::class, 'store']);
    });

    /*
    |--------------------------------------------------------------------------
    | 📰 Noticias
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
    | 👤 Usuarios
    |--------------------------------------------------------------------------
    */
    Route::prefix('usuarios')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | 🎭 Roles
    |--------------------------------------------------------------------------
    */
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::post('/', [RoleController::class, 'store']);

        // Permisos del rol
        Route::get('/{role}/permisos', [RolePermissionController::class, 'permissions']);
        Route::put('/{role}/permisos', [RolePermissionController::class, 'update']);
        Route::post('/{role}/permisos/asignar', [RolePermissionController::class, 'assignPermission']);
        Route::post('/{role}/permisos/revocar', [RolePermissionController::class, 'revokePermission']);
    });

    /*
    |--------------------------------------------------------------------------
    | 🔑 Permisos
    |--------------------------------------------------------------------------
    */
    Route::prefix('permisos')->group(function () {
        Route::get('/', [PermissionController::class, 'index']);
        Route::post('/', [PermissionController::class, 'store']);
        Route::get('/{permission}', [PermissionController::class, 'show']);
        Route::put('/{permission}', [PermissionController::class, 'update']);
        Route::delete('/{permission}', [PermissionController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | ⚙️ Asignación de Roles y Permisos a Usuarios
    |--------------------------------------------------------------------------
    */
    Route::prefix('usuarios')->group(function () {
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
    | 👑 Dashboard Admin (solo rol admin)
    |--------------------------------------------------------------------------
    */
    Route::get('/admin/dashboard', [DashboardController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | 📊 Reportes (solo con permiso)
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:ver reportes')->get('/reportes', [DashboardController::class, 'reportes']);
});


/*
|--------------------------------------------------------------------------
| 👑 Dashboard Admin (solo rol admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin/dashboard')->middleware('role:admin')->group(function () {
    Route::get('/stats', [DashboardController::class, 'stats']);       // KPIs
    Route::get('/charts', [DashboardController::class, 'charts']);     // Gráficas
    Route::get('/users', [DashboardController::class, 'listUsers']);   // Usuarios
    Route::post('/users', [DashboardController::class, 'storeUser']);  // Crear usuario
    Route::get('/users/{id}', [DashboardController::class, 'showUser']);
    Route::put('/users/{id}', [DashboardController::class, 'updateUser']);
    Route::delete('/users/{id}', [DashboardController::class, 'deleteUser']);
});

// 🔁 Fallback para rutas no encontradas
Route::fallback(function () {
    return response()->json(['message' => 'Ruta no encontrada.'], 404);
});
