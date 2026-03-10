<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\CashRegister;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    /** ============================================================
     * 🔹 LISTAR TODAS LAS TRANSACCIONES DEL DÍA (EMPRESA)
     * ============================================================ */
    public function index()
    {
        $transactions = Transaction::select(
                'id',
                'client_name',
                'grams',
                'moneda',
                'total_pen',
                'total_usd',
                'total_bob',
                'created_at'
            )
            ->where('company_id', Auth::user()->company_id)
            ->whereDate('created_at', Carbon::today('America/Lima'))
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Lista de transacciones del día',
            'data'    => $transactions,
        ], 200);
    }

    /** ============================================================
     * ➕ CREAR NUEVA TRANSACCIÓN
     * ============================================================ */
    public function store(Request $request)
    {
        try {

            /* =======================
             * VALIDACIÓN
             * ======================= */
            $validated = $request->validate([
                'company_id'            => 'nullable|integer', // se ignora
                'client_name'           => 'nullable|string|max:255',
                'grams'                 => 'required|numeric|min:0.01',
                'purity'                => 'required|numeric|min:0|max:1',
                'discount_percentage'   => 'nullable|numeric|min:0|max:100',
                'price_per_oz'          => 'required|numeric|min:0',
                'exchange_rate_pen_usd' => 'nullable|numeric|min:0.01',
                'moneda'                => 'required|in:PEN,USD,BOB',
                'tipo_venta'            => 'required|in:regular,empresa,0,1',
                'hora'                  => 'nullable|string'
            ]);

            /* =======================
             * CAJA ACTIVA DE LA EMPRESA
             * ======================= */
            $cashRegister = CashRegister::where('company_id', Auth::user()->company_id)
                ->where('status', 'open')
                ->latest('date')
                ->first();

            if (!$cashRegister) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay caja abierta para la empresa.',
                ], 409);
            }

            /* =======================
             * CAMPOS AUTOMÁTICOS
             * ======================= */
            $validated['company_id']       = Auth::user()->company_id;
            $validated['metal_type']       = 'oro';
            $validated['created_by']       = Auth::id();
            $validated['cash_register_id'] = $cashRegister->id;
            $validated['hora']             = $validated['hora']
                ?? now('America/Lima')->format('H:i:s');

            /* =======================
             * CÁLCULOS BASE (NO TOCADOS)
             * ======================= */
            $grams    = $validated['grams'];
            $purity   = $validated['purity'];
            $priceOz  = $validated['price_per_oz'];
            $discount = $validated['discount_percentage'] ?? 0;
            $exchange = $validated['exchange_rate_pen_usd'] ?? 1;

            $pricePerGramUSD = ($priceOz / 31.1035) * $purity;
            $pricePerGramPEN = $pricePerGramUSD * $exchange;
            $pricePerGramPEN -= $pricePerGramPEN * ($discount / 100);
            $pricePerGramBOB = $pricePerGramPEN;

            /* =======================
             * ASIGNAR PRECIOS
             * ======================= */
            $validated['price_per_gram_usd'] = $pricePerGramUSD;
            $validated['price_per_gram_pen'] = $pricePerGramPEN;
            $validated['price_per_gram_bob'] = $pricePerGramBOB;

            /* =======================
             * TOTALES POR MONEDA
             * ======================= */
            $validated['total_pen'] = null;
            $validated['total_usd'] = null;
            $validated['total_bob'] = null;

            if ($validated['moneda'] === 'PEN') {
                $validated['total_pen'] = $pricePerGramPEN * $grams;
            }

            if ($validated['moneda'] === 'USD') {
                $validated['total_usd'] = $pricePerGramUSD * $grams;
            }

            if ($validated['moneda'] === 'BOB') {
                $validated['total_bob'] = $pricePerGramBOB * $grams;
            }

            /* =======================
             * GUARDAR TRANSACCIÓN
             * ======================= */
            $transaction = Transaction::create($validated);

            /* =======================
             * ACTUALIZAR CAJA
             * (mantengo tu lógica)
             * ======================= */
            if ($validated['moneda'] === 'PEN') {
                $cashRegister->increment('balance_pen', $validated['total_pen']);
            }

            if ($validated['moneda'] === 'USD') {
                $cashRegister->increment('balance_usd', $validated['total_usd']);
            }

            if ($validated['moneda'] === 'BOB') {
                $cashRegister->increment('balance_bob', $validated['total_bob']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Transacción registrada correctamente',
                'data'    => $transaction
            ], 201);

        } catch (\Throwable $e) {

            Log::error('Error al guardar transacción', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la transacción',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /** ============================================================
     * 📆 TRANSACCIONES DEL DÍA (CON RELACIONES – EMPRESA)
     * ============================================================ */
    public function day()
    {
        $startOfDay = Carbon::now('America/Lima')->startOfDay();
        $endOfDay   = Carbon::now('America/Lima')->endOfDay();

        $transactions = Transaction::with(['cashRegister', 'user'])
            ->where('company_id', Auth::user()->company_id)
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Transacciones del día',
            'data'    => $transactions,
        ], 200);
    }
}