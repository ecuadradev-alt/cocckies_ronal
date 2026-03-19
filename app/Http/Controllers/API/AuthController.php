<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Registro (User + Company)
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|string|email|unique:users,email',
            'password'     => 'required|string|min:6|confirmed',
            'company_name' => 'required|string|max:255',
        ]);

        $company = Company::create([
            'name' => $validated['company_name'],
            'slug' => Str::slug($validated['company_name']) . '-' . uniqid(),
            'plan' => 'free',
        ]);

        $user = User::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'company_id' => $company->id,
        ]);

        $user->assignRole('owner');

        // 🔥 LOGIN AUTOMÁTICO (SANCTUM)
        Auth::login($user);

        return response()->json([
            'message' => 'Usuario creado correctamente',
            'user'    => $user,
            'roles'   => $user->getRoleNames(),
            'permisos'=> $user->getAllPermissions()->pluck('name'),
        ], 201);
    }

    /**
     * Login (FIXED)
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // 🔥 CAMBIO CLAVE AQUÍ
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        // Evita session fixation
        $request->session()->regenerate();

        $user = $request->user();

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
            'roles'    => $user->getRoleNames(),
            'permisos' => $user->getAllPermissions()->pluck('name'),
        ]);
    }

    /**
     * Perfil autenticado
     */
    public function profile(Request $request)
    {
        $user = $request->user()->load('company');

        return response()->json([
            'user' => $user,
            'roles' => $user->getRoleNames(),
            'permisos' => $user->getAllPermissions()->pluck('name'),
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Sesión cerrada correctamente',
        ]);
    }

    /**
     * Enviar link de reseteo
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        return response()->json([
            'message' => __($status),
        ], $status === Password::RESET_LINK_SENT ? 200 : 400);
    }

    /**
     * Resetear contraseña
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'token'    => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return response()->json([
            'message' => __($status),
        ], $status === Password::PASSWORD_RESET ? 200 : 400);
    }
}