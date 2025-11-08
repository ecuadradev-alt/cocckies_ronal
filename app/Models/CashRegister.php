<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'opening_cash_pen',
        'opening_cash_bob',
        'opening_cash_usd',
        'opening_gold',
        'balance_pen',
        'balance_bob',
        'balance_usd',
        'closing_cash_pen',
        'closing_cash_bob',
        'closing_cash_usd',
        'closing_gold',
        'status',
        'notes',
        'opened_by',
        'closed_by',
    ];

    protected $casts = [
        'date' => 'date',
        'opening_cash_pen' => 'decimal:2',
        'opening_cash_bob' => 'decimal:2',
        'opening_cash_usd' => 'decimal:2',
        'opening_gold' => 'decimal:3',
        'closing_cash_pen' => 'decimal:2',
        'closing_cash_bob' => 'decimal:2',
        'closing_cash_usd' => 'decimal:2',
        'closing_gold' => 'decimal:3',
        'balance_pen' => 'decimal:2',
        'balance_bob' => 'decimal:2',
        'balance_usd' => 'decimal:2',
    ];

    // 🔹 Relación con transacciones
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'cash_register_id');
    }

    // 🔹 Verificar si la caja está abierta
    public function isOpen(): bool
    {
        return $this->status === 'abierta';
    }

    // 🔹 Cerrar la caja
    public function closeRegister(array $closingData = []): void
    {
        $this->update(array_merge($closingData, [
            'status' => 'cerrada',
            'closed_by' => auth()->id(),
        ]));
    }

    // 🔹 Aplicar transacción a los saldos
    public function applyTransaction(Transaction $transaction)
    {
        if (!$this->isOpen()) {
            return; // Evita actualizar caja cerrada
        }

        if ($transaction->moneda === 'PEN') {
            $this->balance_pen += $transaction->total_pen;
        } elseif ($transaction->moneda === 'USD') {
            $this->balance_usd += $transaction->total_usd;
        } elseif ($transaction->moneda === 'BOB') {
            $this->balance_bob += $transaction->total_bob;
        }

        $this->save();
    }
}
