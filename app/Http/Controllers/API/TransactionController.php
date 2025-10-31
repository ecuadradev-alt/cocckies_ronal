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
     * 🔹 LISTAR TODAS LAS TRANSACCIONES DEL DÍA
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
     * 🔹 CREAR NUEVA TRANSACCIÓN
     * ============================================================ */
    public function store(Request $request)
    {
        try {
            // ✅ Validar datos de entrada
            $validated = $request->validate([
                'grams'                => 'required|numeric|min:0.01',
                'purity'               => 'required|numeric|min:0|max:1',
                'discount_percentage'  => 'nullable|numeric|min:0|max:100',
                'price_per_gram_pen'   => 'nullable|numeric|min:0',
                'price_per_gram_usd'   => 'nullable|numeric|min:0',
                'price_per_gram_bob'   => 'nullable|numeric|min:0',
                'price_per_oz'         => 'required|numeric|min:0',
                'total_pen'            => 'nullable|numeric|min:0',
                'total_usd'            => 'nullable|numeric|min:0',
                'total_bob'            => 'nullable|numeric|min:0',
                'exchange_rate_pen_usd'=> 'required|numeric|min:0.01',
                'moneda'               => 'required|string|in:PEN,BOB,USD',
                'tipo_venta'           => 'nullable|string|in:regular,empresa,0,1',
                'client_name'          => 'nullable|string|max:255',
                'hora'                 => 'nullable|string',
            ]);

            // ============================================================
            // 🔧 Valores por defecto y automáticos
            // ============================================================
            $validated['metal_type']       = 'oro';
            $validated['type']             = 'venta'; // o compra si lo manejas desde el front
            $validated['created_by']       = Auth::id();
            $validated['cash_register_id'] = Auth::user()->cash_register_id ?? 1;
            $validated['hora']             = $validated['hora'] ?? now('America/Lima')->format('H:i:s');

            // ============================================================
            // 💰 Calcular precios y totales si no vienen del frontend
            // ============================================================
            $grams       = $validated['grams'];
            $priceOz     = $validated['price_per_oz'];
            $exchange    = $validated['exchange_rate_pen_usd'];
            $purity      = $validated['purity'];
            $discountPct = $validated['discount_percentage'] ?? 0;

            // Cálculos base
            $pricePerGramUSD = ($priceOz / 31.1035) * $purity;
            $pricePerGramPEN = $pricePerGramUSD * $exchange * (1 - $discountPct / 100);
            $pricePerGramBOB = $pricePerGramPEN; // 1 BOB = 1 PEN

            $totalPEN = $pricePerGramPEN * $grams;
            $totalUSD = $totalPEN / $exchange;
            $totalBOB = $totalPEN; // Igual por paridad

            // Aplicar valores calculados
            $validated['price_per_gram_pen'] = $validated['price_per_gram_pen'] ?? $pricePerGramPEN;
            $validated['price_per_gram_usd'] = $validated['price_per_gram_usd'] ?? $pricePerGramUSD;
            $validated['price_per_gram_bob'] = $validated['price_per_gram_bob'] ?? $pricePerGramBOB;
            $validated['total_pen']          = $validated['total_pen'] ?? $totalPEN;
            $validated['total_usd']          = $validated['total_usd'] ?? $totalUSD;
            $validated['total_bob']          = $validated['total_bob'] ?? $totalBOB;

            // ============================================================
            // 💾 Guardar la transacción
            // ============================================================
            $transaction = Transaction::create($validated);

            // ============================================================
            // 🧮 Actualizar balances de caja
            // ============================================================
            $cashRegister = CashRegister::find($validated['cash_register_id']);
            if ($cashRegister) {
                match ($validated['moneda']) {
                    'PEN' => $cashRegister->increment('balance_pen', $validated['total_pen']),
                    'BOB' => $cashRegister->increment('balance_bob', $validated['total_bob']),
                    'USD' => $cashRegister->increment('balance_usd', $validated['total_usd']),
                    default => null,
                };
            }

            // ============================================================
            // 📦 Respuesta OK
            // ============================================================
            return response()->json([
                'success' => true,
                'message' => 'Transacción registrada correctamente',
                'data'    => $transaction->load(['cashRegister', 'user']),
            ], 201);

        } catch (\Throwable $e) {
            Log::error('❌ Error al registrar transacción', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la transacción',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /** ============================================================
     * 🔹 OBTENER TRANSACCIONES DEL DÍA (con relaciones)
     * ============================================================ */
    public function day()
    {
        $startOfDay = Carbon::now('America/Lima')->startOfDay();
        $endOfDay   = Carbon::now('America/Lima')->endOfDay();

        $transactions = Transaction::with(['cashRegister', 'user'])
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
