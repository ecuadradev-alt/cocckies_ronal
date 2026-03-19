<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\API\AuthController;
/* web.php
|--------------------------------------------------------------------------
| CSRF y Estado de Sesión (Laravel Sanctum)
|--------------------------------------------------------------------------
| Este endpoint es requerido por Sanctum para emitir cookies de sesión y CSRF.
| No lo pongas en api.php ni le pongas prefijo /api.
*/
Route::get('/sanctum/csrf-cookie', fn () => response()->noContent());

Route::post('/login', [AuthController::class, 'login'])->name('login'); 
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Rutas base / salud
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return response()->json([
        'message' => '✅ API Laravel funcionando correctamente',
        'frontend' => 'Vue.js corre separado (Vite o similar)',
    ]);
});

Route::get('/health', function () {
    return response()->json([
        'status' => 'OK',
        'timestamp' => now(),
    ]);
});
