<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CashRegister;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;
use Carbon\Carbon;

class CashRegisterController extends Controller
{
    /**
     * 🔹 Abrir la caja del día actual.
     */
    public function abrir(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'opening_cash_pen' => ['required', 'numeric', 'min:0'],
            'opening_cash_bob' => ['nullable', 'numeric', 'min:0'],
            'opening_cash_usd' => ['nullable', 'numeric', 'min:0'],
            'opening_gold'     => ['required', 'numeric', 'min:0'],
        ]);

        $today = Carbon::today()->toDateString();

        // Evitar duplicado de caja
        if (CashRegister::where('date', $today)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'La caja ya fue abierta hoy.',
                'data'    => null
            ], 409);
        }

        $cashRegister = CashRegister::create([
            'date'             => $today,
            'opening_cash_pen' => $validated['opening_cash_pen'],
            'opening_cash_bob' => $validated['opening_cash_bob'] ?? 0,
            'opening_cash_usd' => $validated['opening_cash_usd'] ?? 0,
            'opening_gold'     => $validated['opening_gold'],
            'balance_pen'      => $validated['opening_cash_pen'],
            'balance_bob'      => $validated['opening_cash_bob'] ?? 0,
            'balance_usd'      => $validated['opening_cash_usd'] ?? 0,
            'opened_by'        => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Caja abierta correctamente.',
            'data'    => $cashRegister,
        ], 201);
    }

    /**
     * 🔹 Cerrar la caja del día actual.
     */
    public function cerrar(Request $request)
    {
        $validated = $request->validate([
            'closing_cash_pen' => ['required', 'numeric', 'min:0'],
            'closing_cash_bob' => ['nullable', 'numeric', 'min:0'],
            'closing_cash_usd' => ['nullable', 'numeric', 'min:0'],
            'closing_gold'     => ['required', 'numeric', 'min:0'],
        ]);

        $cashRegister = CashRegister::where('date', Carbon::today()->toDateString())->first();

        if (!$cashRegister) {
            return response()->json([
                'success' => false,
                'message' => 'No hay caja abierta para hoy.',
                'data'    => null
            ], 404);
        }

        if (!is_null($cashRegister->closing_cash_pen)) {
            return response()->json([
                'success' => false,
                'message' => 'La caja ya fue cerrada.',
                'data'    => $cashRegister
            ], 409);
        }

        $cashRegister->update([
            'closing_cash_pen' => $validated['closing_cash_pen'],
            'closing_cash_bob' => $validated['closing_cash_bob'] ?? 0,
            'closing_cash_usd' => $validated['closing_cash_usd'] ?? 0,
            'closing_gold'     => $validated['closing_gold'],
            'closed_by'        => $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Caja cerrada correctamente.',
            'data'    => $cashRegister,
        ], 200);
    }

    /**
     * 🔹 Obtener la caja del día actual.
     */
    public function actual()
    {
        $cashRegister = CashRegister::where('date', Carbon::today()->toDateString())->first();

        if (!$cashRegister) {
            return response()->json([
                'success' => false,
                'message' => 'No hay caja abierta para hoy.',
                'data'    => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Caja actual encontrada.',
            'data'    => $cashRegister,
        ], 200);
    }

            /**
     * Listar cierres de caja
     */
    public function cierres()
    {
        try {
            $closures = CashRegister::with(['openedBy','closedBy'])
                ->orderByDesc('date')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Lista de cierres obtenida correctamente.',
                'data'    => $closures,
            ]);

        } catch (\Throwable $th) {
            Log::error("Error al listar cierres: ".$th->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener cierres.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }


    /**
     * Resumen completo del día
     */
   public function resumenDia($date)
{
    try {
        $cashRegister = CashRegister::with(['openedBy','closedBy'])
            ->where('date', $date)
            ->first();

        if (!$cashRegister) {
            return response()->json([
                'success' => false,
                'message' => 'No existe caja para esa fecha.',
            ], 404);
        }

        $transactions = Transaction::with('user')
            ->where('cash_register_id', $cashRegister->id)
            ->orderBy('created_at')
            ->get();

        $totals = [
            'total_pen'   => $transactions->sum('total_pen'),
            'total_usd'   => $transactions->sum('total_usd'),
            'total_bob'   => $transactions->sum('total_bob'),
            'total_grams' => $transactions->sum('grams'),
            'count'       => $transactions->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Detalle del cierre obtenido.',
            'data' => [
                'cash_register' => $cashRegister,
                'transactions'  => $transactions,
                'totals'        => $totals,
            ]
        ]);

    } catch (\Throwable $th) {
        Log::error("Error en summaryDetail: " . $th->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Error al obtener detalle del cierre.',
            'error'   => $th->getMessage(),
        ], 500);
    }
}

  /**
     * Resumen completo del día
     */


}
