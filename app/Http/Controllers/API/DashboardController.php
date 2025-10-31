<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaction;
use App\Models\CashRegister;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * 📊 KPIs principales
     * Endpoint: GET /api/admin/dashboard/stats
     */
    public function stats()
    {
        $today = Carbon::today();

        $usersCount = User::count();
        $consultasHoy = Transaction::whereDate('created_at', $today)->count();
        $volumenVendido = Transaction::where('type', 'venta')->sum('grams');

        $lastPrice = Transaction::whereNotNull('price_per_gram_usd')
            ->latest('created_at')
            ->value('price_per_gram_usd');

        $avgWeek = Transaction::whereNotNull('price_per_gram_usd')
            ->whereDate('created_at', '>=', Carbon::today()->subDays(6))
            ->avg('price_per_gram_usd');

        return response()->json([
            'success' => true,
            'data' => [
                [
                    'label' => 'Usuarios registrados',
                    'value' => $usersCount,
                    'icon' => 'users',
                ],
                [
                    'label' => 'Transacciones hoy',
                    'value' => $consultasHoy,
                    'icon' => 'activity',
                ],
                [
                    'label' => 'Volumen vendido (g)',
                    'value' => round($volumenVendido, 2),
                    'icon' => 'package',
                ],
                [
                    'label' => 'Precio oro (USD/g)',
                    'value' => $lastPrice ? number_format($lastPrice, 2) : '—',
                    'icon' => 'dollar-sign',
                ],
                [
                    'label' => 'Promedio semanal (USD/g)',
                    'value' => $avgWeek ? number_format($avgWeek, 2) : '—',
                    'icon' => 'bar-chart',
                ],
            ],
        ]);
    }

    /**
     * 📈 Gráficos: precios del oro + usuarios activos/inactivos
     * Endpoint: GET /api/admin/dashboard/charts
     */
    public function charts()
    {
        // === Oro últimos 7 días ===
        $labels = [];
        $goldData = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $labels[] = $day->format('d M');
            $avg = Transaction::whereDate('created_at', $day)
                ->avg('price_per_gram_usd');
            $goldData[] = $avg ? round($avg, 2) : 0;
        }

        // === Usuarios activos / inactivos ===
        $active = User::whereNotNull('last_login_at')
            ->where('last_login_at', '>=', Carbon::now()->subDays(30))
            ->count();

        $total = User::count();
        $inactive = max(0, $total - $active);

        return response()->json([
            'success' => true,
            'data' => [
                'goldPrices' => [
                    'labels' => $labels,
                    'data' => $goldData,
                ],
                'userDistribution' => [
                    'labels' => ['Activos', 'Inactivos'],
                    'data' => [$active, $inactive],
                ],
            ],
        ]);
    }

    /**
     * 👥 Listado de usuarios
     * Endpoint: GET /api/admin/dashboard/users
     */
    public function listUsers(Request $request)
    {
        $perPage = intval($request->query('per_page', 10));
        $search = $request->query('q');

        $query = User::query()->select('id', 'name', 'email', 'created_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        $users = $query->orderBy('id', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'total' => $users->total(),
            'data' => $users->items(),
        ]);
    }

    /**
     * 🧾 Crear usuario (rápido)
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|string|min:6',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password'] ?? 'secret123'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado correctamente',
            'data' => $user,
        ]);
    }

    /**
     * 🔄 Actualizar usuario
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
        ]);

        $user->fill($validated);
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }
        $user->save();

        return response()->json(['success' => true, 'data' => $user]);
    }

    /**
     * 🗑️ Eliminar usuario
     */
    public function deleteUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
        }

        $user->delete();

        return response()->json(['success' => true, 'message' => 'Usuario eliminado']);
    }
}
